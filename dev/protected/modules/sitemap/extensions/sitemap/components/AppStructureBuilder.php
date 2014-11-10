<?php
//namespace yiiExtensions\sitemap\components;

//use CAction;
//use CController;
//use CException;
//use CFileHelper;
//use CWebApplication;
//use CWebModule;
//use ReflectionClass;
//use Yii;

/**
 * Class AppStructureBuilder
 *
 * Provides ability to build hierarchy of application modules, their controllers and actions belonging to controllers.
 * Each hierarchy node is represented as instance or subclass of AppStructureComponent.
 *
 * @see     AppStructureComponent
 *
 * @author  Alexander Bolshakov <a.bolshakov.coder@gmail.com>
 * @package yiiExtensions\sitemap\components
 */
class AppStructureBuilder {
	/**
	 * @var string Class of created structure components. It must be instance or subclass of AppStructureComponent.
	 */
	public $appStructureComponentClass;

	/**
	 * Creates hierarchy of modules, their controllers and actions.
	 *
	 * @param integer $only Which type of components should be the last in the hierarchy.
	 *
	 * @todo Implement inclusion submodules to hier
	 *
	 * @see  AppStructureComponent::getType()
	 * @return array|AppStructureComponent[]
	 */
	public function buildStructure ( $only = null ) {
		$structure = array();

		// add application controllers
		if ( $only !== AppStructureComponent::TYPE_MODULE ) {
			$structure = array_merge($structure,
				$this->getControllersForModule(Yii::app(),
					($only !== AppStructureComponent::TYPE_CONTROLLER)));
		}
		// add application modules
		foreach ( Yii::app()->modules as $moduleID => $moduleConfig ) {
			if ( $moduleID == 'gii' ) {
				continue;
			}
			if ( ($module = Yii::app()->getModule($moduleID)) instanceof CWebModule ) {
				$structureComponent = Yii::createComponent($this->appStructureComponentClass, $module);
				if ( $structureComponent->validate() ) {
					// gonna deep to controllers too
					if ( $only !== AppStructureComponent::TYPE_MODULE ) {
						$structureComponent->setChildren($this->getControllersForModule($module),
							// gonna deep to actions too
							($only !== AppStructureComponent::TYPE_CONTROLLER));
					}
					$structure[] = $structureComponent;
				}
			}
		}
//CVarDumper::dump($structure,4,true);exit();
		return $structure;
	}

	/**
	 * Creates list of controllers, that belong to given module.
	 * Searches between controller files at controllerPath and at controllerMap listing.
	 * Controllers, listed at controllerMap will override the ones which have a mathing file if their ID's are equal.
	 *
	 * @see CWebApplication::createController()
	 * @see CWebApplication::controllerMap
	 *
	 * @param CWebApplication|CWebModule $module        Instance of module.
	 * @param bool                       $deepToActions Whether to fetch information about actions of each controller.
	 *
	 * @return array|AppStructureComponent[]
	 */
	public function getControllersForModule ( $module, $deepToActions = false ) {
		if ( !$module instanceof CWebModule && !$module instanceof CWebApplication ) {
			throw new CException('Instance of module that provide controllers must be type of CWebModule or CWebApplication');
		}
		$controllers = array();
		//var_dump($module->getControllerPath());
		// find controllers at files
		if ( file_exists($module->getControllerPath()) ) {
			/** @var $module CWebModule|CWebApplication */
			$controllersFiles = CFileHelper::findFiles($module->getControllerPath(),
				array('fileTypes' => array('php')));
			//var_dump($controllersFiles);
			foreach ( $controllersFiles as $filePath ) {
				$pathInfo = pathinfo($filePath);
				$className = $pathInfo['filename'];

				if ( strrpos($className, 'Controller', -1) !== false ) {
					if ( $module->controllerNamespace !== null ) {
						$className = $module->controllerNamespace . '\\' . $className;
					}
					//var_dump($className);
					if ( !class_exists($className, false) ) {
						require($filePath);
					}
					if ( class_exists($className, false) && is_subclass_of($className, 'CController') ) {
						$controllerID = lcfirst(str_replace('Controller', '', $className));
						$class = new ReflectionClass($className);
						if ( $class->isAbstract() ) {
							return array();
						}
						$controllerInstance = new $className($controllerID, $module);
						$structureComponent = Yii::createComponent($this->appStructureComponentClass,
							$controllerInstance);
						if ( $structureComponent->validate() ) {
							if ( $deepToActions ) {
								$structureComponent->setChildren($this->getActionsForController($controllerInstance));
							}
							$controllers[get_class($module) . '_' . $controllerID] = $structureComponent;
						}
						unset($controllerInstance);
					}
				}
			}

		}

		// find controllers at controllerMap
		foreach ( $module->controllerMap as $controllerID => $controllerConfig ) {
			$controllerInstance = Yii::createComponent($controllerConfig, $controllerID, $module);
			$structureComponent = new $this->appStructureComponentClass($controllerInstance);
			if ( $structureComponent->validate() ) {
				if ( $deepToActions ) {
					$structureComponent->setChildren($this->getActionsForController($controllerInstance));
				}
				$controllers[get_class($module) . '_' . $controllerID] = $structureComponent;
			}
		}

		return $controllers;
	}

	/**
	 * Creates list of actions that belong to given controller.
	 * Searches between methods of controller (inline actions) and listing at CController::actions().
	 * Inline actions will owerride the ones listed at CController::actions() if their ID's are equal.
	 *
	 * @see CController::createAction()
	 * @see CController::actions()
	 *
	 * @param CController $controller
	 *
	 * @return array|AppStructureComponent[]
	 */
	public function getActionsForController ( CController $controller ) {
		// actionID => actionID to exclude duplicates
		$actions = array();

		// search at CController::actions()
		$actionsIDsFromMethod = array_keys($controller->actions());
		if ( !$actionsIDsFromMethod ) {
			return array();
		}
		$actionIDs = array_combine($actionsIDsFromMethod, $actionsIDsFromMethod);

		// search at methods
		$reflection = new ReflectionClass($controller);
		foreach ( $reflection->getMethods() as $method ) {
			$methodName = $method->getName();
			if ( strpos($methodName, 'action') === 0 && strcasecmp($methodName, 's') ) {
				$methodName = lcfirst(str_replace('action', '', $methodName));
				$actionIDs[$methodName] = $methodName;
			}
		}

		foreach ( $actionIDs as $actionID ) {
			$actionInstance = $controller->createAction($actionID);
			if ( $actionInstance instanceof CAction ) {
				$structureComponent = Yii::createComponent($this->appStructureComponentClass, $actionInstance);
				if ( $structureComponent->validate() ) {
					$actions[] = $structureComponent;
				}
			}
		}

		return $actions;
	}
}