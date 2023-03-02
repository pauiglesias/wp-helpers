<?php

namespace MicroDeploy\Package\Helpers;

/**
 * Array Cache class
 *
 * Creates in-memory cache sets based on data arrays and a unique generated key.
 *
 * @package		WordPress
 * @subpackage	Helpers
 * @version		1.0.0
 * @license		GPLv3
 * @author		Pau Iglesias
 * @link		https://github.com/pauiglesias/wp-helpers
 */
class ArrayCache {



	/**
	 * Cached data
	 */
	private $data = [];



	/**
	 * Get cached data matching the given array arguments.
	 *
	 * @param array $args An associative array of arguments to generate the cache key.
	 *
	 *  @return mixed|null Returns the cached data if available or null if not found.
	 */
	public function get($args) {

		if (empty($args['cache'])) {
			return null;
		}

		$key = self::key($args);
		return isset($this->data[$key]) ? $this->data[$key] : null;
	}



	/**
	 * Set data in cache based on given arguments.
	 *
	 * @param array $args An associative array of arguments to generate the cache key.
	 * @param mixed $value The value to be stored in cache.
	 *
	 * @return bool Returns true on successful cache insertion, false otherwise.
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
	 * Generates cache key based on sorting the given array.
	 *
	 * param array $args An associative array of arguments to generate the cache key.
	 *
	 * @return string Returns a unique cache key based on the given arguments.
	 */
	public static function key($args) {
		ksort($args);
		return md5(print_r($args, true));
	}



}