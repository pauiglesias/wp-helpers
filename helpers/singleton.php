<?php

namespace MicroDeploy\Package\Helpers;

/**
 * Singleton class
 *
 * @package WordPress
 * @subpackage Helpers
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