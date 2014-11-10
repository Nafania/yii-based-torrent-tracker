<?php
namespace modules\tracking\components;

/**
 * Interface Trackable
 */
interface Trackable {

	/**
	 * @return int timestamp of record
	 */
	public function getLastTime();
}