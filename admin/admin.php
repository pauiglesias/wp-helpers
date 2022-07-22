<?php

namespace MicroDeploy\Package\Admin;

use \MicroDeploy\Package\Helpers\Singleton;
use \MicroDeploy\Package\Helpers\Util;

/**
 * Admin class
 *
 * @package WC Stel Order API
 * @subpackage Admin
 */
final class Admin extends Singleton {



	/**
	 * WP generated hook
	 */
	private $settingsHook;



	/**
	 * Constructor
	 */
	protected function __construct() {
		$this->menus();
		$this->screen();
	}



	/**
	 * Loads the admin menu options
	 */
	private function menus() {
		add_action('admin_menu', function() {
			$this->settingsHook = add_submenu_page('options-general.php', 'Settings Page', 'Settings Page', 'publish_posts', $this->settingsKey(), [$this, 'settingsDisplay']);
		});
	}



	/**
	 * Display from settings page
	 */
	public function settingsDisplay() {
		Settings::instance()->display();
	}



	/**
	 * Handles screen hook before loading
	 */
	private function screen() {
		add_action('current_screen', function($screen) {
			if (!empty($this->settingsHook)) {
				add_action('load-'.$this->settingsHook, [$this, 'settingsHandle']);
			}
		});
	}



	/**
	 * Handle the settings submit
	 */
	public function settingsHandle() {
		if (Util::postParam('nonce_settings')) {
			Settings::instance()->handle();
			return;
		}
	}



	/**
	 * Compose the page prefix
	 */
	public function settingsKey() {
		return Util::key('settings');
	}



}