<?php

namespace MicroDeploy\Package\Helpers;

/**
 * Autoload class
 *
 * @package WordPress
 * @subpackage Helpers
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
	 * Plugin root path
	 */
	private $root;



	/**
	 * Loaded files
	 */
	private $loaded = array();



	/**
	 * Create instance and try to load the class
	 */
	public static function register($name = null) {

		if (!isset(self::$instance)) {
			self::$instance = new self;
		}

		if (!empty($name)) {
			self::$instance->load($name);
		}
	}



	/**
	 * Constructor
	 */
	private function __construct() {

		$namespace = explode('\\', __NAMESPACE__);
		if (count($namespace) < 2) {
			return;
		}

		$this->vendor = $namespace[0];
		$this->package = $namespace[1];

		$const = '\\'.$this->vendor.'\\'.$this->package.'\\FILE';
		if (defined($const)) {
			$this->root = dirname(constant($const));
		}
	}



	/**
	 * Load by namespace
	 */
	public function load($name) {

		if (!isset($this->root)) {
			return;
		}

		$namespace = explode('\\', $name);
		if ($this->vendor != $namespace[0]) {
			return;
		}

		array_shift($namespace);

		if ($this->package == $namespace[0]) {
			array_shift($namespace);
		}

		$path = $this->root.'/'.implode('/', str_replace('_', '-', array_map('strtolower', $namespace))).'.php';
		if (in_array($path, $this->loaded)) {
			return;
		}

		$this->loaded[] = $path;
		if (@file_exists($path)) {
			require_once $path;
		}
	}



}

// Autoload in throw exceptions mode
spl_autoload_register(__NAMESPACE__.'\AutoLoad::register', true);