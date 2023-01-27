<?php

namespace MicroDeploy\Package\Helpers;

/**
 * Cron class
 *
 * @package		WordPress
 * @subpackage	Helpers
 * @version		1.0.0
 */
class Cron {



	/**
	 * Interval and offset
	 */
	const REPEAT_INTERVAL = 60;
	const SCHEDULE_OFFSET = 30;



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
			'interval'			=> self::REPEAT_INTERVAL,
			'interval_display'	=> sprintf(__('%s seconds'), self::REPEAT_INTERVAL),
			'seconds_offset'	=> self::SCHEDULE_OFFSET,
			'action_key'		=> '',
			'schedule_key'		=> '',
			'callback'			=> null,
		]);
	}



	/**
	 * Start the cron processes
	 */
	private function start() {
		$this->action();
		$this->interval();
		$this->schedule();
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
	private function interval() {
		add_filter('cron_schedules', function($schedules) {
			$schedules[$this->scheduleKey()] = [
				'interval' => $this->config['interval'],
				'display'  => $this->config['interval_display'],
			];
			return $schedules;
		});
	}



	/**
	 * Check scheduled event
	 */
	private function schedule() {
		if (!wp_next_scheduled($this->actionKey())) {
			wp_schedule_event(time() + $this->config['seconds_offset'], $this->scheduleKey(), $this->actionKey());
		}
	}



	/**
	 * Composes schedule key
	 */
	private function scheduleKey() {
		return Util::key($this->config['action_key']);
	}



	/**
	 * Composes action key
	 */
	private function actionKey() {
		return Util::key($this->config['schedule_key']);
	}



}