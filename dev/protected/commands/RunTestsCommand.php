<?php
class RunTestsCommand extends CConsoleCommand {
	private $_modules;

	public function run ( $args ) {
		if ( !function_exists('proc_open') ) {
			throw new CException('Function "proc_open" not found');
		}

		$onlyModule = (isset($args[0]) ? $args[0] : '');
		$testType = (isset($args[1]) ? $args[1] : '');
		$testName = (isset($args[2]) ? $args[2] : '');

		$this->_getModulesList();

		foreach ( $this->_modules AS $moduleName => $configPath ) {
			if ( $onlyModule && $moduleName != $onlyModule ) {
				continue;
			}
			echo "executing " . ( $testName ? $testName : 'all' ) . " tests from {$moduleName} module\n\n";
			$descriptorSpec = array(
				0 => array(
					'pipe',
					'r'
				),
				// stdin is a pipe that the child will read from
				1 => array(
					'pipe',
					'w'
				),
				// stdout is a pipe that the child will write to
			);
			flush();
			$process = proc_open('php ' . Yii::getPathOfAlias('application.vendor.tests.codecept') . '.phar --config=' . $configPath . ' run' . ( $testType ? ' ' . $testType : '' ) . ( $testName ? ' ' . $testName : '' ),
				$descriptorSpec,
				$pipes);
			if ( is_resource($process) ) {
				while ( $s = fgets($pipes[1]) ) {
					echo $s;
					flush();
				}
			}
			echo "tests from {$moduleName} module done\n\n";
		}

		return '0';
	}

	private function _getModulesList () {
		$modulesDir = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR;
		$handle = opendir($modulesDir);

		while ( false !== ($file = readdir($handle)) ) {
			if ( $file != '.' && $file != '..' && is_dir($modulesDir . $file) ) {
				$configPath = $modulesDir . $file . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'codeception.yml';
				if ( file_exists($configPath) ) {
					$this->_modules[$file] = $configPath;
				}
			}
		}
		closedir($handle);
	}
}