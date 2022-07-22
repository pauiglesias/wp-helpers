<?php

namespace MicroDeploy\Package\Admin;

use \MicroDeploy\Package\Helpers\Module;
use \MicroDeploy\Package\Helpers\Notices;
use \MicroDeploy\Package\Helpers\Singleton;
use \MicroDeploy\Package\Helpers\Util;

/**
 * Settings class
 *
 * @package WC Stel Order API
 * @subpackage Admin
 */
final class Settings extends Singleton {



	/**
	 * Handle form submit
	 */
	public function handle() {

		if (!$this->verifyNonce('nonce_settings')) {
			return;
		}

		// Update code or function call ...

		Notices::success('Data successfully updated');
	}



	/**
	 * Verifies submit nonce
	 */
	private function verifyNonce($key) {

		if (!wp_verify_nonce(Util::postParam($key), Module::file())) {
			Notices::error('Not valid security credentials. Please try again.');
			return false;
		}

		return true;
	}



	/**
	 * Display page content
	 */
	public function display() {

	}



}