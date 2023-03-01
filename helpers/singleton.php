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
class Singleton {



	/**
	 * Prevent direct `new` operation constructions
	 */
	protected function __construct(...$args) {}



	/**
	 * Singletons should not be cloneable.
	 */
	protected function __clone() { }



	/**
	 * Create or retrieve instance
	 */
	final public static function instance(...$args) {

		static $instance;

		if (!isset($instance)) {
			$instance = new static(...$args);
		}

		return $instance;
	}



}