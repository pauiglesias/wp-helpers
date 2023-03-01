<?php

namespace MicroDeploy\Package\Helpers;

/**
 * Notices class
 *
 * A simple way to create notifications in WordPress admin.
 *
 * @package		WordPress
 * @subpackage	Helpers
 * @version		1.0.0
 * @license		GPLv3
 * @author		Pau Iglesias
 * @link		https://github.com/pauiglesias/wp-helpers
 */
class Notices {



	/**
	 * Data store
	 */
	private static $notices = [];



	/**
	 * Supported classes
	 */
	private static $classes = [
		'updated' => 'updated notice notice-success',
		'success' => 'notice notice-success',
		'warning' => 'notice notice-warning',
		'error'	  => 'notice notice-error',
	];



	/**
	 * Add updated notice
	 */
	public static function updated($notice, $classes = '') {
		self::add($notice, 'updated', $classes);
	}



	/**
	 * Add success notice
	 */
	public static function success($notice, $classes = '') {
		self::add($notice, 'success', $classes);
	}



	/**
	 * Add warning notice
	 */
	public static function warning($notice, $classes = '') {
		self::add($notice, 'warning', $classes);
	}



	/**
	 * Add error notice
	 */
	public static function error($notice, $classes = '') {
		self::add($notice, 'error', $classes);
	}



	/**
	 * Register notice
	 */
	private static function add($notice, $type, $classes) {
		self::$notices[] = [$notice, $type, $classes];
		if (1 == count(self::$notices)) {
			add_action('admin_notices', [__CLASS__, 'display']);
		}
	}



	/**
	 * Display the admin notices
	 */
	public static function display() {
		foreach (self::$notices as $notice) : ?>
			<div class="<?php echo self::classes($notice); ?>"><p style="padding:0;"><?php echo $notice[0]; ?></p></div>
		<?php endforeach;
	}



	/**
	 * Composes the notice classes
	 */
	private static function classes($notice) {
		$classes = self::$classes[$notice[1]];
		$extra = ''.esc_attr(trim($notice[2]));
		return '' === $extra ? $classes : $classes.' '.$extra;
	}



}