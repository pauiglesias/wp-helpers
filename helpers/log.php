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



}