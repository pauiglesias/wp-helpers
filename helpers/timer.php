<?php

namespace MicroDeploy\Package\Helpers;

/**
 * Timer class
 *
 * @package WordPress
 * @subpackage Helpers
 */
class Timer {



	/**
	 * Start of this timer
	 */
	private $started;



	/**
	 * Last elapsed time request
	 */
	private $ended;



	/**
	 * Constructor
	 */
	public function __construct() {
		$this->start();
	}



	/**
	 * Start the timer
	 */
	public function start() {
		$this->started = microtime(true);
	}



	/**
	 * Seconds from started
	 */
	public function seconds() {
		$this->ended = microtime(true);
		return (int) ($this->ended - $this->started);
	}



}