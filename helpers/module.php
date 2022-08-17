<?php

namespace MicroDeploy\Package\Helpers;

use \MicroDeploy\Package as Root;

/**
 * Module class
 *
 * @package WordPress
 * @subpackage Helpers
 */
class Module {



	/**
	 * Constant
	 */
	public static function name() {
		return Root\NAME;
	}



	/**
	 * Constant
	 */
	public static function slug() {
		return Root\SLUG;
	}



	/**
	 * Constant
	 */
	public static function file() {
		return AutoLoad::instance()->file();
	}



	/**
	 * Package directory
	 */
	public static function dir() {
		return AutoLoad::instance()->dir();
	}



	/**
	 * Constant
	 */
	public static function prefix() {
		return Root\PREFIX;
	}



	/**
	 * Constant
	 */
	public static function version() {
		return Root\VERSION;
	}



	/**
	 * Constant
	 */
	public static function dev() {
		return Root\DEV;
	}



	/**
	 * Constant
	 */
	public static function debug() {
		return Root\DEBUG;
	}



}