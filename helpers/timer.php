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
	 *
	 * @param int   		$precision The number of digits from the right of the decimal to display.
 	 *						Default 3.
	 *
	 * @return int|float 	The seconds finished time calculation.
	 */
	public function seconds($precision = 0) {

		$this->ended = microtime(true);
		$total = $this->ended - $this->started;

		if (empty($precision)) {
			return (int) $total;
		}

		return (float) number_format($total, $precision);
	}



}