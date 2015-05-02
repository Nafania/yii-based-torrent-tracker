<?php

class m150502_093251_HOTFIX_20150502_add_country_code extends CDbMigration
{
	public function up()
	{
        $this->execute('ALTER TABLE delete187F3 ADD COLUMN country_code VARCHAR(4) NOT NULL');
	}

	public function down()
	{
        $this->execute('ALTER TABLE delete187F3 DROP COLUMN country_code');
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