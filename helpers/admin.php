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
	 * Creates nonce based on module file
	 */
	public static function createNonce() {
		return wp_create_nonce(Module::file());
	}



	/**
	 * Verifies nonce submit based on module file
	 */
	public static function verifyNonce($key) {

		if (!wp_verify_nonce(Util::postParam($key), Module::file())) {
			Notices::error('Not valid security credentials. Please try again.');
			return false;
		}

		return true;
	}



}