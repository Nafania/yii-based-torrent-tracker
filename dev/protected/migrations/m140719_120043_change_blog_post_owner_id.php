<?php

class m140719_120043_change_blog_post_owner_id extends CDbMigration
{
	public function safeUp()
	{
        $this->execute('ALTER TABLE blogPosts MODIFY ownerId INT(11) UNSIGNED DEFAULT NULL');
	}

	public function safeDown()
	{
        $this->execute('ALTER TABLE blogPosts MODIFY ownerId INT(11) UNSIGNED NOT NULL');
	}
}