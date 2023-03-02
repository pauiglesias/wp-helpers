<?php

namespace MicroDeploy\Package\Helpers;

/**
 * Singleton class
 *
 * A singleton pattern implementation.
 *
 * @package		WordPress
 * @subpackage	Helpers
 * @version		1.0.0
 * @license		GPLv3
 * @author		Pau Iglesias
 * @link		https://github.com/pauiglesias/wp-helpers
 */
abstract class Singleton {



	/**
	 * Create or retrieve instance
	 */
	final public static function instance(...$args) {

		static $instance = [];

		$class = get_called_class();

		if (!isset($instance[$class])) {
			$instance[$class] = new static(...$args);
		}

		return $instance[$class];
	}



	/**
	 * Allow constructor
	 */
	protected function __construct(...$args) {}



	/**
	 * Prevent object cloning
	 */
	private function __clone() { }



}