<?php

class m150115_094812_change_names_in_tag_relations extends CDbMigration
{
	public function safeUp()
	{
		$this->execute('SET FOREIGN_KEY_CHECKS=0');
		$this->execute('UPDATE tagRelations SET modelName = \'' . \modules\torrents\models\Torrent::model()->resolveClassName() . '\' WHERE modelName = \'Torrent\'');
		$this->execute('UPDATE tagRelations SET modelName = \'' . \modules\torrents\models\TorrentGroup::model()->resolveClassName() . '\' WHERE modelName = \'TorrentGroup\'');
		$this->execute('SET FOREIGN_KEY_CHECKS=1');
	}

	public function safeDown()
	{
		$this->execute('UPDATE tagRelations SET modelName = \'Torrent\' WHERE modelName = \'' . \modules\torrents\models\Torrent::model()->resolveClassName() . '\'');
		$this->execute('UPDATE tagRelations SET modelName = \'TorrentGroup\' WHERE modelName = \'' . \modules\torrents\models\TorrentGroup::model()->resolveClassName() . '\'');
	}
}