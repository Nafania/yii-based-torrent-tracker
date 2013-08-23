<?php

class m130823_185838_setup extends CDbMigration {
	public function safeUp () {
		$this->insert('AuthItem',
			array(
			     'name'        => 'blogs.default.index',
			     'type'        => CAuthItem::TYPE_OPERATION,
			     'description' => 'Просмотр списка блогов',
			     'bizrule'     => '',
			     'data'        => 'N;'
			));
		$this->insert('AuthItem',
			array(
			     'name'        => 'blogs.default.view',
			     'type'        => CAuthItem::TYPE_OPERATION,
			     'description' => 'Просмотр одного блога',
			     'bizrule'     => '',
			     'data'        => 'N;'
			));
		$this->insert('AuthItem',
			array(
			     'name'        => 'blogs.post.view',
			     'type'        => CAuthItem::TYPE_OPERATION,
			     'description' => 'Просмотр записи из блога',
			     'bizrule'     => '',
			     'data'        => 'N;'
			));
		$this->insert('AuthItem',
			array(
			     'name'        => 'blogs.default.my',
			     'type'        => CAuthItem::TYPE_OPERATION,
			     'description' => 'Просмотр своих блогов',
			     'bizrule'     => '',
			     'data'        => 'N;'
			));
		$this->insert('AuthItem',
			array(
			     'name'        => 'blogs.default.create',
			     'type'        => CAuthItem::TYPE_OPERATION,
			     'description' => 'Создание блога',
			     'bizrule'     => '',
			     'data'        => 'N;'
			));
		$this->insert('AuthItem',
			array(
			     'name'        => 'blogs.post.tagsSuggest',
			     'type'        => CAuthItem::TYPE_OPERATION,
			     'description' => 'Ajax подсказки тегов для записи в блоге',
			     'bizrule'     => '',
			     'data'        => 'N;'
			));
		/**
		 * Create operations
		 */

		$this->insert('AuthItem',
			array(
			     'name'        => 'createPostInOwnBlogTask',
			     'type'        => CAuthItem::TYPE_TASK,
			     'description' => 'Создание записи в своем блоге (задача)',
			     'bizrule'     => '',
			     'data'        => 'N;'
			));
		$this->insert('AuthItem',
			array(
			     'name'        => 'createPostInBlogTask',
			     'type'        => CAuthItem::TYPE_TASK,
			     'description' => 'Создание записи в любом блоге (задача)',
			     'bizrule'     => '',
			     'data'        => 'N;'
			));
		$this->insert('AuthItem',
			array(
			     'name'        => 'createPostInBlog',
			     'type'        => CAuthItem::TYPE_OPERATION,
			     'description' => 'Создание записи в любом блоге',
			     'bizrule'     => '',
			     'data'        => 'N;'
			));
		$this->insert('AuthItem',
			array(
			     'name'        => 'createPostInOwnBlog',
			     'type'        => CAuthItem::TYPE_OPERATION,
			     'description' => 'Создание записи в своем блоге',
			     'bizrule'     => 'return $params[\'ownerId\'] == Yii::app()->getUser()->getId();',
			     'data'        => 'N;'
			));
		$this->insert('AuthItem',
			array(
			     'name'        => 'blogs.post.create',
			     'type'        => CAuthItem::TYPE_OPERATION,
			     'description' => 'Создание записи',
			     'bizrule'     => '',
			     'data'        => 'N;'
			));
		$this->insert('AuthItemChild',
			array(
			     'parent' => 'createPostInOwnBlogTask',
			     'child'   => 'blogs.post.create'
			));
		$this->insert('AuthItemChild',
			array(
			     'parent' => 'createPostInOwnBlogTask',
			     'child'   => 'createPostInOwnBlog'
			));

		$this->insert('AuthItemChild',
			array(
			     'parent' => 'createPostInBlogTask',
			     'child'   => 'blogs.post.create'
			));
		$this->insert('AuthItemChild',
			array(
			     'parent' => 'createPostInBlogTask',
			     'child'   => 'createPostInBlog'
			));

		/**
		 * Update operations
		 */


		$this->insert('AuthItem',
			array(
			     'name'        => 'deletePostInOwnBlogTask',
			     'type'        => CAuthItem::TYPE_TASK,
			     'description' => 'Удаление записи в своем блоге (задача)',
			     'bizrule'     => '',
			     'data'        => 'N;'
			));
		$this->insert('AuthItem',
			array(
			     'name'        => 'deletePostInBlogTask',
			     'type'        => CAuthItem::TYPE_TASK,
			     'description' => 'Удаление записи в любом блоге (задача)',
			     'bizrule'     => '',
			     'data'        => 'N;'
			));
		$this->insert('AuthItem',
			array(
			     'name'        => 'deletePostInBlog',
			     'type'        => CAuthItem::TYPE_OPERATION,
			     'description' => 'Удаление записи в любом блоге',
			     'bizrule'     => '',
			     'data'        => 'N;'
			));
		$this->insert('AuthItem',
			array(
			     'name'        => 'deletePostInOwnBlog',
			     'type'        => CAuthItem::TYPE_OPERATION,
			     'description' => 'Удаление записи в своем блоге',
			     'bizrule'     => 'return $params[\'ownerId\'] == Yii::app()->getUser()->getId();',
			     'data'        => 'N;'
			));
		$this->insert('AuthItem',
			array(
			     'name'        => 'blogs.post.delete',
			     'type'        => CAuthItem::TYPE_OPERATION,
			     'description' => 'Удаление записи',
			     'bizrule'     => '',
			     'data'        => 'N;'
			));
		$this->insert('AuthItem',
			array(
			     'name'        => 'blogs.post.update',
			     'type'        => CAuthItem::TYPE_OPERATION,
			     'description' => 'Редактирование записи',
			     'bizrule'     => '',
			     'data'        => 'N;'
			));
		$this->insert('AuthItemChild',
			array(
			     'parent' => 'deletePostInOwnBlogTask',
			     'child'   => 'blogs.post.delete'
			));
		$this->insert('AuthItemChild',
			array(
			     'parent' => 'deletePostInOwnBlogTask',
			     'child'   => 'deletePostInOwnBlog'
			));

		$this->insert('AuthItemChild',
			array(
			     'parent' => 'deletePostInBlogTask',
			     'child'   => 'blogs.post.delete'
			));
		$this->insert('AuthItemChild',
			array(
			     'parent' => 'deletePostInBlogTask',
			     'child'   => 'deletePostInBlog'
			));

		/**
		 * Update operations
		 */

		$this->insert('AuthItem',
			array(
			     'name'        => 'updatePostInOwnBlogTask',
			     'type'        => CAuthItem::TYPE_TASK,
			     'description' => 'Редактирование записи в своем блоге (задача)',
			     'bizrule'     => '',
			     'data'        => 'N;'
			));
		$this->insert('AuthItem',
			array(
			     'name'        => 'updatePostInBlogTask',
			     'type'        => CAuthItem::TYPE_TASK,
			     'description' => 'Редактирование записи в любом блоге (задача)',
			     'bizrule'     => '',
			     'data'        => 'N;'
			));
		$this->insert('AuthItem',
			array(
			     'name'        => 'updatePostInBlog',
			     'type'        => CAuthItem::TYPE_OPERATION,
			     'description' => 'Редактирование записи в любом блоге',
			     'bizrule'     => '',
			     'data'        => 'N;'
			));
		$this->insert('AuthItem',
			array(
			     'name'        => 'updatePostInOwnBlog',
			     'type'        => CAuthItem::TYPE_OPERATION,
			     'description' => 'Редактирование записи в своем блоге',
			     'bizrule'     => 'return $params[\'ownerId\'] == Yii::app()->getUser()->getId();',
			     'data'        => 'N;'
			));
		$this->insert('AuthItem',
			array(
			     'name'        => 'update.post.delete',
			     'type'        => CAuthItem::TYPE_OPERATION,
			     'description' => 'Редактирование записи',
			     'bizrule'     => '',
			     'data'        => 'N;'
			));
		$this->insert('AuthItemChild',
			array(
			     'parent' => 'updatePostInOwnBlogTask',
			     'child'   => 'blogs.post.update'
			));
		$this->insert('AuthItemChild',
			array(
			     'parent' => 'updatePostInOwnBlogTask',
			     'child'   => 'updatePostInOwnBlog'
			));

		$this->insert('AuthItemChild',
			array(
			     'parent' => 'updatePostInBlogTask',
			     'child'   => 'blogs.post.update'
			));
		$this->insert('AuthItemChild',
			array(
			     'parent' => 'updatePostInBlogTask',
			     'child'   => 'updatePostInBlog'
			));
	}

	public function down () {
		echo "blogsInstall does not support migration down.\n";
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