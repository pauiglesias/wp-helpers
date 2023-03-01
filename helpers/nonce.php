<?php

namespace MicroDeploy\Package\Helpers;

/**
 * Nonce class
 *
 * Generates WP nonces without the need for logged-in users in the WordPress admin.
 *
 * @package		WordPress
 * @subpackage	Helpers
 * @version		1.0.0
 * @license		GPLv3
 * @author		Pau Iglesias
 * @link		https://github.com/pauiglesias/wp-helpers
 */
class Nonce {



	/**
	 * Single class instance
	 */
	private static $instance;



	/**
	 * Custom salt string
	 */
	private $salt;



	/**
	 * Internal random value
	 */
	private $randomValue = '';



	/**
	 * Retrieve previous instance or create new one
	 */
	public static function instance($salt = null) {

		if (!isset(self::$instance)) {
			self::$instance = new self($salt);
		}

		return self::$instance;
	}



	/**
	 * Constructor
	 * Allows to initialize without the "instance" pattern
	 */
	public function __construct($salt = null) {

		if (isset($salt)) {
			$this->salt = $salt;
		}
	}



	/**
	 * Create a new, user-independent, 24H valid nonce
	 */
	public function create($action = '') {
		return substr($this->getHash($this->tick().'|'.$action.'|'.__FILE__), -12, 10);
	}



	/*
	 * Verify a given nonce
	 */
	public function verify($nonce, $action = '') {

		$nonce = (string) $nonce;
		if (empty($nonce)) {
			return false;
		}

		$tick = $this->tick();

		// Nonce generated 0-12 hours ago
		$expected = substr($this->getHash($tick.'|'.$action.'|'.__FILE__), -12, 10);
		if ($this->hashEquals($expected, $nonce)) {
			return 1;
		}

		// Nonce generated 12-24 hours ago
		$expected = substr($this->getHash(($tick - 1).'|'.$action.'|'.__FILE__), -12, 10);
		return $this->hashEquals($expected, $nonce)? 2 : false;
	}



	/**
	 * Get system hash based on current salt
	 */
	public function getHash($data) {
		$salt = $this->getSalt();
		return $this->hashHmac('md5', $data, $salt);
	}



	/**
	 * Generates a random number
	 **/
	public function getRand($min = 0, $max = 0) {

		// Reset random value after 14 uses
		// 32(md5) + 40(sha1) + 40(sha1) / 8 = 14 random numbers from $rnd_value
		if (strlen($this->randomValue) < 8) {
			$this->randomValue = md5(uniqid(microtime().mt_rand(), true));
			$this->randomValue .= sha1($this->randomValue);
			$this->randomValue .= sha1($this->randomValue);
		}

		// Take the first 8 digits for our value
		$value = substr($this->randomValue, 0, 8);

		// Strip the first eight, leaving the remainder for the next call to wp_rand().
		$this->randomValue = substr($this->randomValue, 8);

		// Cast to absolute decimal value
		$value = abs(hexdec($value));

		// Some misconfigured 32bit environments (Entropy PHP, for example) truncate integers larger than PHP_INT_MAX to PHP_INT_MAX rather than overflowing them to floats.
		$max_random_number = 3000000000 === 2147483647 ? (float) "4294967295" : 4294967295; // 4294967295 = 0xffffffff

		// Reduce the value to be within the min - max range
		if ($max != 0) {
			$value = $min + ($max - $min + 1) * $value / ($max_random_number + 1);
		}

		// Done
		return abs(intval($value));
	}



	/**
	 * Password generation
	 */
	public function generatePassword($length = 12, $special_chars = true, $extra_special_chars = false) {

		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

		if ($special_chars) {
			$chars .= '!@#$%^&*()';
		}

		if ($extra_special_chars) {
			$chars .= '-_ []{}<>~`+=,.;:/?|';
		}

		$password = '';
		for ($i = 0; $i < $length; $i++) {
			$password .= substr($chars, $this->getRand(0, strlen($chars) - 1), 1);
		}

		return $password;
	}



	/* Internal functions */



	/**
	 * Creates a "tick", time based
	 * This is a clone of WP nonce_tick function without constants and filters
	 */
	private function tick() {
		return ceil(time()/(86400/2));
	}



	/**
	 * Unique salt to hash strings
	 */
	private function getSalt() {

		// Process cache
		static $cached_salt;
		if (isset($cached_salt)) {
			return $cached_salt;
		}

		// Salt from file
		$salt = isset($this->salt)? $this->salt : $this->getSaltFromWPConstants();
		if (empty($salt)) {
			$salt = php_uname().'|'.phpversion().'|'.__FILE__;
		}

		// Store hash
		$cached_salt = md5($salt);

		// Done
		return $cached_salt;
	}



	/**
	 * Return salt from constants defined on config file
	 */
	private function getSaltFromWPConstants() {

		// Prepare combinations
		$suffixes = array('KEY', 'SALT');
		$prefixes = array('AUTH', 'SECURE_AUTH', 'LOGGED_IN', 'NONCE', 'SECRET');

		// Initialize
		$salt = array();
		$duplicated_keys = array('put your unique phrase here' => true);

		// Enum prefixes
		foreach ($prefixes as $prefix) {

			// Enum suffixes
			foreach ($suffixes as $suffix) {

				// Check constant
				if (!defined("{$prefix}_{$suffix}")) {
					continue;
				}

				// Add value to duplicates array
				$value = constant("{$prefix}_{$suffix}");
				$duplicated_keys[$value] = isset($duplicated_keys[$value]);
			}
		}

		// Second round
		foreach ($prefixes as $prefix) {

			// Enum suffixes
			foreach ($suffixes as $suffix) {

				// Check constant
				if (!defined("{$prefix}_{$suffix}")) {
					continue;
				}

				// Extract and check value
				$value = constant("{$prefix}_{$suffix}");
				if (empty($duplicated_keys[$value]) && !isset($salt[$suffix])) {
					$salt[$suffix] = $value;
				}
			}
		}

		// Done
		return empty($salt)? false : implode('', array_values($salt));
	}



	/**
	 * Wrapper for hash_hmac function
	 */
	private function hashHmac($algo, $data, $key, $raw_output = false) {

		// Check existing function
		if (function_exists('hash_hmac')) {
			return hash_hmac($algo, $data, $key, $raw_output);
		}

		/* From WP */

		$packs = array('md5' => 'H32', 'sha1' => 'H40');

		if (!isset($packs[$algo])) {
			return false;
		}

		$pack = $packs[$algo];

		if (strlen($key) > 64) {
			$key = pack($pack, $algo($key));
		}

		$key = str_pad($key, 64, chr(0));

		$ipad = (substr($key, 0, 64) ^ str_repeat(chr(0x36), 64));
		$opad = (substr($key, 0, 64) ^ str_repeat(chr(0x5C), 64));

		$hmac = $algo($opad.pack($pack, $algo($ipad.$data)));

		if ($raw_output) {
			return pack($pack, $hmac);
		}

		return $hmac;
	}



	/**
	 * Wrapper for hash_equals function
	 */
	private function hashEquals($a, $b) {

		// Check existing function
		if (function_exists('hash_equals')) {
			return hash_equals($a, $b);
		}

		/* From WP */

		$a_length = strlen($a);
		if ($a_length !== strlen($b)) {
			return false;
		}

		$result = 0;

		// Do not attempt to "optimize" this.
		for ($i = 0; $i < $a_length; $i++) {
			$result |= ord($a[$i]) ^ ord($b[$i]);
		}

		return $result === 0;
	}



}