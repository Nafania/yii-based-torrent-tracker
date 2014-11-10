<?php

class m130809_202804_createUserSocialTable extends CDbMigration {
	public function up () {
		$this->createTable('userSocialAccounts',
			array(
			     'uId'     => 'int(10) NOT NULL',
			     'id'      => 'varchar(255) NOT NULL',
			     'service' => 'varchar(255) NOT NULL',
			));
	}

	public function down () {
		echo "m130809_202804_createUserSocialTable does not support migration down.\n";
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