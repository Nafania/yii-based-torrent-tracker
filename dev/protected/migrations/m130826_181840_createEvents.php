<?php

class m130826_181840_createEvents extends CDbMigration {
	public function safeUp () {
		$this->createTable('events',
			array(
			     'id' => 'INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY',
			     'title'  => 'varchar(255) not null',
			     'text'   => 'text not null',
			     'url'    => 'varchar(255) not null',
			     'ctime'  => 'int(11) not null',
			     'uId'    => 'int(10) not null',
			     'unread' => 'tinyint(1) not null',
			));
	}

	public function safeDown () {
		echo "m130826_181840_createEvents does not support migration down.\n";
		return false;
	}

}