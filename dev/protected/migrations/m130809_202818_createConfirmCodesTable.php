<?php

class m130809_202818_createConfirmCodesTable extends CDbMigration {
	public function up () {
		$this->createTable('userConfirmCodes',
			array(
			     'uId'         => 'int(10) NOT NULL',
			     'confirmCode' => 'varchar(32) NOT NULL',
			));
	}

	public function down () {
		echo "m130809_202818_createConfirmCodesTable does not support migration down.\n";
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