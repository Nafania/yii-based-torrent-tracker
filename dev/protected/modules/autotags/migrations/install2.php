<?php
class install extends CDbMigration {
	public function safeUp () {

		$this->execute('CREATE TABLE auto_tag (
			id_auto_tag INT AUTO_INCREMENT PRIMARY KEY,
			fk_tag int unsigned not null,
			fk_category int unsigned not null,
			FOREIGN KEY (fk_tag) REFERENCES tags(id),
			FOREIGN KEY (fk_category) REFERENCES categories(id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8');
	}

	public function safeDown () {
		$this->delete('auto_tag');
	}
}