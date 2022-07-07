<?php

namespace MicroDeploy\Package\Helpers;

/**
 * Log class
 *
 * @package WordPress
 * @subpackage Helpers
 */
class Log {



	/**
	 * Send an error to the error log
	 */
	public static function error($value, $before = '', $split = ': ') {

		$content = $value;

		if (is_wp_error($content)) {
			$content = $content->get_error_message();

		} elseif (is_array($content) || is_object($content)) {
			$content = print_r($content, true);
		}

		error_log($before.('' === $before ? '' : $split).$content);
	}



	/**
	 * Log only whether is under debug or dev mode
	 */
	public static function debugOrDev($value, $before = '', $split = ': ') {
		if (Module::dev() || self::debugging()) {
			self::error($value, $before, $split);
		}
	}



	/**
	 * Checks wheter is running in dev mode
	 */
	public static function dev($value, $before = '', $split = ': ') {
		if (Module::dev()) {
			self::error($value, $before, $split);
		}
	}



	/**
	 * Log only on debugging mode
	 */
	public static function debug($value, $before = '', $split = ': ') {
		if (self::debugging()) {
			self::error($value, $before, $split);
		}
	}



	/**
	 * Set or get the debugging mode
	 */
	public static function debugging($value = null) {

		static $active;
		if (isset($value)) {
			$active = $value;
			return $active;
		}

		return isset($active) ? $active : Module::debug();
	}



}