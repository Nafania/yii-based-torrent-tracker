<?php

class m141110_133616_TASK_2_add_auth_items_about_my_torrents extends CDbMigration
{
	public function up()
	{
        $this->execute("INSERT INTO `AuthItem` (`name`, `type`, `description`, `bizrule`, `data`) VALUES ('torrents.default.my', 0, 'Просмотр своих торрентов', '', 'N;')");
        $this->execute("INSERT INTO `AuthItemChild` (`parent`, `child`) VALUES ('registered', 'torrents.default.my')");
	}

	public function down()
	{
        $this->execute('DELETE FROM AuthItemChild WHERE child LIKE \'torrents.default.my\'');
        $this->execute('DELETE FROM AuthItem WHERE name LIKE \'torrents.default.my\'');
	}
}