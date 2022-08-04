<?php

namespace MicroDeploy\Package\Helpers;

/**
 * Util class
 *
 * @package WordPress
 * @subpackage Helpers
 */
class Util {



	/**
	 * Basic name composition without escaping
	 */
	public static function key($name, $join = '_') {
		return Module::prefix().$join.$name;
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

		$attrs = is_array($names) ? $names : [$names];

		$values = [];
		foreach ($attrs as $attr) {
			$values[] = esc_attr(Module::prefix().$join.$attr);
		}

		if ($echo) {
			echo implode(' ', $values);
		}

		return is_array($names) ? $values : $values[0];
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
	public static function postParam($name, $join = '_') {
		$var = Module::prefix().$join.$name;
		return isset($_POST[$var]) ? $_POST[$var] : null;
	}



	/**
	 * Retrieve an array of post data
	 */
	public static function posts($names, $join = '_') {
		$posts = [];
		foreach ($names as $name) {
			$posts[$name] = self::postParam($name, $join);
		}
		return $posts;
	}



	/**
	 * Retrieve a Url GET data
	 */
	public static function getParam($name, $join = '_') {
		$var = Module::prefix().$join.$name;
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
		return get_post_meta($postId, Module::prefix().$join.$name, $single);
	}



	/**
	 * Updates a meta value
	 */
	public static function metaUpdate($postId, $name, $value, $join = '_') {
		return update_post_meta($postId, Module::prefix().$join.$name, $value);
	}



	/**
	 * Protects a given prefix against WP overwriting
	 */
	public static function metaProtectedPrefix($prefix = null) {

		static $prefixes = [];

		if (!isset($prefix)) {
			$prefix = Module::prefix();
		}

		if (!in_array($prefix, $prefixes)) {
			$prefixes[] = $prefix;
		}

		if (1 != count($prefixes)) {
			return;
		}

		add_filter('is_protected_meta', function($protected, $metaKey) use($prefixes) {

			foreach ($prefixes as $prefix) {
				if (0 === stripos($metaKey, $prefix)) {
					return true;
				}
			}

			return $protected;

		}, PHP_INT_MAX, 2);
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
		return get_option(Module::prefix().$join.$name, $default);
	}



	/**
	 * Update a single option
	 */
	public static function optionUpdate($name, $value, $autoload = null, $join = '_') {
		return update_option(Module::prefix().$join.$name, $value, $autoload);
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