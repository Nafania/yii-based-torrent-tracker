<?php

class m130811_185838_addNameToSocials extends CDbMigration {
	public function up () {
		$this->addColumn('userSocialAccounts', 'name', 'varchar(255) NOT NULL');
	}

	public function down () {
		echo "m130811_185838_addNameToSocials does not support migration down.\n";
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