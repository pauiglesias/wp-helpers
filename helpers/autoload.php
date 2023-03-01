<?php

namespace MicroDeploy\Package\Helpers;

/**
 * Autoload class
 *
 * An autoloading based on the root namespace scheme vendor/package.
 *
 * @package		WordPress
 * @subpackage	Helpers
 * @version		1.1.0
 * @license		GPLv3
 * @author		Pau Iglesias
 * @link		https://github.com/pauiglesias/wp-helpers
 */
final class AutoLoad {



	/**
	 * Singleton
	 */
	private static $instance;



	/**
	 * Namespace vendor and package
	 */
	private $vendor;
	private $package;



	/**
	 * Root directory and main file
	 */
	private $dir;
	private $file;



	/**
	 * Loaded files
	 */
	private $loaded = [];



	/**
	 * Retrieve single object instance
	 */
	public static function instance() {

		if (!isset(self::$instance)) {
			self::$instance = new self;
		}

		return self::$instance;
	}



	/**
	 * Create instance and try to load the class
	 */
	public static function register($name = null) {

		self::instance();

		if (!empty($name)) {
			self::$instance->load($name);
		}
	}



	/**
	 * Constructor
	 */
	private function __construct() {
		$this->namespace();
		$this->root();
	}



	/**
	 * Check namespace for vendor and package names
	 */
	private function namespace() {

		$namespace = explode('\\', __NAMESPACE__);
		if (count($namespace) < 2) {
			return;
		}

		$this->vendor = $namespace[0];
		$this->package = $namespace[1];
	}



	/**
	 * Check the root via package constant
	 */
	private function root() {

		$const = '\\'.$this->vendor.'\\'.$this->package.'\\FILE';
		if (!defined($const)) {
			return;
		}

		$hook = 'AutoLoad_'.ltrim($const, '\\');
		$this->file = apply_filters($hook, constant($const));
		$this->dir = dirname($this->file);
	}



	/**
	 * Load by namespace
	 */
	public function load($name) {

		$path = $this->path($name);
		if (!$path || in_array($path, $this->loaded)) {
			return;
		}

		$this->loaded[] = $path;
		if (@file_exists($path)) {
			require_once $path;
		}
	}



	/**
	 * Composes namespace path
	 */
	private function path($name) {
		$namespace = $this->match($name);
		return $namespace ? $this->dir.'/'.implode('/', array_map([self::class, 'studlyCaps2Kebab'], $namespace)).'.php' : false;
	}



	/**
	 * Check namespace and class request matches
	 */
	private function match($name) {

		if (!isset($this->vendor) ||
			!isset($this->dir)) {
			return false;
		}

		$namespace = explode('\\', $name);
		if ($this->vendor != $namespace[0]) {
			return false;
		}

		array_shift($namespace);

		if ($this->package != $namespace[0]) {
			return false;
		}

		array_shift($namespace);

		return empty($namespace) ? false : $namespace;
	}



	/**
	 * Converts StudlyCaps class names to keban notation
	 */
	public static function studlyCaps2Kebab($name) {

		static $letters;
		if (!isset($letters)) {
			$letters = range('A', 'Z');
		}

		static $cached = [];
		if (isset($cached[$name])) {
			return $cached[$name];
		}

		$converted = '';

		for ($i = 0; $i < strlen($name); $i++) {
			$char = substr($name, $i, 1);
			$converted .= 0 === $i
				? strtolower($char)
				: (in_array($char, $letters) ? '-'.strtolower($char) : str_replace('_', '-', $char));
		}

		$cached[$name] = $converted;

		return $converted;
	}



	/**
	 * Retrieve root directory
	 */
	public function dir() {
		return $this->dir;
	}



	/**
	 * Retrieve main file
	 */
	public function file() {
		return $this->file;
	}



}

// Autoload in throw exceptions mode
spl_autoload_register(__NAMESPACE__.'\AutoLoad::register', true);