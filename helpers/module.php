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
	public static function file() {
		return Root\FILE;
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



	/**
	 * Package directory
	 */
	public static function dir() {

		static $dirname;
		if (isset($dirname)) {
			return $dirname;
		}

		$dirname = dirname(Root\FILE);
		return $dirname;
	}



}