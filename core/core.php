<?php

namespace MicroDeploy\Package\Core;

use \MicroDeploy\Package\Admin\Admin;

use \MicroDeploy\Package\Helpers\Singleton;
use \MicroDeploy\Package\Helpers\Util;

/**
 * Core class
 *
 * @package WP-Helpers
 * @subpackage Core
 */
final class Core extends Singleton {



	/**
	 * Constructor
	 */
	protected function __construct() {
		$this->admin();
	}



	/**
	 * Loads the admin module
	 */
	private function admin() {

		if (!is_admin()) {
			return;
		}

		//Util::metaProtectedPrefix();

		if (wp_doing_ajax()) {
			return;
		}

		Admin::instance();
	}



}