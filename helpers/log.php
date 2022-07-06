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
		}
		return isset($active) ? $active : Module::debug();
	}



}