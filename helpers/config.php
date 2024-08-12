<?php

namespace MicroDeploy\Package\Helpers;

/**
 * Config class
 *
 * Provides access to an additional config.php file located in the root of the module.
 *
 * This class is used to manage configuration settings from a specified file.
 * It offers methods to load and retrieve configuration data, and handles the
 * file's location in relation to a subdirectory, if specified.
 *
 * @uses Module class (used to determine the base directory of the module)
 *
 * @package		WordPress
 * @subpackage	Helpers
 * @version		1.0.0
 * @license		GPLv3
 * @author		Pau Iglesias
 * @link		https://github.com/pauiglesias/wp-helpers
 */
class Config {



	/**
	 * @var array $data Stores the last data retrieved from the configuration file.
	 */
	private $data;



	/**
	 * @var string $filename The name of the configuration file to be loaded.
	 */
	private $filename;



	/**
	 * @var string $subdirectory The subdirectory where the configuration file is located, relative to the module's root directory.
	 */
	private $subdirectory;



	/**
	 * Constructor
	 *
	 * Initializes the Config class with the filename and subdirectory.
	 *
	 * @param string $filename The name of the configuration file. Defaults to 'config.php'.
	 * @param string $subdirectory The subdirectory where the file is located. Defaults to an empty string, which implies the root directory.
	 */
	public function __construct($filename = 'config.php', $subdirectory = '') {
		$this->filename = ltrim($filename, DIRECTORY_SEPARATOR);
		$this->subdirectory = $this->prepareSubdirectory($subdirectory);
	}



	/**
	 * Prepare subdirectory data
	 *
	 * Processes the provided subdirectory path, ensuring it has the correct
	 * directory separators at the beginning and end.
	 *
	 * @param string $subdirectory The subdirectory path to be processed.
	 * @return string The processed subdirectory path with leading and trailing directory separators.
	 */
	private function prepareSubdirectory($subdirectory) {

		$subdirectory = trim($subdirectory, DIRECTORY_SEPARATOR);

		if ('' === $subdirectory) {
			return DIRECTORY_SEPARATOR;
		}

		return DIRECTORY_SEPARATOR.$subdirectory.DIRECTORY_SEPARATOR;
	}



	/**
	 * Retrieve data
	 *
	 * Returns the configuration data. If the data has not been loaded yet,
	 * it will load it from the file first.
	 *
	 * @return array The configuration data.
	*/
	public function data() {
		return isset($this->data) ? $this->data : $this->load();
	}



	/**
	 * Load the configuration data from the file
	 *
	 * Attempts to load the configuration file from the specified path.
	 * If the file exists and contains valid data, it will be stored in the `$data` property.
	 *
	 * @return array The configuration data loaded from the file. If the file is not found or contains invalid data, an empty array is returned.
	 */
	private function load() {

		$this->data = [];

		$path = rtrim(Module::dir(), DIRECTORY_SEPARATOR).$this->subdirectory.$this->filename;
		if (!file_exists($path)) {
			return $this->data;
		}

		$dataTest = @include($path);
		if (empty($dataTest) || !is_array($dataTest)) {
			return $this->data;
		}

		$this->data = $dataTest;
		return $this->data;
	}



}