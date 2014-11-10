<?php
//namespace yiiExtensions\sitemap;

//use CApplicationComponent;
//use CException;
//use Yii;
//use yiiExtensions\sitemap\components\AppStructureBuilder;
//use yiiExtensions\sitemap\components\AppStructureComponent;

/**
 * Class SitemapComponent
 *
 * @author  Alexander Bolshakov <a.bolshakov.coder@gmail.com>
 * @package yiiExtensions\sitemap
 */

Yii::import('application.modules.sitemap.extensions.sitemap.components.*');
class SitemapComponent extends CApplicationComponent
{

    /**
     * @var string
     */
    public $structureBuilderClass = 'yiiExtensions\sitemap\components\AppStructureBuilder';

    /**
     * @var string
     */
    public $structureComponentClass = 'yiiExtensions\sitemap\components\AppStructureComponent';

    /**
     * @var AppStructureBuilder
     */
    protected $builderInstance;

    /**
     * @var array|AppStructureComponent[]
     */
    protected $structure = array();


    /**
     * @param bool $refresh Whether structure renewal is needed.
     *                      For example, if structure builder class or structure component class was changed.
     *
     * @return array|components\AppStructureComponent[]
     */
    public function getApplicationStructure($refresh = false)
    {
        if ($refresh || empty($this->structure)) {
            $this->initStructureBuilder();
            $this->structure = $this->builderInstance->buildStructure();
        }

        return $this->structure;
    }

    protected function initStructureBuilder()
    {
        if ($this->builderInstance == null) {
            $this->builderInstance = Yii::createComponent($this->structureBuilderClass);
            if (!$this->builderInstance instanceof AppStructureBuilder) {
                throw new CException('Application structure builder must be instance of AppStructureBuilder class');
            }
            $this->builderInstance->appStructureComponentClass = $this->structureComponentClass;
        }
    }
}