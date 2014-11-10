<?php

class m130826_172605_createSubscriptions extends CDbMigration
{
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
		$this->createTable('subscriptions', array(
		                                       'modelId' => 'int(10) not null',
		                                       'modelName' => 'varchar(45) not null',
		                                       'uId' => 'int(10) not null',
		                                       'ctime' => 'int(11) not null',
		                                    ));
		$this->createIndex('unique', 'subscriptions', 'modelId,modelName,uId', true);

		$this->insert('AuthItem',
			array(
			     'name'        => 'subscriptions.default.add',
			     'type'        => CAuthItem::TYPE_OPERATION,
			     'description' => 'Добавление подписки',
			     'bizrule'     => '',
			     'data'        => 'N;'
			));

		$this->insert('AuthItem',
			array(
			     'name'        => 'subscriptions.default.delete',
			     'type'        => CAuthItem::TYPE_OPERATION,
			     'description' => 'Удаление подписки',
			     'bizrule'     => '',
			     'data'        => 'N;'
			));
	}

	public function safeDown()
	{
		echo "m130826_172605_createSubscriptions does not support migration down.\n";
		return false;
	}

}