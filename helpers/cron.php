<?php

namespace MicroDeploy\Package\Helpers;

/**
 * Cron class for scheduling recurring events in WordPress.
 *
 * This class provides a simple interface for setting up and managing recurring events
 * using the WordPress Cron API. It can be used to schedule actions to occur at specified
 * intervals, and to ensure that those actions are executed reliably and efficiently.
 *
 * @package		WordPress
 * @subpackage	Helpers
 * @version		1.0.0
 * @license		GPLv3
 * @author		Pau Iglesias
 * @link		https://github.com/pauiglesias/wp-helpers
 */
class Cron {



	/**
	 * The number of seconds between each iteration of the cron job.
	 *
	 * @var int
	 */
	const REPEAT_INTERVAL = 60;



	/**
	 * The number of seconds to offset the first run of the cron job.
	 *
	 * @var int
	 */
	const SCHEDULE_OFFSET = 30;



	/**
	 * An array of configuration options for the cron job.
	 *
	 * @var array
	 */
	private $config;



	/**
	 * Constructor for the Cron class.
	 *
	 * @param array $args An array of configuration options for the cron job.
	 */
	public function __construct($args) {
		$this->init($args);
		$this->start();
	}



	/**
	 * Initializes the configuration options for the cron job.
	 *
	 * @param array $args An array of configuration options for the cron job.
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
	 * Starts the cron job by setting up the scheduled actions.
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
	 * Adds a custom interval to the cron schedules.
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
	 * Checks if the scheduled event has been set and schedules it if it has not.
	 */
	private function schedule() {
		if (!wp_next_scheduled($this->actionKey())) {
			wp_schedule_event(time() + $this->config['seconds_offset'], $this->scheduleKey(), $this->actionKey());
		}
	}



	/**
	 * Composes the schedule key for the cron job.
	 *
	 * @return string The schedule key for the cron job.
	 */
	private function scheduleKey() {
		return Util::key($this->config['action_key']);
	}



	/**
	 * Composes the action key for the cron job.
	 *
	 * @return string The action key for the cron job.
	 */
	private function actionKey() {
		return Util::key($this->config['schedule_key']);
	}



}