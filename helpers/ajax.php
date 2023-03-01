<?php

namespace MicroDeploy\Package\Helpers;

/**
 * AJAX class
 *
 * Wrapper AJAX methods and default response definition.
 *
 * @package		WordPress
 * @subpackage	Helpers
 * @version		1.0.0
 * @license		GPLv3
 * @author		Pau Iglesias
 * @link		https://github.com/pauiglesias/wp-helpers
 */
class Ajax {



	/**
	 * Checking for a valid nonce
	 */
	public static function verifyNoncePosted($param, $seed) {
		return !empty($_POST[$param]) &&
		 		function_exists('wp_verify_nonce') &&
				wp_verify_nonce($_POST[$param], $seed);
	}



	/**
	 * Default response
	 */
	public static function default($data = []) {
		return ['status' => 'ok', 'data' => $data];
	}



	/**
	 * Output with data
	 */
	public static function success($data = []) {
		self::json(self::default($data));
	}



	/**
	 * Output an error response
	 */
	public static function error($reason, $data = []) {
		self::json(['status' => 'error', 'reason' => $reason, 'data' => $data]);
	}



	/**
	 * AJAX response in JSON format
	 */
	public static function json($response) {
		@header('Content-Type: application/json');
		die(@json_encode($response, self::jsonFlags()));
	}



	/**
	 * Allows to customize the Json flags
	 */
	public static function jsonFlags($flags = null)  {

		static $custom;
		if (isset($flags)) {
			$custom = $flags;
			return $custom;
		}

		return isset($custom) ? $custom : JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES;
	}



}