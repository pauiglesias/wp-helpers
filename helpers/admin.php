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
	 * Verifies nonce submit
	 */
	public static function verifyNonce($key) {

		if (!wp_verify_nonce(Util::postParam($key), Module::file())) {
			Notices::error('Not valid security credentials. Please try again.');
			return false;
		}

		return true;
	}



}