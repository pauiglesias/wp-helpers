<?php

namespace MicroDeploy\Package\Helpers;

/**
 * Bearer class
 *
 * Provides a set of static methods for obtaining the access token from the
 * authorization header in the HTTP request.
 *
 * @package		WordPress
 * @subpackage	Helpers
 * @version		1.0.0
 * @category	 Authentication
 * @license		GPLv3
 * @author		Pau Iglesias
 * @link		https://github.com/pauiglesias/wp-helpers
 */
class Bearer {



	/**
	 * Get the access token from the authorization header.
	 *
	 * @return string|null The access token string or null if not found.
	 */
	public static function token() {

		$headers = self::header();
		if (empty($headers)) {
			return null;
		}

		if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
			return $matches[1];
		}

		return null;
	}



	/**
	 * Obtain the authorization header from the request headers.
	 *
	 * @return string|null The authorization header string or null if not found.
	 */
	public static function header(){

		if (isset($_SERVER['Authorization'])) {
			return trim($_SERVER['Authorization']);
		}

		// Nginx or fast CGI
		if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
			return trim($_SERVER['HTTP_AUTHORIZATION']);
		}

		$apacheAuthHeader = self::apacheAuthHeader();
		if (!empty($apacheAuthHeader)) {
			return $apacheAuthHeader;
		}

		return null;
	}



	/**
	 * Retrieve the Apache Authorization Header from the request headers.
	 *
	 * @return string|null The Apache Authorization Header string or null if not found.
	 */
	private static function apacheAuthHeader() {

		if (!function_exists('apache_request_headers')) {
			return null;
		}

		$requestHeaders = @apache_request_headers();
		if (empty($requestHeaders) || !is_array($requestHeaders)) {
			return null;
		}

		// Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
		$requestHeaders = array_combine(
			array_map('ucwords', array_keys($requestHeaders)),
			array_values($requestHeaders)
		);

		if (!isset($requestHeaders['Authorization'])) {
			return null;
		}

		return trim($requestHeaders['Authorization']);
	}



}