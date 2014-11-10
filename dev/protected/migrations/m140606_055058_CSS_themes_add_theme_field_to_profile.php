<?php

class m140606_055058_CSS_themes_add_theme_field_to_profile extends CDbMigration
{

	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
        $this->addColumn('userProfiles', 'theme', 'VARCHAR(255) NOT NULL DEFAULT "default"');
	}

	public function safeDown()
	{
        $this->dropColumn('userProfiles', 'theme');
	}
}