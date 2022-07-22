<?php

namespace MicroDeploy\Package\Admin;

use \MicroDeploy\Package\Helpers\Singleton;
use \MicroDeploy\Package\Helpers\Util;

/**
 * Admin class
 *
 * @package WP-Helpers
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
		$this->columns();
	}



	/**
	 * Loads the admin menu options
	 */
	private function menus() {
		add_action('admin_menu', function() {
			$this->settingsHook = add_submenu_page('options-general.php', 'Settings Page', 'Settings Page', $this->settingsCapability(), $this->settingsKey(), [$this, 'settingsDisplay']);
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
	 * Listing columns hooks
	 */
	private function columns() {
		add_filter('manage_posts_columns', [$this, 'columnsHead'], 11, 2);
		add_action('manage_posts_custom_column', [$this, 'columnsBody'], 11, 2);
	}



	/**
	 * Adds custom columns
	 */
	public function columnsHead($columns, $postType) {

		if ('myPostType' == $postType) {
			return Columns::instance()->columnsHead($columns);
		}

		return $columns;
	}



	/**
	 * Content column for each post
	 */
	public function columnsBody($columnName, $postId) {

		if (0 === strpos($columnName, Util::key(''))) {
			Columns::instance()->columnsBody($columnName, $postId);
		}
	}



	/**
	 * Decides menu capability
	 */
	private function settingsCapability() {
		return 'publish_posts';
	}



	/**
	 * Compose the page prefix
	 */
	private function settingsKey() {
		return Util::key('settings');
	}



}