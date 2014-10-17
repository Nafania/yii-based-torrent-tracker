<?php

class m141017_060946_add_permissions_about_subscriptions extends CDbMigration
{
	public function up()
	{
        $this->execute("INSERT INTO `AuthItem` (`name`, `type`, `description`, `bizrule`, `data`) VALUES
        ('subscriptions.default.index', 0, 'Просмотр подписок', '', 'N;')");

        $this->execute("INSERT INTO `AuthItemChild` (`parent`, `child`) VALUES
        ('registered', 'subscriptions.default.index')
        ");
	}

	public function down()
	{
        $this->execute('DELETE FROM AuthItemChild WHERE child LIKE \'subscriptions.default.index\'');
        $this->execute('DELETE FROM AuthItem WHERE name LIKE \'subscriptions.default.index\'');
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}