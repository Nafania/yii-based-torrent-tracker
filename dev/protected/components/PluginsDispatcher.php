<?php
class PluginsDispatcher extends CApplicationComponent {
	/**
	 * @var array
	 */
	public $plugins = array();

	/**
	 * @var array
	 */
	static $_modules = array();

	/**
	 * init method
	 */
	public function init () {
		parent::init();

		Yii::trace('PluginsDispatcher init', 'PluginsDispatcher');
	}

	/**
	 * Collect all modules
	 */
	private static function _getModulesList () {
		$modulesDir = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR;
		$handle = opendir($modulesDir);

		while ( false !== ($file = readdir($handle)) ) {
			if ( $file != "." && $file != ".." && is_dir($modulesDir . $file) ) {
				$configPath = $modulesDir . $file . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';
				if ( file_exists($configPath) ) {
					$config = new CConfiguration($modulesDir . $file . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php');
					self::$_modules[$file] = $config->toArray();
				}
			}
		}
		closedir($handle);
	}

	/**
	 * Load all modules and register them
	 */
	public static function load () {
		Yii::trace('PluginsDispatcher load started', 'PluginsDispatcher');

		self::_getModulesList();

		foreach ( self::$_modules AS $moduleTitle => $data ) {
			if ( empty($data['class']) ) {
				$data['class'] = 'application.modules.' . $moduleTitle . '.' . ucfirst($moduleTitle) . 'Module';
			}

			Yii::import($data['class']);
			$moduleName = explode('.', $data['class']);
			$moduleName = array_pop($moduleName);

			Yii::trace('PluginsDispatcher getting module ' . $moduleTitle . ' data', 'PluginsDispatcher');

			if ( method_exists($moduleName, 'register') ) {
				Yii::trace('PluginsDispatcher register module ' . $moduleTitle, 'PluginsDispatcher');
				$moduleName::register();

			}
			else {
				Yii::trace('PluginsDispatcher ignore module ' . $moduleTitle, 'PluginsDispatcher');
			}
		}
		Yii::app()->setModules(self::$_modules);

		Yii::app()->getMessages()->basePath = Yii::getPathOfAlias('application.messages');

		Yii::trace('PluginsDispatcher load finished', 'PluginsDispatcher');
	}

	/**
	 * Register behavior for future use
	 *
	 * @param string $modelName
	 * @param        $behavior
	 */
	public function registerBehavior ( $modelName, $behavior ) {
		Yii::trace('PluginsDispatcher register behavior ' . key($behavior) . ' for ' . $modelName, 'PluginsDispatcher');

		$this->plugins['behaviors'][$modelName] = CMap::mergeArray($this->plugins['behaviors'][$modelName], $behavior);
	}

	/**
	 * Collect behaviors for model and return them as array
	 *
	 * @param $component
	 *
	 * @return array|IBehavior
	 */
	public function loadBehaviors ( $component ) {
		if ( is_object($component) ) {
			$componentName = get_class($component);
		}
		elseif ( is_string($component) ) {
			$componentName = $component;
		}
		else {
			return array();
		}

		if ( empty($this->plugins['behaviors'][$componentName]) ) {
			return array();
		}

		$behaviors = $this->plugins['behaviors'][$componentName];

		Yii::trace('PluginsDispatcher attach behaviors ' . implode(', ',
				array_keys($behaviors)) . ' for ' . $componentName,
			'PluginsDispatcher');

		return $behaviors;
	}

	/**
	 * Add model to list of yiiadmin module
	 *
	 * @param string $modelPath
	 */
	public function addAdminModel ( $modelPath ) {
		Yii::trace('PluginsDispatcher load admin model ' . $modelPath, 'PluginsDispatcher');

		self::$_modules['yiiadmin']['registerModels'][] = $modelPath;
	}

	/**
	 * Add module to list of yiiadmin module
	 *
	 * @param string $moduleName
	 * @param string $category
	 */
	public function addAdminModule ( $moduleName, $category = 'modules' ) {
		if ( isset(self::$_modules[$moduleName]) ) {
			Yii::trace('PluginsDispatcher load admin module ' . $moduleName, 'PluginsDispatcher');
			self::$_modules['yiiadmin']['registerModules'][$category][$moduleName] = self::$_modules[$moduleName];
		}
	}

	/**
	 * Register application components
	 *
	 * @param array $components
	 */
	public function registerApplicationComponents ( array $components ) {
		foreach ( $components AS $name => $config ) {
			Yii::app()->setComponent($name, $config);
		}
	}

	/**
	 * Register event for future use
	 *
	 * @param string $modelName     model name
	 * @param string $eventName     event name
	 * @param array  $handler       CComponent
	 */
	public function registerEvent ( $modelName, $eventName, $handler ) {
		Yii::trace('PluginsDispatcher register event for ' . $modelName,
			'PluginsDispatcher');

		$this->plugins['events'][$modelName][$eventName][] = $handler;
	}

	/**
	 * Load event data and run it
	 *
	 * @param CComponent $component component to attach events
	 */
	public function loadEvents ( CComponent $component ) {
		$componentName = get_class($component);

		if ( empty($this->plugins['events'][$componentName]) ) {
			return;
		}

		foreach ( $this->plugins['events'][$componentName] AS $eventName => $handlers ) {

			foreach ( $handlers AS $handler ) {
				Yii::trace('PluginsDispatcher attach handler for ' . $eventName,
					'PluginsDispatcher');

				$component->attachEventHandler($eventName, $handler);
			}

			break;
		}
	}

	/**
	 * Load relations
	 *
	 * @param CComponent $component component to attach events
	 *
	 * @return array of relations
	 */
	public function loadRelations ( CComponent $component ) {
		$componentName = get_class($component);

		$relations = array();

		if ( empty($this->plugins['relations'][$componentName]) ) {
			return $relations;
		}

		foreach ( $this->plugins['relations'][$componentName] AS $relationName => $relationData ) {
			list($relationData, $importPath) = $relationData;

			if ( strpos($importPath, ',') !== false ) {
				$paths = explode(',', $importPath);
				foreach ( $paths AS $path ) {
					Yii::import(trim($path));
				}
			}
			else {
				Yii::import($importPath);
			}
			$relations[$relationName] = $relationData;
		}

		return $relations;
	}

	public function addRelations ( $componentName, $relationName, $relationData, $importPath ) {
		$this->plugins['relations'][$componentName][$relationName] = array(
			$relationData,
			$importPath
		);
	}

	/**
	 * Load url rules from module and add it to app rules
	 *
	 * @param array $rules
	 * @param bool  $append
	 */
	public function addUrlRules ( $rules, $append = true ) {
		Yii::trace('PluginsDispatcher add url rules ' . implode(', ', $rules),
			'PluginsDispatcher');

		Yii::app()->getUrlManager()->addRules($rules, $append);
	}

	/**
	 * Adds model rules
	 *
	 * @param string $modelName
	 * @param array  $rules
	 */
	public function addModelRules ( $modelName ) {
		$rules = func_get_args();
		unset($rules[0]);

		foreach ( $rules AS $rule ) {
			Yii::trace('PluginsDispatcher add model rules ' . implode(', ', $rule) . ' to ' . $modelName,
				'PluginsDispatcher');

			$this->plugins['modelRules'][$modelName][] = $rule;
		}
	}

	/**
	 * @param CModel $model
	 *
	 * @return array
	 */
	public function loadModelRules ( CModel $model ) {
		Yii::trace('PluginsDispatcher load model rules for ' . get_class($model),
			'PluginsDispatcher');

		$modelName = get_class($model);

		$modelRules = array();

		if ( empty($this->plugins['modelRules'][$modelName]) ) {
			return $modelRules;
		}

		foreach ( $this->plugins['modelRules'][$modelName] AS $key => $rule ) {
			$modelRules[] = $rule;
		}

		return $modelRules;
	}

	/**
	 * Register application components
	 *
	 * @param array $components
	 */
	public function setImport ( array $import ) {
		Yii::app()->setImport($import);
	}


}