<?php

namespace MicroDeploy\Package\Helpers;

/**
 * Ajax class
 *
 * Provides methods to handle AJAX requests and responses in a standardized way.
 *
 * @uses Module class
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
	 * Checking for a valid ajax nonce
	 *
	 * @param string		$param 	The POST param name to verify the nonce (will NOT be prefixed).
	 * @param string|null	$seed	The optional seed that created the nonce (by default the project FILE constant).
	 *
	 * @return int|false	1 if the nonce is valid and generated between 0-12 hours ago,
	 *						2 if the nonce is valid and generated between 12-24 hours ago.
	 *						false if the nonce is invalid or the basis WP function is not available.
	 */
	public static function verifyNoncePosted($param, $seed = null) {
		return !empty($_POST[$param]) &&
		 		function_exists('wp_verify_nonce') &&
				wp_verify_nonce($_POST[$param], isset($seed) ? $seed : Module::file());
	}



	/**
	 * Default response, consisting of a status indicator and associated data.
	 *
	 * @param array		$data	An array of data for the output response.
	 *
	 * @return array The defined response.
	 */
	public static function default($data = []) {
		return ['status' => 'ok', 'data' => $data];
	}



	/**
	 * Outputs response on success in JSON format.
	 *
	 * @param array		$data	An array of data for the output response.
	 *
	 * @return void
	 */
	public static function success($data = []) {
		self::json(self::default($data));
	}



	/**
	 * Outputs an error response in JSON format.
	 *
	 * @param string	$reason		The error description.
	 * @param array		$data		An array of data for the output response.
	 *
	 * @return void
	 */
	public static function error($reason, $data = []) {
		self::json(['status' => 'error', 'reason' => $reason, 'data' => $data]);
	}



	/**
	 * Ouputs the AJAX response in JSON format
	 *
	 * @param array		$response	The response array to be encoded in JSON format.
	 *
	 * @return void
	 */
	public static function json($response) {
		@header('Content-Type: application/json');
		die(@json_encode($response, self::jsonFlags()));
	}



	/**
	 * Allows to customize the Json flags
	 *
	 * @param string|null	$flags	Optional flags to encode.
	 *
	 * @return string The default JSON flags or the defined ones.
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