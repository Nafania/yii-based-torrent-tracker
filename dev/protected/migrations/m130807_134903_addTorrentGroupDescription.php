<?php

class m130807_134903_addTorrentGroupDescription extends CDbMigration
{
	public function up()
	{
		$this->addColumn('torrentGroups', 'description', 'text not null');
	}

	public function down()
	{
		echo "m130807_134903_addTorrentGroupDescription does not support migration down.\n";
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