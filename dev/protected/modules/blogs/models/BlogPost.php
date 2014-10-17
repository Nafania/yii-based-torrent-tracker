<?php
namespace modules\blogs\models;

use Yii;
use CDbCriteria;
use CActiveDataProvider;
use CSort;
use CMap;
use CHtml;
use CHtmlPurifier;

/**
 * This is the model class for table "blogposts".
 *
 * The followings are the available columns in table 'blogposts':
 * @property integer $id
 * @property string  $title
 * @property string  $text
 * @property integer $blogId
 * @property integer $ownerId
 * @property integer $ctime
 * @property integer $mtime
 * @property integer $hidden
 * @property integer $pinned
 * @property Blog    blog
 */
class BlogPost extends \EActiveRecord implements \ChangesInterface, \WebInterface
{
    const HIDDEN = 1;
    const NOT_HIDDEN = 0;

    public $cacheTime = 3600;

    /**
     * Returns the static model of the specified AR class.
     *
     * @param string $className active record class name.
     *
     * @return BlogPost the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'blogPosts';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        $purifier = new CHtmlPurifier();
        $purifier->options = array('HTML.SafeIframe' => true, 'URI.SafeIframeRegexp' => '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%'); // Передача конфига в формате массива

        return array(
            array(
                'title, text',
                'required'
            ),
            array(
                'text',
                'filter',
                'filter' => array(
                    $purifier,
                    'purify'
                )
            ),
            array(
                'title',
                'length',
                'max' => 255
            ),
            array(
                'hidden',
                'boolean',
            ),
            array(
                'id, title, text, blogId, ownerId, groupId, disableComments, private, hided, ctime',
                'safe',
                'on' => 'adminSearch'
            ),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array(
                'id, title, text, blogId, ownerId, groupId, disableComments, private, hided, ctime',
                'safe',
                'on' => 'search'
            ),
        );
    }

    public function relations()
    {
        return CMap::mergeArray(parent::relations(),
            array(
                'blog' => array(
                    self::BELONGS_TO,
                    'modules\blogs\models\Blog',
                    'blogId'
                ),
                'user' => array(
                    self::BELONGS_TO,
                    'User',
                    'ownerId'
                ),
            ));
    }

    public function behaviors()
    {
        return CMap::mergeArray(parent::behaviors(),
            array(
                'SlugBehavior' => array(
                    'class' => 'application.extensions.SlugBehavior.aii.behaviors.SlugBehavior',
                    'sourceAttribute' => 'title',
                    'slugAttribute' => 'slug',
                    'mode' => 'translit',
                ),
            ));
    }


    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'title' => Yii::t('blogsModule.common', 'Название'),
            'text' => Yii::t('blogsModule.common', 'Текст'),
            'blogId' => 'Blog',
            'ownerId' => 'Owner',
            'ctime' => Yii::t('blogsModule.common', 'Дата создания'),
            'mtime' => Yii::t('blogsModule.common', 'Дата изменения'),
            'hidden' => Yii::t('blogsModule.common', 'Скрытая запись'),
            'rating' => Yii::t('blogsModule.common', 'Рейтинг'),
            'commentsCount' => Yii::t('blogsModule.common', 'Кол-во комментариев'),
            'lastCommentCtime' => Yii::t('blogsModule.common', 'Дата последнего комментария'),
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $alias = $this->getTableAlias();

        $criteria = new CDbCriteria;

        $criteria->compare($alias . '.id', $this->id);
        $criteria->compare($alias . '.blogId', $this->blogId);
        $criteria->compare($alias . '.ownerId', $this->ownerId);
        $criteria->compare($alias . '.ctime', $this->ctime);

        $sort = Yii::app()->getRequest()->getParam('sort');
        /**
         * TODO: убрать все это в поведения
         */
        /**
         * подключаем таблицу счетчиков только если запрошена сортировка по счетчикам
         */
        if (strpos($sort, 'commentsCount') !== false) {
            $criteria->select = 't.*, cc.count AS commentsCount';
            $criteria->join = 'LEFT JOIN {{commentCounts}} cc ON ( cc.modelName = \'' . $this->resolveClassName() . '\' AND cc.modelId = t.id)';
            //$criteria->group = 't.id';
        }
        /**
         * подключаем таблицу рейтингов
         */
        if (strpos($sort, 'rating') !== false) {
            $criteria->select = 't.*, r.rating AS rating';
            $criteria->join .= 'LEFT JOIN {{ratings}} r ON ( r.modelName = \'' . $this->resolveClassName() . '\' AND r.modelId = t.id)';
        }

        if (strpos($sort, 'lastCommentCtime') !== false) {
            $criteria->select = 't.*, MAX(comments.ctime) AS lastCommentCtime';
            $criteria->join .= 'LEFT JOIN {{comments}} comments ON ( comments.modelName = \'' . $this->resolveClassName() . '\' AND comments.modelId = t.id)';
            $criteria->group .= 'comments.modelId';
        }

        $sort = new CSort($this);
        //$sort->applyOrder($alias . '.pinned DESC');
        $sort->defaultOrder = $alias . '.pinned DESC, ' . $alias . '.ctime DESC';
        $sort->attributes = array(
            '*',
            'commentsCount' => array(
                'asc' => 'commentsCount ASC',
                'desc' => 'commentsCount DESC',
                'default' => 'desc',
            ),
            'rating' => array(
                'asc' => 'rating ASC',
                'desc' => 'rating DESC',
                'default' => 'desc',
            ),
            'lastCommentCtime' => array(
                'asc' => 'lastCommentCtime ASC',
                'desc' => 'lastCommentCtime DESC',
                'default' => 'desc',
            ),
        );
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'sort' => $sort,
            'pagination' => ['pageVar' => 'page'],
        ));
    }

    public function afterFind()
    {
        parent::afterFind();

        if (php_sapi_name() == 'cli') {
            return true;
        }

        /**
         * если запись скрытая
         */
        if ($this->hidden == self::HIDDEN) {
            $group = $this->blog->group;

            /**
             * если это запись группы
             */
            if ($group) {
                /**
                 * а текущий юзер не член этой группы, то записи как бэ нет
                 */
                if (!\Group::checkJoin($group)) {
                    throw new \CHttpException(404);
                }
            } /**
             * или если, запись не группы, то проверим права на чтение скрытых записей
             */
            elseif (!Yii::app()->getUser()->checkAccess('canViewOwnHiddenPost',
                array(
                    'ownerId' => $this->ownerId
                ))
            ) {
                throw new \CHttpException(404);
            }
        }

        return true;
    }

    public function onlyVisible()
    {
        $alias = $this->getTableAlias();

        $criteria = new CDbCriteria();
        $criteria->condition = $alias . '.hidden = :hidden';
        $criteria->params = array(
            'hidden' => self::NOT_HIDDEN
        );
        $this->getDbCriteria()->mergeWith($criteria);
        return $this;
    }

    public function forBlog($blogId)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('blogId = :blogId');
        $criteria->params[':blogId'] = $blogId;

        $this->getDbCriteria()->mergeWith($criteria);
        return $this;
    }

    public function forGroup($groupId)
    {
        $criteria = new CDbCriteria();
        $criteria->with = 'blog';
        $criteria->addCondition('blog.groupId = :groupId');
        $criteria->params[':groupId'] = $groupId;

        $this->getDbCriteria()->mergeWith($criteria);
        return $this;
    }


    protected function beforeSave()
    {
        if (parent::beforeSave()) {
            if ($this->getIsNewRecord()) {
                $this->ctime = time();
                $this->ownerId = Yii::app()->getUser()->getId();
            } else {
                $this->mtime = time();
            }

            return true;
        }

        return false;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle($encode = true)
    {
        return ($encode ? CHtml::encode($this->title) : $this->title);
    }

    public function getText()
    {
        return $this->text;
    }

    public function getUrl()
    {
        if ($this->blog->groupId) {
            return array(
                '/blogs/post/view',
                'id' => $this->getId(),
                'title' => $this->getSlugTitle(),
                'groupId' => $this->blog->groupId,
                'groupTitle' => $this->blog->group->getSlugTitle(),
            );
        } else {
            return array(
                '/blogs/post/view',
                'id' => $this->getId(),
                'title' => $this->getSlugTitle(),
                'blogId' => $this->blogId,
                'blogTitle' => $this->blog->getSlugTitle(),
            );
        }
    }

    public function getCtime($format = false)
    {
        if ($format) {
            return date($format, $this->ctime);
        }
        return $this->ctime;
    }


    public function getChangesText()
    {
        return Yii::t('commentsModule.common',
            'Добавлен новый комментарий к вашей записи "{title}"',
            array(
                '{title}' => $this->getTitle()
            ));
    }

    public function getChangesTitle()
    {
        return Yii::t('commentsModule.common', 'Новый комменатрий к вашей записи');
    }

    public function getMtime()
    {
        return $this->mtime;
    }

    public function getChangesIcon()
    {
        return 'comment';
    }

    //search functions
    public function searchWithTags($tags = false)
    {
        if ($tags) {
            $this->taggedWith($tags);
        }
    }

    public function searchWithText($search = '')
    {
        if ($search) {
            $criteria = new CDbCriteria();
            $criteria->with = 'torrents';
            $this->getDbCriteria()->mergeWith($criteria);

            $this->withEavAttributes(array($search));
        }
    }


    public function getPluralNames () {
        return [
            Yii::t('blogsModule.common', 'Пост в блоге'),
            Yii::t('blogsModule.common', 'Посты в блоге'),
        ];
    }
}