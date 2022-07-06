<?php

namespace MicroDeploy\Package\Helpers;

use \MicroDeploy\Package as Root;

/**
 * Prefix class
 *
 * @package WordPress
 * @subpackage Helpers
 */
class Prefix {



	/**
	 * Meta key protection
	 */
	private static $metaProtectedValue;



	/**
	 * Return current prefix
	 */
	public static function prefix() {
		return Root\PREFIX;
	}



	/**
	 * Basic name composition without escaping
	 */
	public static function key($name, $join = '_') {
		return self::prefix().$join.$name;
	}



	/**
	 * Escapes key as an attribute
	 */
	public static function escAttrKey($name, $join = '_') {
		return esc_attr(self::key($name, $join));
	}



	/**
	 * Escape attribute and echoes its value by default
	 */
	public static function escAttr($names, $echo = true, $join = '-') {

		if (!is_array($names)) {
			$names = [$names];
		}

		$values = [];
		foreach ($names as $name) {
			$values[] = esc_attr(self::prefix().$join.$name);
		}

		$attrs = implode(' ', $values);

		if ($echo) {
			echo $attrs;
		}

		return $attrs;
	}



	/**
	 * Escape attribute using `_` join and echoes by default
	 */
	public static function escAttr_($names, $echo = true) {
		return self::escAttr($names, $echo, '_');
	}



	/**
	 * Retrieve escaped attributes
	 */
	public static function escAttrGet($names) {
		return self::escAttr($names, false);
	}



	/**
	 * Retrieve escaped attributes using `_` join
	 */
	public static function escAttrGet_($names) {
		return self::escAttr($names, false, '_');
	}



	/**
	 * Retrieve a post data via prefix
	 */
	public static function post($name, $join = '_') {
		$var = self::prefix().$join.$name;
		return isset($_POST[$var]) ? $_POST[$var] : null;
	}



	/**
	 * Retrieve an array of post data
	 */
	public static function posts($names, $join = '_') {
		$posts = [];
		foreach ($names as $name) {
			$posts[$name] = self::post($name, $join);
		}
		return $posts;
	}



	/**
	 * Retrieve a Url GET data
	 */
	public static function getParam($name, $join = '_') {
		$var = self::prefix().$join.$name;
		return isset($_GET[$var]) ? $_GET[$var] : null;
	}



	/**
	 * Retrieve an array of Url GET data
	 */
	public static function getParams($names, $join = '_') {
		$params = [];
		foreach ($names as $name) {
			$params[$name] = self::getParam($name, $join);
		}
		return $params;
	}



	/**
	 * Retrieve meta value
	 */
	public static function meta($postId, $name, $single = true, $join = '_') {
		return get_post_meta($postId, self::prefix().$join.$name, $single);
	}



	/**
	 * Updates a meta value
	 */
	public static function metaUpdate($postId, $name, $value, $join = '_') {
		return update_post_meta($postId, self::prefix().$join.$name, $value);
	}



	/**
	 * Protects the current key as a prefix
	 */
	public static function metaProtected($join = '_', $extended = '') {
		self::$metaProtectedValue = self::prefix().$join.$extended;
		add_filter('is_protected_meta', function($protected, $metaKey) {
			return false !== stripos($metaKey, Prefix::metaProtectedValue()) ? true : $protected;
		}, PHP_INT_MAX, 2);
	}



	/**
	 * Retrieve the current metaprotected value
	 */
	public static function metaProtectedValue() {
		return self::$metaProtectedValue;
	}



	/**
	 * Retrieves several metas
	 */
	public static function metas($postId, $names, $single = true, $join = '_') {
		$metas = [];
		foreach ($names as $name) {
			$metas[$name] = self::meta($postId, $name, $single, $join);
		}
		return $metas;
	}



	/**
	 * Updates several meta data
	 */
	public static function metasUpdate($postId, $metas, $join = '_') {
		$updates = [];
		foreach ($metas as $name => $value) {
			$updates[$name] = self::metaUpdate($postId, $name, $value, $join);
		}
		return $updates;
	}



	/**
	 * Retrieves a single option
	 */
	public static function option($name, $default = false, $join = '_') {
		return get_option(self::prefix().$join.$name, $default);
	}



	/**
	 * Update a single option
	 */
	public static function optionUpdate($name, $value, $autoload = null, $join = '_') {
		return update_option(self::prefix().$join.$name, $value, $autoload);
	}



	/**
	 * Retrieves several options
	 */
	public static function options($names, $default = false, $join = '_') {
		$options = [];
		foreach ($names as $name) {
			$options[$name] = self::option($name, $default, $join);
		}
		return $options;
	}



	/**
	 * Updates several options
	 */
	public static function optionsUpdate($options, $autoload = null, $join = '_') {
		$updates = [];
		foreach ($options as $name => $value) {
			$updates[$name] = self::optionUpdate($name, $value, $autoload, $join);
		}
		return $updates;
	}



}