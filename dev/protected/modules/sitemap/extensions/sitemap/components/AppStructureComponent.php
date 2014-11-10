<?php
//namespace yiiExtensions\sitemap\components;

/*use CAction;
use CController;
use CWebModule;
use Yii;*/

/**
 * Class AppStructureComponent
 *
 * Provides abstraction for working with modules, controllers and actions.
 *
 * @author  Alexander Bolshakov <a.bolshakov.coder@gmail.com>
 * @package yiiExtensions\sitemap\components
 */
class AppStructureComponent
{
    const TYPE_MODULE     = 0;
    const TYPE_CONTROLLER = 1;
    const TYPE_ACTION     = 2;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var array|AppStructureComponent[]
     */
    protected $children = array();

    /**
     * @var integer
     */
    protected $type;

    /**
     * @var CWebModule|CController|CAction
     */
    protected $instance;

    /**
     * @var string
     */
    protected $route;

    /**
     * @param CWebModule|CController|CAction $instance
     */
    final public function __construct($instance)
    {
        if ($instance instanceof CWebModule) {
            $this->initFromModule($instance);
        } elseif ($instance instanceof CController) {
            $this->initFromController($instance);
        } elseif ($instance instanceof CAction) {
            $this->initFromAction($instance);
        } else {
            throw new \CException('Incorrect component type');
        }
    }

    /**
     * Override this method if you need to check whether current component is suitable for addition to output structure.
     *
     * @return bool
     */
    public function validate()
    {
        return true;
    }

    /**
     * @param CWebModule $instance
     */
    public function initFromModule($instance)
    {
        $this->instance    = $instance;
        $this->type        = self::TYPE_MODULE;
        $this->name        = $instance->getName();
        $this->description = $instance->getDescription();
        $this->route       = $instance->getId();
    }

    /**
     * @param CController $instance
     */
    public function initFromController($instance)
    {
        $this->instance = $instance;
        $this->type     = self::TYPE_CONTROLLER;
        $this->name     = $instance->getId();
        if ($instance->getModule() == Yii::app()) {
            $this->route = $instance->getId();
        } else {
            $this->route = $instance->getRoute();
        }
    }

    /**
     * @param CAction $instance
     */
    public function initFromAction($instance)
    {
        $this->instance = $instance;
        $this->type     = self::TYPE_ACTION;
        $this->name     = $instance->getId();
        if ($instance->getController()->getModule() == Yii::app()) {
            $this->route = $instance->getController()->getId() . '/' . $instance->getId();
        } else {
            $this->route = $instance->getController()->getRoute() . '/' . $instance->getId();
        }
    }

    /**
     * @param array|AppStructureComponent[] $children
     */
    public function setChildren(array $children)
    {
        $this->children = $children;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return array|AppStructureComponent[] Child components. For modules those are controllers and submodules,
     * for controllers those are actions. Actions have no child components.
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return int Type of component.
     * @see AppStructureComponent::TYPE_MODULE
     * @see AppStructureComponent::TYPE_CONTROLLER
     * @see AppStructureComponent::TYPE_ACTION
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return CWebModule|CController|CAction Instance of component.
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * @return string If it is possible - moduleID/controllerID/actionID, otherwise only part of this construction.
     */
    public function getRoute()
    {
        return $this->route;
    }
}