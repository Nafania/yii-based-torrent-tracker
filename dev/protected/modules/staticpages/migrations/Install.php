<?php
class Install extends CDbMigration {
	public function safeUp () {
		$this->createTable(
			'staticPages',
			array(
			     'id'        => 'pk',
			     'title'     => 'string not null',
			     'pageTitle' => 'string not null',
			     'content'   => 'text not null',
			     'url'       => 'string not null',
			     'published'       => 'tinyint(1)',
			),
			"ENGINE=InnoDB DEFAULT CHARSET=utf8"
		);
		echo 'Table staticpages created succefully';
	}

	public function safeDown () {
		$this->delete('staticPages');
	}
}