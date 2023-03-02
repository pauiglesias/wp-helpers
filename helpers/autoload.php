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
	 * Singleton instance of the AutoLoad class.
	 *
	 * @var AutoLoad|null
	 */
	private static $instance;



	/**
	 * The project vendor name.
	 *
	 * @var string
	 */
	private $vendor;



	/**
	 * The main package name.
	 *
	 * @var string
	 */
	private $package;



	/**
	 * Root directory path of the current project.
	 *
	 * @var string
	 */
	private $dir;



	/**
	 * The main file path of the current project.
	 *
	 * @var string
	 */
	private $file;



	/**
	 * An array containing the paths of already loaded files
	 * *
	 * @var array
	*/
	private $loaded = [];



	/**
	 * Returns a singleton instance of the current class.
	 *
	 * @return self The singleton instance of the current class.
	 */
	public static function instance() {

		if (!isset(self::$instance)) {
			self::$instance = new self;
		}

		return self::$instance;
	}



	/**
	 * Registers a new instance of the class and loads a specified class name if provided.
	 *
	 * @param string|null $className The name of the class to be loaded.
	 *
	 * @return void
	 */
	public static function register($className = null) {

		self::instance();

		if (!empty($className)) {
			self::$instance->load($className);
		}
	}



	/**
	 * Private constructor that initializes the object checking the namespace and set the project root.
	 *
	 * @return void
	 */
	private function __construct() {
		$this->namespace();
		$this->root();
	}



	/**
	 * Initializes the vendor and package properties based on the current namespace.
	 *
	 * @return void
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
	 * Initializes the file and dir properties based on the root directory constant of the class.
	 *
	 * @return void
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
	 * Loads a PHP file from the given class name if it exists and has not been loaded before.
	 *
	 * @param string $className The name of the class to be loaded.
	 *
	 * @return void
	 */
	public function load($className) {

		$path = $this->path($className);
		if (!$path || in_array($path, $this->loaded)) {
			return;
		}

		$this->loaded[] = $path;
		if (@file_exists($path)) {
			require_once $path;
		}
	}



	/**
	 * Gets the path to the PHP file containing the given class name if it matches the expected vendor and package names.
	 *
	 * @param string $className The name of the class to get the path for.
	 *
	 * @return string|false The path to the PHP file in case of matching the project vendor and package.
	 */
	private function path($className) {
		$namespace = $this->match($className);
		return $namespace ? $this->dir.DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, array_map([self::class, 'studlyCaps2Kebab'], $namespace)).'.php' : false;
	}



	/**
	 * Matches the given class name with the vendor and package names of the registered instance.
	 *
	 * @param string $className The name of the class to match.
	 *
	 * @return array|false An array of namespace parts if the vendor and package names match, false otherwise.
	 */
	private function match($className) {

		if (!isset($this->vendor) ||
			!isset($this->dir)) {
			return false;
		}

		$namespace = explode('\\', $className);
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
	 * Converts a string from StudlyCaps to kebab-case.
	 *
	 * @param string $className The input string to be converted.
	 *
	 * @return string The converted string in kebab-case.
	 */
	public static function studlyCaps2Kebab($className) {

		static $letters;
		if (!isset($letters)) {
			$letters = range('A', 'Z');
		}

		static $cached = [];
		if (isset($cached[$className])) {
			return $cached[$className];
		}

		$converted = '';

		for ($i = 0; $i < strlen($className); $i++) {
			$char = substr($className, $i, 1);
			$converted .= 0 === $i
				? strtolower($char)
				: (in_array($char, $letters) ? '-'.strtolower($char) : str_replace('_', '-', $char));
		}

		$cached[$className] = $converted;

		return $converted;
	}



	/**
	 * Returns the directory path of the current namespace.
	 *
	 * @return string The directory path of the current namespace.
	 */
	public function dir() {
		return $this->dir;
	}



	/**
	 * Returns the file path of the current namespace.
	 *
	 * @return string The file path of the current namespace.
	 */
	public function file() {
		return $this->file;
	}



}

/**
 * Registers the AutoLoad::register method as an autoloading function.
 *
 * @param callable $autoload_function The autoloading function to register.
 * @param bool $throw Whether to throw exceptions when the autoload function fails to find a class. Default true.
 * @param bool $prepend Whether to prepend the autoloading function to the autoloading functions stack. Defautl false.
 */
spl_autoload_register(__NAMESPACE__.'\AutoLoad::register', true);