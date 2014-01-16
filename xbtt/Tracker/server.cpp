#include "stdafx.h"
#include "server.h"

#include <boost/foreach.hpp>
#include <boost/format.hpp>
#include <sql/sql_query.h>
#include <iostream>
#include <sstream>
#include <signal.h>
#include <bt_misc.h>
#include <bt_strings.h>
#include <stream_int.h>
#include "transaction.h"
#include <math.h>

static volatile bool g_sig_term = false;

Cserver::Cserver(Cdatabase& database, const std::string& table_prefix, bool use_sql, const std::string& conf_file):
	m_database(database)
{
	m_fid_end = 0;

	for (int i = 0; i < 8; i++)
		m_secret = m_secret << 8 ^ rand();
	m_conf_file = conf_file;
	m_table_prefix = table_prefix;
	m_time = ::time(NULL);
	m_use_sql = use_sql;
}

int Cserver::run()
{
	read_config();
	if (test_sql())
		return 1;
	if (m_epoll.create(1 << 10) == -1)
	{
		std::cerr << "epoll_create failed" << std::endl;
		return 1;
	}
	t_tcp_sockets lt;
	t_udp_sockets lu;
	BOOST_FOREACH(Cconfig::t_listen_ipas::const_reference j, m_config.m_listen_ipas)
	{
		BOOST_FOREACH(Cconfig::t_listen_ports::const_reference i, m_config.m_listen_ports)
		{
			Csocket l;
			if (l.open(SOCK_STREAM) == INVALID_SOCKET)
				std::cerr << "socket failed: " << Csocket::error2a(WSAGetLastError()) << std::endl;
			else if (l.setsockopt(SOL_SOCKET, SO_REUSEADDR, true),
				l.bind(j, htons(i)))
				std::cerr << "bind failed: " << Csocket::error2a(WSAGetLastError()) << std::endl;
			else if (l.listen())
				std::cerr << "listen failed: " << Csocket::error2a(WSAGetLastError()) << std::endl;
			else
			{
#ifdef SO_ACCEPTFILTER
				accept_filter_arg afa;
				bzero(&afa, sizeof(afa));
				strcpy(afa.af_name, "httpready");
				if (l.setsockopt(SOL_SOCKET, SO_ACCEPTFILTER, &afa, sizeof(afa)))
					std::cerr << "setsockopt failed: " << Csocket::error2a(WSAGetLastError()) << std::endl;
#elif TCP_DEFER_ACCEPT
				if (l.setsockopt(IPPROTO_TCP, TCP_DEFER_ACCEPT, true))
					std::cerr << "setsockopt failed: " << Csocket::error2a(WSAGetLastError()) << std::endl;
#endif
				lt.push_back(Ctcp_listen_socket(this, l));
				if (!m_epoll.ctl(EPOLL_CTL_ADD, l, EPOLLIN | EPOLLOUT | EPOLLPRI | EPOLLERR | EPOLLHUP, &lt.back()))
					continue;
			}
			return 1;
		}
		BOOST_FOREACH(Cconfig::t_listen_ports::const_reference i, m_config.m_listen_ports)
		{
			Csocket l;
			if (l.open(SOCK_DGRAM) == INVALID_SOCKET)
				std::cerr << "socket failed: " << Csocket::error2a(WSAGetLastError()) << std::endl;
			else if (l.setsockopt(SOL_SOCKET, SO_REUSEADDR, true),
				l.bind(j, htons(i)))
				std::cerr << "bind failed: " << Csocket::error2a(WSAGetLastError()) << std::endl;
			else
			{
				lu.push_back(Cudp_listen_socket(this, l));
				if (!m_epoll.ctl(EPOLL_CTL_ADD, l, EPOLLIN | EPOLLPRI | EPOLLERR | EPOLLHUP, &lu.back()))
					continue;
			}
			return 1;
		}
	}
	clean_up();
	read_db_files();
	read_db_users();
	write_db_files();
	write_db_users();
#ifndef WIN32
	if (m_config.m_daemon)
	{
#if 1
		if (daemon(true, false))
			std::cerr << "daemon failed" << std::endl;
#else
		switch (fork())
		{
		case -1:
			std::cerr << "fork failed" << std::endl;
			break;
		case 0:
			break;
		default:
			exit(0);
		}
		if (setsid() == -1)
			std::cerr << "setsid failed" << std::endl;
#endif
		std::ofstream(m_config.m_pid_file.c_str()) << getpid() << std::endl;
		struct sigaction act;
		act.sa_handler = sig_handler;
		sigemptyset(&act.sa_mask);
		act.sa_flags = 0;
		if (sigaction(SIGTERM, &act, NULL))
			std::cerr << "sigaction failed" << std::endl;
		act.sa_handler = SIG_IGN;
		if (sigaction(SIGPIPE, &act, NULL))
			std::cerr << "sigaction failed" << std::endl;
	}
#endif
#ifdef EPOLL
	const int c_events = 64;

	epoll_event events[c_events];
#else
	fd_set fd_read_set;
	fd_set fd_write_set;
	fd_set fd_except_set;
#endif
	while (!g_sig_term)
	{
#ifdef EPOLL
		int r = m_epoll.wait(events, c_events, 5000);
		if (r == -1)
			std::cerr << "epoll_wait failed: " << errno << std::endl;
		else
		{
			int prev_time = m_time;
			m_time = ::time(NULL);
			for (int i = 0; i < r; i++)
				reinterpret_cast<Cclient*>(events[i].data.ptr)->process_events(events[i].events);
			if (m_time == prev_time)
				continue;
			for (t_connections::iterator i = m_connections.begin(); i != m_connections.end(); )
			{
				if (i->run())
					i = m_connections.erase(i);
				else
					i++;
			}
		}
#else
		FD_ZERO(&fd_read_set);
		FD_ZERO(&fd_write_set);
		FD_ZERO(&fd_except_set);
		int n = 0;
		BOOST_FOREACH(t_connections::reference i, m_connections)
		{
			int z = i.pre_select(&fd_read_set, &fd_write_set);
			n = std::max(n, z);
		}
		BOOST_FOREACH(t_tcp_sockets::reference i, lt)
		{
			FD_SET(i.s(), &fd_read_set);
			n = std::max<int>(n, i.s());
		}
		BOOST_FOREACH(t_udp_sockets::reference i, lu)
		{
			FD_SET(i.s(), &fd_read_set);
			n = std::max<int>(n, i.s());
		}
		timeval tv;
		tv.tv_sec = 5;
		tv.tv_usec = 0;
		if (select(n + 1, &fd_read_set, &fd_write_set, &fd_except_set, &tv) == SOCKET_ERROR)
			std::cerr << "select failed: " << Csocket::error2a(WSAGetLastError()) << std::endl;
		else
		{
			m_time = ::time(NULL);
			BOOST_FOREACH(t_tcp_sockets::reference i, lt)
			{
				if (FD_ISSET(i.s(), &fd_read_set))
					accept(i.s());
			}
			BOOST_FOREACH(t_udp_sockets::reference i, lu)
			{
				if (FD_ISSET(i.s(), &fd_read_set))
					Ctransaction(*this, i.s()).recv();
			}
			for (t_connections::iterator i = m_connections.begin(); i != m_connections.end(); )
			{
				if (i->post_select(&fd_read_set, &fd_write_set))
					i = m_connections.erase(i);
				else
					i++;
			}
		}
#endif
		if (time() - m_read_config_time > m_config.m_read_config_interval)
			read_config();
		else if (time() - m_clean_up_time > m_config.m_clean_up_interval)
			clean_up();
		else if (time() - m_read_db_files_time > m_config.m_read_db_interval)
			read_db_files();
		else if (time() - m_read_db_users_time > m_config.m_read_db_interval)
			read_db_users();
		else if (m_config.m_write_db_interval && time() - m_write_db_files_time > m_config.m_write_db_interval)
			write_db_files();
		else if (m_config.m_write_db_interval && time() - m_write_db_users_time > m_config.m_write_db_interval)
			write_db_users();
	}
	write_db_files();
	write_db_users();
	unlink(m_config.m_pid_file.c_str());
	return 0;
}

void Cserver::accept(const Csocket& l)
{
	sockaddr_in a;
	while (1)
	{
		socklen_t cb_a = sizeof(sockaddr_in);
		Csocket s = ::accept(l, reinterpret_cast<sockaddr*>(&a), &cb_a);
		if (s == SOCKET_ERROR)
		{
			if (WSAGetLastError() == WSAECONNABORTED)
				continue;
			if (WSAGetLastError() != WSAEWOULDBLOCK)
				std::cerr << "accept failed: " << Csocket::error2a(WSAGetLastError()) << std::endl;
			break;
		}
		t_deny_from_hosts::const_iterator i = m_deny_from_hosts.lower_bound(ntohl(a.sin_addr.s_addr));
		if (i != m_deny_from_hosts.end() && ntohl(a.sin_addr.s_addr) >= i->second.begin)
		{
			m_stats.rejected_tcp++;
			continue;
		}
		m_stats.accepted_tcp++;
		if (s.blocking(false))
			std::cerr << "ioctlsocket failed: " << Csocket::error2a(WSAGetLastError()) << std::endl;
		std::auto_ptr<Cconnection> connection(new Cconnection(this, s, a));
		connection->process_events(EPOLLIN);
		if (connection->s() != INVALID_SOCKET)
		{
			m_connections.push_back(connection.release());
			m_epoll.ctl(EPOLL_CTL_ADD, m_connections.back().s(), EPOLLIN | EPOLLOUT | EPOLLPRI | EPOLLERR | EPOLLHUP | EPOLLET, &m_connections.back());
		}
	}
}

std::string Cserver::insert_peer(const Ctracker_input& v, bool udp, t_user* user)
{

	if (!m_config.m_offline_message.empty())
		return m_config.m_offline_message;
	if (!m_config.m_anonymous_announce && !user)
		return bts_unregistered_torrent_pass;
	if (!m_config.m_auto_register && !file(v.m_info_hash))
		return bts_unregistered_torrent;
	/*if (v.m_left && user && !user->can_leech)
		return bts_can_not_leech;*/
	t_file& file = m_files[v.m_info_hash];
	if (!file.ctime)
		file.ctime = time();
	/*if (user && ( file.hidden && ( !user->hiddentorrents && user->user_class < 5 ) ) )
		return bts_hidden_torrent;
	if (user && !user->enabled)
		return bts_enabled_user;
	if (user && user->parked)
		return bts_parked_user;	*/		
	t_peers::key_type peer_key(v.m_ipa, user ? user->uid : 0);
	t_peers::iterator i = file.peers.find(peer_key);
	if (i != file.peers.end())
	{
		(i->second.left ? file.leechers : file.seeders)--;
		if (t_user* old_user = find_user_by_uid(i->second.uid))
			(i->second.left ? old_user->incompletes : old_user->completes)--;
	}
	/*else if (v.m_left && user && user->torrents_limit && user->incompletes >= user->torrents_limit)
		return bts_torrents_limit_reached;*/
	if (m_use_sql && user && file.fid)
	{
		long long downloaded = 0;
		long long uploaded = 0;
		long long timespent = 0;
		long long upspeed = 0;
		long long downspeed = 0;
		int self_mtime = time();		
		int completedat = 0;
		
		if (i != file.peers.end()
			&& boost::equals(i->second.peer_id, v.m_peer_id)
			&& v.m_downloaded >= i->second.downloaded
			&& v.m_uploaded >= i->second.uploaded)
		{
			downloaded = v.m_downloaded - i->second.downloaded;
			uploaded = v.m_uploaded - i->second.uploaded;
			timespent = time() - i->second.mtime;
			self_mtime = i->second.mtime;
			if ((downloaded || uploaded) && timespent)
			{
				upspeed = uploaded / timespent;
				downspeed = downloaded / timespent;
			} 			
		}
		
		//active, announced, completed, downloaded, `left`, uploaded, mtime, fid, uid, peer_id, ip, port, started, upspeed, downspeed
		m_files_users_updates_buffer += Csql_query(m_database, "(?,1,?,?,?,?,?,?,?,?,?,?,?,?,?),")
			.p(v.m_event != Ctracker_input::e_stopped)
			.p(v.m_event == Ctracker_input::e_completed)
			.p(downloaded)
			.p(v.m_left)
			.p(uploaded)
			.p(time())
			.p(file.fid)
			.p(user->uid)
			.p(v.m_peer_id)
			.p(ntohl(v.m_ipa))
			.p(ntohs(v.m_port))
			.p(time())
			.p(upspeed)
			.p(downspeed)
			.read();
			
		completedat = ( v.m_event == Ctracker_input::e_completed ? 1 : 0 );
		//torrentid, userid, uploaded, downloaded, last_action, completedat
		m_snatched_updates_buffer += Csql_query(m_database, "(?, ?, ?, ?, ?, ?),")
			.p(file.fid)
			.p(user->uid)
			.p(uploaded)
			.p(downloaded)
			.p(time())
			.p(completedat)
			.read();
			
		/*int current_total_time = 0;
		
		if ( !v.m_left ) {
			current_total_time = int(round( ( ( time() - self_mtime ) / ( ( user->completes ? user->completes : 1 ) ) ) ));
		}
		if ( file.free ) {
			switch ( file.free ) {
				case 1:
					downloaded = 0;
				break;
				case 2:
					downloaded = ceil(downloaded * 0.50);
				break;
				case 4:
					downloaded = ceil(downloaded * 0.75);
				break;
			}		
		}
		
		//user is VIP
		if ( user->user_class == 3 ) {
			uploaded = 0;
			downloaded = 0;
		}*/

		/*if ( downloaded || uploaded ) {
			m_users_updates_buffer += Csql_query(m_database, "(?,?,?),").p(downloaded).p(uploaded).p(user->uid).read();
		}*/
	}
	if (v.m_event == Ctracker_input::e_stopped)
		file.peers.erase(peer_key);
	else
	{
		t_peer& peer = file.peers[peer_key];
		peer.downloaded = v.m_downloaded;
		peer.left = v.m_left;
		std::copy(v.m_peer_id.begin(), v.m_peer_id.end(), peer.peer_id.begin());
		peer.port = v.m_port;
		peer.uid = user ? user->uid : 0;
		peer.uploaded = v.m_uploaded;
		peer.fid = file.fid;
		(peer.left ? file.leechers : file.seeders)++;
		if (user)
			(peer.left ? user->incompletes : user->completes)++;
		peer.mtime = time();
	}
	if (v.m_event == Ctracker_input::e_completed)
		file.downloads++;
	(udp ? m_stats.announced_udp : m_stats.announced_http)++;
	file.dirty = true;
	return "";
}

std::string Cserver::t_file::select_peers(const Ctracker_input& ti) const
{
	if (ti.m_event == Ctracker_input::e_stopped)
		return "";

	typedef std::vector<boost::array<char, 6> > t_candidates;

	t_candidates candidates;
	BOOST_FOREACH(t_peers::const_reference i, peers)
	{
		if (!ti.m_left && !i.second.left)
			continue;
		boost::array<char, 6> v;
		memcpy(&v.front(), &i.first.host_, 4);
		memcpy(&v.front() + 4, &i.second.port, 2);
		candidates.push_back(v);
	}
	size_t c = ti.m_num_want < 0 ? 50 : std::min(ti.m_num_want, 50);
	std::string d;
	d.reserve(300);
	if (candidates.size() > c)
	{
		while (c--)
		{
			int i = rand() % candidates.size();
			d.append(candidates[i].begin(), candidates[i].end());
			candidates[i] = candidates.back();
			candidates.pop_back();
		}
	}
	else
	{
		BOOST_FOREACH(t_candidates::reference i, candidates)
			d.append(i.begin(), i.end());
	}
	return d;
}

Cvirtual_binary Cserver::select_peers(const Ctracker_input& ti) const
{
	const t_file* f = file(ti.m_info_hash);
	if (!f)
		return Cvirtual_binary();
	std::string peers = f->select_peers(ti);
	return Cvirtual_binary((boost::format("d8:completei%de10:incompletei%de8:intervali%de12:min intervali%de5:peers%d:%se")
		% f->seeders % f->leechers % config().m_announce_interval % config().m_announce_interval % peers.size() % peers).str());
}

void Cserver::t_file::clean_up(time_t t, Cserver& server)
{
	for (t_peers::iterator i = peers.begin(); i != peers.end(); )
	{
		if (i->second.mtime < t)
		{
			(i->second.left ? leechers : seeders)--;
			if (t_user* user = server.find_user_by_uid(i->second.uid))
				(i->second.left ? user->incompletes : user->completes)--;
			if (i->second.uid)
				//server.m_files_users_updates_buffer += Csql_query(server.m_database, "(0,0,0,0,-1,0,-1,?,?, 0, 0, 0, 0),").p(fid).p(i->second.uid).read();
				Csql_query(server.m_database, "DELETE FROM peers WHERE fid = ? AND uid = ?").p(fid).p(i->second.uid).execute();
			peers.erase(i++);
			dirty = true;
		}
		else
			i++;
	}
}

void Cserver::clean_up()
{
	BOOST_FOREACH(t_files::reference i, m_files)
		i.second.clean_up(time() - static_cast<int>(1.5 * m_config.m_announce_interval), *this);
	m_clean_up_time = time();
}

static byte* write_compact_int(byte* w, unsigned int v)
{
	if (v >= 0x200000)
	{
		*w++ = 0xe0 | (v >> 24);
		*w++ = v >> 16;
		*w++ = v >> 8;
	}
	else if (v >= 0x4000)
	{
		*w++ = 0xc0 | (v >> 16);
		*w++ = v >> 8;
	}
	else if (v >= 0x80)
		*w++ = 0x80 | (v >> 8);
	*w++ = v;
	return w;
}

Cvirtual_binary Cserver::scrape(const Ctracker_input& ti)
{
	std::string d;
	d += "d5:filesd";
	if (ti.m_info_hashes.empty())
	{
		m_stats.scraped_full++;
		if (ti.m_compact)
		{
			Cvirtual_binary d;
			byte* w = d.write_start(32 * m_files.size() + 1);
			*w++ = 'x';
			BOOST_FOREACH(t_files::reference i, m_files)
			{
				if (!i.second.leechers && !i.second.seeders)
					continue;
				memcpy(w, i.first.data(), i.first.size());
				w += i.first.size();
				w = write_compact_int(w, i.second.seeders);
				w = write_compact_int(w, i.second.leechers);
				w = write_compact_int(w, i.second.downloads);
			}
			d.resize(w - d);
			return d;
		}
		d.reserve(90 * m_files.size());
		BOOST_FOREACH(t_files::reference i, m_files)
		{
			if (i.second.leechers || i.second.seeders)
				d += (boost::format("20:%sd8:completei%de10:downloadedi%de10:incompletei%dee") % i.first % i.second.seeders % i.second.downloads % i.second.leechers).str();
		}
	}
	else
	{
		m_stats.scraped_http++;
		BOOST_FOREACH(Ctracker_input::t_info_hashes::const_reference j, ti.m_info_hashes)
		{
			t_files::const_iterator i = m_files.find(j);
			if (i != m_files.end())
				d += (boost::format("20:%sd8:completei%de10:downloadedi%de10:incompletei%dee") % i->first % i->second.seeders % i->second.downloads % i->second.leechers).str();
		}
	}
	d += "e";
	if (m_config.m_scrape_interval)
		d += (boost::format("5:flagsd20:min_request_intervali%dee") % m_config.m_scrape_interval).str();
	d += "e";
	return Cvirtual_binary(d);
}

void Cserver::read_db_files()
{
	m_read_db_files_time = time();
	if (m_use_sql)
		read_db_files_sql();
	else if (!m_config.m_auto_register)
	{
		std::set<std::string> new_files;
		std::ifstream is("xbt_files.txt");
		std::string s;
		while (getline(is, s))
		{
			s = hex_decode(s);
			if (s.size() != 20)
				continue;
			m_files[s];
			new_files.insert(s);
		}
		for (t_files::iterator i = m_files.begin(); i != m_files.end(); )
		{
			if (new_files.find(i->first) == new_files.end())
				m_files.erase(i++);
			else
				i++;
		}
	}
}

void Cserver::read_db_files_sql()
{
	try
	{
		if (!m_config.m_auto_register)
		{
			Csql_result result = Csql_query(m_database, "select info_hash, fid from ?").p_name(table_name(table_deleted_files)).execute();
			for (Csql_row row; row = result.fetch_row(); )
			{
				t_files::iterator i = m_files.find(row[0].s());
				if (i != m_files.end())
				{
					BOOST_FOREACH(t_peers::reference j, i->second.peers)
					{
						if (t_user* user = find_user_by_uid(j.second.uid))
							(j.second.left ? user->incompletes : user->completes)--;
					}
					m_files.erase(i);
				}
				Csql_query(m_database, "delete from ? where fid = ?").p_name(table_name(table_deleted_files)).p(row[1].i()).execute();
			}
		}
		if (m_files.empty()) {
			m_database.query("update " + table_name(table_files) + " set " + column_name(column_files_leechers) + " = 0, " + column_name(column_files_seeders) + " = 0");
			m_database.query("TRUNCATE TABLE " + table_name(table_files_users));
		}
		else if (m_config.m_auto_register)
			return;
		//Csql_result result = Csql_query(m_database, "select info_hash, ?, ?, ctime, hidden, free, flags from ? where ? >= ? OR flags > 1")
		Csql_result result = Csql_query(m_database, "select info_hash, ?, ?, ctime, tId AS hashChanged from ? LEFT JOIN ? ON id = tId where ? >= ? OR tId IS NOT NULL")
			.p_name(column_name(column_files_completed))
			.p_name(column_name(column_files_fid))
			.p_name(table_name(table_files))
			.p_name(table_name(table_changed_files))
			.p_name(column_name(column_files_fid))
			.p(m_fid_end)
			.execute();
		for (Csql_row row; row = result.fetch_row(); )
		{
			m_fid_end = std::max(m_fid_end, static_cast<int>(row[2].i()) + 1);
			t_file& file = m_files[row[0].s()];
			
			if ( file.hashChanged ) {
					file.dirty = true;					
					file.downloads = row[1].i();
					file.hashChanged = 0;
			}
			else {
					file.dirty = false;	
					file.downloads = row[1].i();
					file.hashChanged = row[4].i();
			}
			file.fid = row[2].i();			
			file.ctime = row[3].i();
			//file.hidden = row[4].i();
			//file.free = row[5].i();
		}
	}
	catch (Cdatabase::exception&)
	{
	}
}

void Cserver::read_db_users()
{
	m_read_db_users_time = time();
	if (!m_use_sql)
		return;
	try
	{
		Csql_query q(m_database, "select ?");
		/*if (m_read_users_can_leech)
			q += ", can_leech";*/
		if (m_read_users_torrent_pass)
			q += ", torrentPass";
		/*if (m_read_users_torrents_limit)
			q += ", torrents_limit";*/
		//q += ", ";
		q += " from ? INNER JOIN ? ON (id = uid)";
		q.p_name(column_name(column_users_uid));
		q.p_name(table_name(table_users));
		q.p_name(table_name(table_userProfiles));
		Csql_result result = q.execute();
		BOOST_FOREACH(t_users::reference i, m_users)
			i.second.marked = true;
		m_users_torrent_passes.clear();
		for (Csql_row row; row = result.fetch_row(); )
		{
			t_user& user = m_users[row[0].i()];
			user.marked = false;
			int c = 0;
			user.uid = row[c++].i();
			//if (m_read_users_can_leech)
			//	user.can_leech = row[c++].i();
			if (m_read_users_torrent_pass)
			{
				if (row[c].size())
					m_users_torrent_passes[row[c].s()] = &user;
				c++;
			}
			//if (m_read_users_torrents_limit)
				/*user.torrents_limit = row[c++].i();				
			user.user_class = row[c++].i();
			user.hiddentorrents = row[c++].i();
			user.parked = row[c++].i();
			user.enabled = row[c++].i();*/
		}
		for (t_users::iterator i = m_users.begin(); i != m_users.end(); )
		{
			if (i->second.marked)
				m_users.erase(i++);
			else
				i++;
		}
	}
	catch (Cdatabase::exception&)
	{
	}
}

void Cserver::write_db_files()
{
	m_write_db_files_time = time();
	if (!m_use_sql)
		return;
	try
	{
		std::string buffer;
		BOOST_FOREACH(t_files::reference i, m_files)
		{
			t_file& file = i.second;
			if (!file.dirty)
				continue;
			if (!file.fid)
			{
				Csql_query(m_database, "insert into ? (info_hash, mtime, ctime) values (?, unix_timestamp(), unix_timestamp())").p_name(table_name(table_files)).p(i.first).execute();
				file.fid = m_database.insert_id();
			}
			buffer += Csql_query(m_database, "(?,?,?,?),").p(file.leechers).p(file.seeders).p(file.downloads).p(file.fid).read();			
			file.dirty = false;
			
			/**
			*	Delete changed hashes
			**/
			Csql_query(m_database, "DELETE FROM ? WHERE tId = ?").p_name(table_name(table_changed_files)).p(file.fid).execute();
		}
		if (!buffer.empty())
		{
			buffer.erase(buffer.size() - 1);
			m_database.query("insert into " + table_name(table_files) + " (" + column_name(column_files_leechers) + ", " + column_name(column_files_seeders) + ", " + column_name(column_files_completed) + ", " + column_name(column_files_fid) + ") values "
				+ buffer
				+ " on duplicate key update"
				+ "  " + column_name(column_files_leechers) + " = values(" + column_name(column_files_leechers) + "),"
				+ "  " + column_name(column_files_seeders) + " = values(" + column_name(column_files_seeders) + "),"
				+ "  " + column_name(column_files_completed) + " = values(" + column_name(column_files_completed) + "),"
				+ "  mtime = unix_timestamp()"
			);			
		}
	}
	catch (Cdatabase::exception&)
	{
	}
}

void Cserver::write_db_users()
{
	m_write_db_users_time = time();
	if (!m_use_sql)
		return;
	if (!m_files_users_updates_buffer.empty())
	{
		m_files_users_updates_buffer.erase(m_files_users_updates_buffer.size() - 1);
		try
		{
			m_database.query("insert into " + table_name(table_files_users) + " (active, announced, completed, downloaded, `left`, uploaded, mtime, fid, uid, peer_id, ip, port, started, upspeed, downspeed) values "
				+ m_files_users_updates_buffer
				+ " on duplicate key update"
				+ "  active = values(active),"
				+ "  announced = announced + values(announced),"
				+ "  completed = completed + values(completed),"
				+ "  downloaded = downloaded + values(downloaded),"
				+ "  `left` = if(values(`left`) = -1, `left`, values(`left`)),"
				+ "  uploaded = uploaded + values(uploaded),"
				+ "  mtime = if(values(mtime) = -1, mtime, values(mtime)),"
				+ "  peer_id = values(peer_id),"
				+ "  ip = values(ip),"
				+ "  port = values(port),"
				+ "  upspeed = values(upspeed),"
				+ "  downspeed = values(downspeed)");
		}
		catch (Cdatabase::exception&)
		{
		}
		m_files_users_updates_buffer.erase();
	}
	/*if (!m_users_updates_buffer.empty())
	{
		m_users_updates_buffer.erase(m_users_updates_buffer.size() - 1);
		try
		{
			m_database.query("insert into " + table_name(table_users) + " (downloaded, uploaded, " + column_name(column_users_uid) + ", total_seed_time) values "
				+ m_users_updates_buffer
				+ " on duplicate key update"
				+ "  downloaded = downloaded + values(downloaded),"
				+ "  uploaded = uploaded + values(uploaded),"
				+ "  total_seed_time = total_seed_time + values(total_seed_time)");
		}
		catch (Cdatabase::exception&)
		{
		}
		m_users_updates_buffer.erase();
	}	*/
	if (!m_snatched_updates_buffer.empty())
	{
		m_snatched_updates_buffer.erase(m_snatched_updates_buffer.size() - 1);
		try
		{
			//tId, uId, uploaded, downloaded, last_action, completeTime
			m_database.query("insert into " + table_name(table_snatched) + " (tId, uId, uploaded, downloaded, mtime, completeTime) values "
				+ m_snatched_updates_buffer
				+ " on duplicate key update"
				+ "  uploaded = uploaded + values(uploaded),"
				+ "  downloaded = downloaded + values(downloaded),"
				+ "  mtime = values(mtime),"
				+ "  completeTime = if(values(completeTime) = 0, completeTime, UNIX_TIMESTAMP(NOW()))");
		}
		catch (Cdatabase::exception&)
		{
		}
		m_snatched_updates_buffer.erase();
	}
}

void Cserver::read_config()
{
	if (m_use_sql)
	{
		try
		{
			Csql_result result = m_database.query("select param, value from " + table_name(table_config));
			Cconfig config;
			for (Csql_row row; row = result.fetch_row(); )
			{
				//if (config.set(row[0].s(), row[1].s()))
				config.set(row[0].s(), row[1].s());
					//std::cerr << "unknown config name: " << row[0].s() << std::endl;
			}
			config.load(m_conf_file);
			m_config = config;
		}
		catch (Cdatabase::exception&)
		{
		}
	}
	else
	{
		Cconfig config;
		if (!config.load(m_conf_file))
			m_config = config;
	}
	if (m_config.m_listen_ipas.empty())
		m_config.m_listen_ipas.insert(htonl(INADDR_ANY));
	if (m_config.m_listen_ports.empty())
		m_config.m_listen_ports.insert(2720);
	m_read_config_time = time();
}

void Cserver::t_file::debug(std::ostream& os) const
{
	BOOST_FOREACH(t_peers::const_reference i, peers)
	{
		os << "<tr><td>" + Csocket::inet_ntoa(i.first.host_)
			<< "<td align=right>" << ntohs(i.second.port)
			<< "<td align=right>" << i.second.uid
			<< "<td align=right>" << i.second.left
			<< "<td align=right>" << ::time(NULL) - i.second.mtime
			<< "<td>" << hex_encode(const_memory_range(i.second.peer_id.begin(), i.second.peer_id.end()));
	}
}

std::string Cserver::debug(const Ctracker_input& ti) const
{
	std::ostringstream os;
	os << "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\"><meta http-equiv=refresh content=60><title>XBT Tracker</title>";
	int leechers = 0;
	int seeders = 0;
	int torrents = 0;
	os << "<table>";
	if (ti.m_info_hash.empty())
	{
		BOOST_FOREACH(t_files::const_reference i, m_files)
		{
			if (!i.second.leechers && !i.second.seeders)
				continue;
			leechers += i.second.leechers;
			seeders += i.second.seeders;
			torrents++;
			os << "<tr><td align=right>" << i.second.fid
				<< "<td><a href=\"?info_hash=" << uri_encode(i.first) << "\">" << hex_encode(i.first) << "</a>"
				<< "<td>" << (i.second.dirty ? '*' : ' ')
				<< "<td align=right>" << i.second.leechers
				<< "<td align=right>" << i.second.seeders;
		}
	}
	else
	{
		t_files::const_iterator i = m_files.find(ti.m_info_hash);
		if (i != m_files.end())
			i->second.debug(os);
	}
	os << "</table>";
	return os.str();
}

std::string Cserver::statistics() const
{
	std::ostringstream os;
	os << "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\"><meta http-equiv=refresh content=60><title>XBT Tracker</title>";
	int leechers = 0;
	int seeders = 0;
	int torrents = 0;
	BOOST_FOREACH(t_files::const_reference i, m_files)
	{
		leechers += i.second.leechers;
		seeders += i.second.seeders;
		torrents += i.second.leechers || i.second.seeders;
	}
	time_t t = time();
	os << "<table><tr><td>leechers<td align=right>" << leechers
		<< "<tr><td>seeders<td align=right>" << seeders
		<< "<tr><td>peers<td align=right>" << leechers + seeders
		<< "<tr><td>torrents<td align=right>" << torrents
		<< "<tr><td>"
		<< "<tr><td>accepted tcp<td align=right>" << m_stats.accepted_tcp
		<< "<tr><td>rejected tcp<td align=right>" << m_stats.rejected_tcp
		<< "<tr><td>announced<td align=right>" << m_stats.announced();
	if (m_stats.announced())
	{
		os << "<tr><td>announced http <td align=right>" << m_stats.announced_http << "<td align=right>" << m_stats.announced_http * 100 / m_stats.announced() << " %"
			<< "<tr><td>announced udp<td align=right>" << m_stats.announced_udp << "<td align=right>" << m_stats.announced_udp * 100 / m_stats.announced() << " %";
	}
	os << "<tr><td>scraped full<td align=right>" << m_stats.scraped_full
		<< "<tr><td>scraped<td align=right>" << m_stats.scraped();
	if (m_stats.scraped())
	{
		os << "<tr><td>scraped http<td align=right>" << m_stats.scraped_http << "<td align=right>" << m_stats.scraped_http * 100 / m_stats.scraped() << " %"
			<< "<tr><td>scraped udp<td align=right>" << m_stats.scraped_udp << "<td align=right>" << m_stats.scraped_udp * 100 / m_stats.scraped() << " %";
	}
	os << "<tr><td>"
		<< "<tr><td>up time<td align=right>" << duration2a(time() - m_stats.start_time)
		<< "<tr><td>"
		<< "<tr><td>anonymous connect<td align=right>" << m_config.m_anonymous_connect
		<< "<tr><td>anonymous announce<td align=right>" << m_config.m_anonymous_announce
		<< "<tr><td>anonymous scrape<td align=right>" << m_config.m_anonymous_scrape
		<< "<tr><td>auto register<td align=right>" << m_config.m_auto_register
		<< "<tr><td>full scrape<td align=right>" << m_config.m_full_scrape
		<< "<tr><td>read config time<td align=right>" << t - m_read_config_time << " / " << m_config.m_read_config_interval
		<< "<tr><td>clean up time<td align=right>" << t - m_clean_up_time << " / " << m_config.m_clean_up_interval
		<< "<tr><td>read db files time<td align=right>" << t - m_read_db_files_time << " / " << m_config.m_read_db_interval;
	if (m_use_sql)
	{
		os << "<tr><td>read db users time<td align=right>" << t - m_read_db_users_time << " / " << m_config.m_read_db_interval
			<< "<tr><td>write db files time<td align=right>" << t - m_write_db_files_time << " / " << m_config.m_write_db_interval
			<< "<tr><td>write db users time<td align=right>" << t - m_write_db_users_time << " / " << m_config.m_write_db_interval;
	}
	os << "</table>";
	return os.str();
}

Cserver::t_user* Cserver::find_user_by_torrent_pass(const std::string& v, const std::string& info_hash)
{
	if (t_user* user = find_user_by_uid(read_int(4, hex_decode(v.substr(0, 8)))))
	{
		if (v.size() >= 8 && Csha1((boost::format("%s %d %s") % m_config.m_torrent_pass_private_key % user->uid % info_hash).str()).read().substr(0, 12) == hex_decode(v.substr(8)))
			return user;
	}
	t_users_torrent_passes::const_iterator i = m_users_torrent_passes.find(v);
	return i == m_users_torrent_passes.end() ? NULL : i->second;
}

Cserver::t_user* Cserver::find_user_by_uid(int v)
{
	t_users::iterator i = m_users.find(v);
	return i == m_users.end() ? NULL : &i->second;
}

void Cserver::sig_handler(int v)
{
	switch (v)
	{
	case SIGTERM:
		g_sig_term = true;
		break;
	}
}

void Cserver::term()
{
	g_sig_term = true;
}

std::string Cserver::column_name(int v) const
{
	switch (v)
	{
	case column_files_completed:
		return m_config.m_column_files_completed;
	case column_files_leechers:
		return m_config.m_column_files_leechers;
	case column_files_seeders:
		return m_config.m_column_files_seeders;
	case column_files_fid:
		return m_config.m_column_files_fid;
	case column_users_uid:
		return m_config.m_column_users_uid;
	}
	assert(false);
	return "";
}

std::string Cserver::table_name(int v) const
{
	switch (v)
	{
	case table_config:
		return m_table_prefix + "config";
	case table_files:
		return m_config.m_table_files.empty() ? m_table_prefix + "files" : m_config.m_table_files;
	case table_files_users:
		return m_config.m_table_files_users.empty() ? m_table_prefix + "files_users" : m_config.m_table_files_users;
	case table_users:
		return m_config.m_table_users.empty() ? m_table_prefix + "users" : m_config.m_table_users;
	case table_userProfiles:
    	return m_config.m_table_userProfiles.empty() ? m_table_prefix + "usersProfiles" : m_config.m_table_userProfiles;
	case table_snatched:
		return m_config.m_table_snatched.empty() ? m_table_prefix + "downloads" : m_config.m_table_snatched;
	case table_deleted_files:
		return m_config.m_table_deleted_files.empty() ? m_table_prefix + "xbt_deleted_hashes" : m_config.m_table_deleted_files;		
	case table_changed_files:
		return m_config.m_table_changed_files.empty() ? m_table_prefix + "xbt_changed_hashes" : m_config.m_table_changed_files;	
	}
	assert(false);
	return "";
}

int Cserver::test_sql()
{
	if (!m_use_sql)
		return 0;
	try
	{
		mysql_get_server_version(&m_database.handle());
		m_database.query("select param, value from " + table_name(table_config));
		m_database.query("select " + column_name(column_files_fid) + ", info_hash, " + column_name(column_files_leechers) + ", " + column_name(column_files_seeders) + ", mtime, ctime from " + table_name(table_files) + " where 0");
		m_database.query("select fid, uid, active, announced, completed, downloaded, `left`, uploaded from " + table_name(table_files_users) + " where 0");
		m_database.query("select " + column_name(column_users_uid) + " from " + table_name(table_users) + " where 0");
		//m_read_users_can_leech = m_database.query("show columns from " + table_name(table_users) + " like 'can_leech'");
		m_read_users_torrent_pass = m_database.query("show columns from " + table_name(table_userProfiles) + " like 'torrentPass'");
		//m_read_users_torrents_limit = m_database.query("show columns from " + table_name(table_users) + " like 'torrents_limit'");
		m_database.query("select tId from " + table_name(table_changed_files) + " where 0");
		return 0;
	}
	catch (Cdatabase::exception&)
	{
	}
	return 1;
}
