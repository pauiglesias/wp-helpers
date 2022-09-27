<?php

namespace MicroDeploy\Package\Helpers;

/**
 * Cache Args class
 *
 * @package WordPress
 * @subpackage Helpers
 */
class CacheArgs {



	/**
	 * Cached data
	 */
	private $data = [];



	/**
	 * Check a cached element
	 */
	public function get($args) {

		if (empty($args['cache'])) {
			return null;
		}

		$key = self::key($args);
		return isset($this->data[$key]) ? $this->data[$key] : null;
	}



	/**
	 * Saves data into cache
	 *
	 * @todo change class to cache-args
	 */
	public function set($args, $value) {

		if (empty($args['cache'])) {
			return false;
		}

		$key = self::key($args);
		$this->data[$key] = $value;
		return true;
	}



	/**
	 * Composes the cache key from the arguments
	 */
	public static function key($args) {
		ksort($args);
		return md5(print_r($args, true));
	}



}