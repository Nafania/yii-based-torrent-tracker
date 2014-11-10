<?php

class m141109_170554_TASK_1_change_review_rating_text_to_params extends CDbMigration
{
	public function up()
	{
        $this->execute('ALTER TABLE reviews CHANGE COLUMN ratingText params TEXT DEFAULT NULL');
        $this->execute('UPDATE reviews SET params = NULL');
	}

	public function down()
	{
        $this->execute('ALTER TABLE reviews CHANGE COLUMN params ratingText TEXT NOT NULL');
	}
}