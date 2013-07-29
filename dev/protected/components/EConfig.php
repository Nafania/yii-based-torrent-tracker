<?php
/**
 * @author ElisDN <mail@elisdn.ru>
 * @link   http://www.elisdn.ru
 */

class EConfig extends CApplicationComponent {
	public $cache = 0;
	public $dependency = null;

	protected $data = array();

	public function init () {
		$db = $this->getDbConnection();

		$items = $db->createCommand('SELECT * FROM {{config}}')->queryAll();

		foreach ( $items as $item ) {
			if ( $item['param'] ) {
				$this->data[$item['param']] = $item['value'] === '' ? $item['default'] : $item['value'];
			}
		}
	}

	public function get ( $key ) {
		if ( isset($this->data[$key]) ) {
			return $this->data[$key];
		}
		else {
			throw new CException('Undefined parameter ' . $key);
		}
	}

	public function set ( $key, $value ) {
		$db = $this->getDbConnection();
		$command = $db->createCommand('UPDATE {{config}} SET value = :value WHERE param = :param');
		$command->bindParam(':value', $value);
		$command->bindParam(':param', $key);
		$command->execute();

		$this->data[$key] = $value;
	}

	public function add ( $params ) {
		if ( is_array($params) ) {
			foreach ( $params as $item ) {
				$this->createParameter($item);
			}
		}
		elseif ( $params ) {
			$this->createParameter($params);
		}
	}

	public function delete ( $key ) {
		if ( is_array($key) ) {
			foreach ( $key as $item ) {
				$this->removeParameter($item);
			}
		}
		elseif ( $key ) {
			$this->removeParameter($key);
		}
	}

	protected function getDbConnection () {
		if ( $this->cache ) {
			$db = Yii::app()->db->cache($this->cache, $this->dependency);
		}
		else {
			$db = Yii::app()->db;
		}

		return $db;
	}

	protected function createParameter ( $param ) {
		if ( !empty($param['param']) ) {
			$db = $this->getDbConnection();
			$command = $db->createCommand('INSERT INTO {{config}} (param, value, default, label, type) VALUES(:param, :value, :default, :label, :type)');
			$command->bindValue(':param', $param['param']);
			$command->bindValue(':value', isset($param['value']) ? $param['value'] : '');
			$command->bindValue(':default', isset($param['default']) ? $param['default'] : '');
			$command->bindValue(':label', $param['param']);
			$command->bindValue(':type', isset($param['type']) ? $param['type'] : 'string');
			$command->execute();

			$this->data[$param['param']] = $param['value'] === '' ? $param['default'] : $param['value'];
		}
	}

	protected function removeParameter ( $key ) {
		if ( !empty($key) ) {
			$db = $this->getDbConnection();
			$command = $db->createCommand('DELETE FROM {{config}} WHERE param = :param');
			$command->bindParam(':param', $key);
			$command->execute();

			unset($this->data[$key]);
		}
	}
}