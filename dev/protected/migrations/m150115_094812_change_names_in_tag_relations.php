<?php

class m150115_094812_change_names_in_tag_relations extends CDbMigration
{
	public function up()
	{
		$this->execute('UPDATE tagRelations SET modelName = \'modules_torrents_models_Torrent\' WHERE modelName = \'Torrent\'');
		$this->execute('UPDATE tagRelations SET modelName = \'modules_torrents_models_TorrentGroup\' WHERE modelName = \'TorrentGroup\'');
	}

	public function down()
	{
		$this->execute('UPDATE tagRelations SET modelName = \'Torrent\' WHERE modelName = \'modules_torrents_models_Torrent\'');
		$this->execute('UPDATE tagRelations SET modelName = \'TorrentGroup\' WHERE modelName = \'modules_torrents_models_TorrentGroup\'');
	}
}