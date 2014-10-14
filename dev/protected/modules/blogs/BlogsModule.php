<?php
namespace modules\blogs;
use Yii;
use CActiveRecord;

class BlogsModule extends \CWebModule {
	public $controllerNamespace = '\modules\blogs\controllers';

	public $backendController = 'blogsBackend';
	public $defaultController = 'default';

	public function init () {
	}


	public static function register () {
        self::_registerComponent();
		self::_addUrlRules();
		self::_setImport();
		self::_addModelsRelations();
		self::_registerBehaviors();

		Yii::app()->pd->addAdminModule('blogs', 'Models management');
	}

    private static function _registerComponent () {
   		Yii::app()->pd->registerApplicationComponents([
                'blogManager' => [
                    'class' => '\modules\blogs\components\BlogManager',
                ]
            ]);
   	}

	private static function _addModelsRelations () {

		Yii::app()->pd->addRelations('User',
			'blogPostsCount',
			array(
			     CActiveRecord::STAT,
			     'modules\blogs\models\BlogPost',
			     'ownerId',
			),
			'application.modules.blogs.models.*');

		Yii::app()->pd->addRelations('User',
			'blogsCount',
			array(
			     CActiveRecord::STAT,
			     'modules\blogs\models\Blog',
			     'ownerId',
			     'condition' => 'groupId IS NULL'
			),
			'application.modules.blogs.models.*');

		Yii::app()->pd->addRelations('Group',
			'blog',
			array(
			     CActiveRecord::HAS_ONE,
			     'modules\blogs\models\Blog',
			     'groupId',
			),
			'application.modules.blogs.models.*');
	}

	private static function _addUrlRules () {
		Yii::app()->pd->addUrlRules(array(
		                                 'yiiadmin/blogs/backend/<action:\w+>/*'           => 'blogs/blogsBackend/<action>',
		                                 'yiiadmin/blogs/backend/*'                        => 'blogs/blogsBackend',

		                                 'blogs/<title>-<id>'                              => 'blogs/default/view',
		                                 'blogs/'                                          => 'blogs/default/index',
		                                 'groups/<groupTitle>-<groupId>/post/<title>-<id>' => 'blogs/post/view',
		                                 'blogs/<blogTitle>-<blogId>/<title>-<id>'    => 'blogs/post/view',
		                                 'blogs/post/<action:\w+>/*'                       => 'blogs/post/<action>',
		                                 'blogs/<action:\w+>/*'                            => 'blogs/default/<action>',
		                                 'blogs/<controller:\w+>/<action:\w+>/*'           => 'blogs/<controller>/<action>',
		                            ));
	}

	private static function _setImport () {
		Yii::app()->pd->setImport(array('application.modules.blogs.models.*'));
	}

	private static function _registerBehaviors () {
		Yii::app()->pd->registerBehavior('Group',
			array(
			     'autoCreateBlog' => array(
				     'class' => 'application.modules.blogs.behaviors.AutoCreateBlogForGroup',
			     ),
			));
	}
}
