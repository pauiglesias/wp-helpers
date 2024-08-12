<?php

namespace MicroDeploy\Package\Helpers;

/**
 * Ping class
 *
 * Handles the creation and verification of ping timestamps to monitor process status.
 *
 * This class allows you to save a ping timestamp and verify its validity by checking
 * whether it has expired based on a defined timeout. It uses the `Util` class to interact
 * with WordPress options for storing and retrieving these timestamps.
 *
 * @uses		Util class for interacting with WordPress options
 *
 * @package		WordPress
 * @subpackage	Helpers
 * @version		1.0.0
 * @license		GPLv3
 * @author		Pau Iglesias
 * @link		https://github.com/pauiglesias/wp-helpers
 */
class Ping {



	/**
	 * The option name used to store the ping timestamp in the WordPress database.
	 *
	 *  @var string
	 */
	private $name;



	/**
	 * The timeout period in seconds after which the ping is considered expired.
	 *
	 * @var int
	 */
	private $timeout;



	/**
	 * Constructor
	 *
	 * Initializes the Ping class with the given option name and timeout.
	 *
	 * @param string $name The option name for storing the ping timestamp.
	 * @param int $timeout The timeout duration in seconds.
	 */
	public function __construct($name, $timeout) {
		$this->name = $name;
		$this->timeout = $timeout;
	}



	/**
	 * Updates the ping timestamp.
	 *
	 * Saves the current timestamp as the latest ping and verifies if it was successfully updated
	 * by comparing the stored value with the provided timestamp.
	 *
	 * @return bool True if the ping was successfully updated, false otherwise.
	 */
	public function ping() {
		$timestamp = time();
		Util::optionUpdate($this->name, time(), false);
		return $timestamp === $this->live();
	}



	/**
	 * Retrieves the current ping timestamp from the database.
	 *
	 * Fetches the stored ping timestamp directly from the WordPress database using a
	 * SQL query. This method bypasses the `get_option()` function of WordPress, ensuring
	 * that it retrieves a fresh value directly from the database rather than relying on
	 * any cached values.
	 *
	 * @global wpdb $wpdb WordPress database global object for performing the query.
	 *
	 * @return int The current ping timestamp as an integer.
	 */
	public function live() {
		global $wpdb;
		return (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT SQL_NO_CACHE option_value FROM $wpdb->options WHERE option_name = %s LIMIT 1",
				Util::key($this->name)
			)
		);
	}



	/**
	 * Determines if the current ping has expired.
	 *
	 * This method fetches the latest ping timestamp using the `live()` method, which
	 * retrieves a fresh value directly from the database.
	 *
	 * @return bool True if the ping has expired or if no valid ping timestamp exists; false otherwise.
	 */
	public function expired() {
		$timestamp = $this->live();
		return $timestamp <= 0 || time() - $timestamp > $this->timeout;
	}



}