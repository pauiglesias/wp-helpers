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
	 * Create or retrieve instance
	 */
	final public static function instance(...$args) {

		static $instance;

		if (!isset($instance)) {
			$instance = new static(...$args);
		}

		return $instance;
	}



	/**
	 * Constructor
	 */
	protected function __construct(...$args) {}



}