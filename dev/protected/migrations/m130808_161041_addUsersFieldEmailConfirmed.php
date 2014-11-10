<?php

class m130808_161041_addUsersFieldEmailConfirmed extends CDbMigration
{
	public function up()
	{
		$this->addColumn('users', 'emailConfirmed', 'tinyint(1) not null');
	}

	public function down()
	{
		echo "m130808_161041_addUsersFieldEmailConfirmed does not support migration down.\n";
		return false;
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