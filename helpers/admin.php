<?php

namespace MicroDeploy\Package\Helpers;

/**
 * Admin class
 *
 * @package WordPress
 * @subpackage Helpers
 */
class Admin {



	/**
	 * Creates nonce based on a seed or module file
	 */
	public static function createNonce($seed = null) {
		return wp_create_nonce(isset($seed) ? $seed : Module::file());
	}



	/**
	 * Verifies nonce submit based on a seed or module file
	 */
	public static function verifyNonce($key, $seed = null) {

		if (!wp_verify_nonce(Util::postParam($key), isset($seed) ? $seed : Module::file())) {
			return false;
		}

		return true;
	}



	/**
	 * Check if a given screen belongs to the slug
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