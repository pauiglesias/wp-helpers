<?php

namespace MicroDeploy\Package\Helpers;

/**
 * Admin class
 *
 * Common methods used from the WordPress admin.
 *
 * @uses Util class
 * @uses Module class
 *
 * @package		WordPress
 * @subpackage	Helpers
 * @version		1.0.0
 * @license		GPLv3
 * @author		Pau Iglesias
 * @link		https://github.com/pauiglesias/wp-helpers
 */
class Admin {



	/**
	 * Creates a WP standar nonce based on a seed.
	 *
	 * @param string|null	$seed	The optional seed to create the nonce (by default the project FILE constant).
	 *
	 * @return string|false The generated nonce or false if the basis WP function is not available
	 */
	public static function createNonce($seed = null) {
		return function_exists('wp_create_nonce') ? wp_create_nonce(isset($seed) ? $seed : Module::file()) : false;
	}



	/**
	 * Verifies the WP nonce on a post submit from a given key
	 *
	 * @param string		$key 	The key to verify the nonce (will be prefixed).
	 * @param string|null	$seed	The optional seed that created the nonce (by default the project FILE constant).
	 *
	 * @return int|false	1 if the nonce is valid and generated between 0-12 hours ago,
	 *						2 if the nonce is valid and generated between 12-24 hours ago.
	 *						false if the nonce is invalid or the basis WP function is not available.
	 */
	public static function verifyNoncePosted($key, $seed = null) {
		return function_exists('wp_verify_nonce') ? wp_verify_nonce(Util::postParam($key), isset($seed) ? $seed : Module::file()) : false;
	}



	/**
	 * Verifies a nonce value based on a seed.
	 *
	 * @param string		$value	The value of the generated nonce (will NOT be prefixed).
	 * @param string|null	$seed	The optional seed that created the nonce (by default the project FILE constant).
	 *
	 * @return int|false	1 if the nonce is valid and generated between 0-12 hours ago,
	 *						2 if the nonce is valid and generated between 12-24 hours ago.
	 *						false if the nonce is invalid or the basis WP function is not available.
	 */
	public static function verifyNonceValue($value, $seed = null) {
		return function_exists('wp_verify_nonce') ? wp_verify_nonce($value, isset($seed) ? $seed : Module::file()) : false;
	}



	/**
	 * Checks if a given screen belongs to the slug
	 *
	 * @param string|object	$screen	The id or an object of a WP admin screen.
	 * @param string		$slug	The slug to compare with the screen id.
	 * @param string		$splig	The chunk to split the screen id.
	 *
	 * @return bool	Whether the screen belongs to the slug.
	 */
	public static function screenOf($screen, $slug, $split = '_page_') {

		if (empty($screen)) {
			return false;
		}

		$screenId = is_object($screen)
			? (empty($screen->id) ? null : $screen->id)
			: $screen;

		if (empty($screenId)) {
			return false;
		}

		$parts = explode($split, $screenId, 2);
		$chunk = empty($parts[1]) ? null : $parts[1];

		if (empty($chunk)) {
			return false;
		}

		if ($chunk === $slug) {
			return true;
		}

		if (0 === strpos($chunk, $slug.'-')) {
			$section = substr($chunk, strlen($slug) + 1);
			return '' === $section ? false : $section;
		}

		return false;
	}



}