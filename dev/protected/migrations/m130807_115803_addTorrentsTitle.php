<?php

class m130807_115803_addTorrentsTitle extends CDbMigration {
	public function up () {
		$this->addColumn('torrents', 'title', 'varchar(255) not null');
	}

	public function down () {
		echo "m130807_115803_addTorrentsTitle does not support migration down.\n";
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