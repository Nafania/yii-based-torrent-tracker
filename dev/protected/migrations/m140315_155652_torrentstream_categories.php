<?php

class m140315_155652_torrentstream_categories extends CDbMigration
{
	public function safeUp()
	{
        $this->createTable('torrentstream_categories', [
            'id_torrentstream_category' => 'INT(10) AUTO_INCREMENT PRIMARY KEY',
            'fk_category' => 'INT(10) UNSIGNED NOT NULL',
            'title' => 'VARCHAR(255) NOT NULL'
        ]);
        $this->addForeignKey('fk_category', 'torrentstream_categories', 'fk_category', 'categories', 'id', 'CASCADE', 'CASCADE');
	}

	public function safeDown()
	{
        $this->dropTable('torrentstream_categories');
	}
}