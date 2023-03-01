<?php

namespace MicroDeploy\Package\Helpers;

/**
 * Bearer class
 *
 * Checks and retrieves the Bearer token from the request headers.
 *
 * @package		WordPress
 * @subpackage	Helpers
 * @version		1.0.0
 */
class Bearer {



	/**
	 * Get access token from header
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
	 * Obtain the authorization header
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
	 * Retrieve Apache Authorization Header
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
		$requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));

		if (!isset($requestHeaders['Authorization'])) {
			return null;
		}

		return trim($requestHeaders['Authorization']);
	}



}