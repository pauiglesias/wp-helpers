<?php

namespace MicroDeploy\Package\Helpers;

/**
 * Log class
 *
 * @package WordPress
 * @subpackage Helpers
 */
class Log {



	public static function error($value, $before = '') {

		$content = $value;

		if (is_wp_error($content)) {
			$content = $content->get_error_message();

		} elseif (is_array($content) || is_object($content)) {
			$content = print_r($content, true);
		}

		error_log($before.('' === $before ? '' : ': ').$content);
	}



	/**
	 * Set or get the debugging mode
	 */
	public static function debugging($value = null) {
		static $active;
		if (isset($value)) {
			$active = $value;
		}
		return isset($active) ? $active : false;
	}



	/**
	 * Log only on debugguing mode
	 */
	public static function debug($value, $before = '') {
		if (self::debugging()) {
			self::error($value, $before);
		}
	}



}