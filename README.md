##It's a beta version, only for dev purposes, but many features a fully working
###Here is some features:
- User accounts (login, social networks login, settings etc)
- Torrents: creating group of torrents based on category settings. Uploading torrent to groups, editing them etc. EAV structure for description.
- Categories, category attributes with validators.
- Yii-auth module
- Chat, based on node.js and socket.io
- Comments for each entity
- Groups (group blogs)
- Blogs (single blogs)
- Reports system
- Ability to save search settings
- Auto generated sitemaps
- Subscription and events system (can used with socket.io or without it if not present)
- Administration panel (not fully working)
- Special modification of xbtt tracker used as default tracker
- Sphinx can be used as search engine (if present)


###Install:
- Create mysql database called 'yii-torrent'
- Import sql initialSetup.sql from protected/migrations
- Apply all migrations
- Edit protected/config/local/config.php if needed.
- You're done.