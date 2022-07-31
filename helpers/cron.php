<?php

namespace MicroDeploy\Package\Helpers;

/**
 * Cron class
 *
 * @package WordPress
 * @subpackage Helpers
 */
class Cron {



	/**
	 * Config array
	 */
	private $config;



	/**
	 * Constructor
	 */
	public function __construct($args) {
		$this->init($args);
		$this->start();
	}



	/**
	 * Init configuration
	 */
	private function init($args) {
		$this->config = wp_parse_args($args, [
			'interval'		=> 60,
			'display'		=> 'Every minute',
			'secondsOffset'	=> 30,
			'actionKey'		=> '',
			'scheduleKey'	=> '',
			'callback'		=> null,
		]);
	}



	/**
	 * Start the cron processes
	 */
	private function start() {
		$this->action();
		$this->schedules();
		$this->scheduled();
	}



	/**
	 * Triggers the scheduled action
	 */
	private function action() {
		add_action($this->actionKey(), $this->config['callback']);
	}



	/**
	 * Minute schedule
	 */
	private function schedules() {
		add_filter('cron_schedules', function($schedules) {
			$schedules[$this->scheduleKey()] = [
				'interval' => $this->config['interval'],
				'display'  => $this->config['display'],
			];
			return $schedules;
		});
	}



	/**
	 * Check scheduled event
	 */
	private function scheduled() {
		if (!wp_next_scheduled($this->actionKey())) {
			wp_schedule_event(time() + $this->config['secondsOffset'], $this->scheduleKey(), $this->actionKey());
		}
	}



	/**
	 * Composes schedule key
	 */
	private function scheduleKey() {
		return Util::key($this->config['actionKey']);
	}



	/**
	 * Composes action key
	 */
	private function actionKey() {
		return Util::key($this->config['scheduleKey']);
	}



}