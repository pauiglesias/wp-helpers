<?php

namespace MicroDeploy\Package\Helpers;

/**
 * Updater class
 *
 * Checks the plugin remote updates and performs the plugin update.
 *
 * @package WordPress
 * @subpackage Helpers
 */
class Updater {



	/**
	 * Configuration
	 */
	private $config;



	/**
	 * Constructor
	 */
	public function __construct($pluginFile, $infoJsonUrl, $args = []) {
		$this->init($pluginFile, $infoJsonUrl, $args);
		$this->hooks();
	}



	/**
	 * Initialize configuration
	 */
	public function init($pluginFile, $infoJsonUrl, $args) {

		$config = wp_parse_args([
			'plugin'				=> get_plugin_data($pluginFile),
			'plugin-basename'		=> plugin_basename($pluginFile),
			'plugin-slug'			=> basename(dirname($pluginFile)),
			'info-json-url'			=> $infoJsonUrl,
		], $args);

		$this->config = wp_parse_args($config, [
			'request-timeout'		=> 15,
			'request-headers'		=> ['Accept' => 'application/json'],
			'cache-key'				=> md5($pluginFile),
			'cache-timeout'			=> MINUTE_IN_SECONDS * 5,
			'default-requires'		=> '5.0',
			'default-tested'		=> '5.0',
			'default-requires_php'	=> '5.6',
		]);

/* // Debug point
$this->config['cache-key'] = null; */

	}



	/**
	 * WP Hooks
	 */
	private function hooks() {
		add_filter('plugins_api', [$this, 'pluginsApi'], PHP_INT_MAX, 3);
		add_filter('site_transient_update_plugins', [$this, 'updatePlugins']);
	}



	/**
	 * Handle `plugins_api` filter
	 */
	public function pluginsApi($result, $action, $args) {

/* // Debug point
error_log('plugin_api hook: '.$action.' - '.print_r($args, true)); */

		if ('plugin_information' !== $action) {
			return $result;
		}

		if ($this->config['plugin-slug'] !== $args->slug) {
			return $result;
		}

		$data = $this->infoJson();
		if (empty($data)) {
			return $result;
		}

/* // Debug point
print_r($data);die; */

		return $this->pluginsApiData($data);
	}



	/**
	 * Plugin info from remote data
	 */
	private function pluginsApiData($data) {

		return (object) [
			'name'				=> $data['name'],
			'version'			=> $data['version'],
			'download_link'		=> $data['download_url'],
			'slug'				=> empty($data['slug'])				? $this->config['plugin-slug']		: $data['slug'],
			'requires'			=> empty($data['requires'])			? $this->config['default-requires'] : $data['requires'],
			'tested'			=> empty($data['tested'])			? $this->config['default-tested']	: $data['tested'],
			'author'			=> empty($data['author'])			? null								: $data['author'],
			'author_profile'	=> empty($data['author_homepage'])	? null								: $data['author_homepage'],
			'last_updated'		=> empty($data['last_updated'])		? null								: $data['last_updated'],
			'short_description' => empty($data['upgrade_notice'])	? null								: $data['upgrade_notice'],
			'homepage'			=> empty($data['homepage'])			? null								: $data['homepage'],
			'sections'			=> $this->pluginsApiDataSections($data),
		];
	}



	/**
	 * Plugin sections data detail
	 */
	private function pluginsApiDataSections($data) {
		return [
			'description'	=> empty($data['sections']) || empty($data['sections']['description'])	? null : $data['sections']['description'],
			'installation'	=> empty($data['sections']) || empty($data['sections']['installation'])	? null : $data['sections']['installation'],
			'changelog'		=> empty($data['sections']) || empty($data['sections']['changelog'])	? null : $data['sections']['changelog'],
		];
	}



	/**
	 * Check the update plugins transient
	 */
	public function updatePlugins($transient) {

		if (empty($transient->checked)) {
			return $transient;
		}

		$data = $this->infoJson();
		if (empty($data)) {
			return $transient;
		}

		if (!$this->outdated($data)) {
			return $transient;
		}

		if (!$this->supportsWpVersion($data)) {
			return $transient;
		}

		if (!$this->supportsPhpVersion($data)) {
			return $transient;
		}

		$transient->response[$this->config['plugin-basename']] = $this->updatePluginsData($data);

		return $transient;
	}



	/**
	 * Check if the plugin is outdated
	 */
	private function outdated($data) {
		return empty($data['version']) || empty($this->config['plugin']['Version']) ||
		version_compare($data['version'], $this->config['plugin']['Version'], '>');
	}



	/**
	 * Check the WP version requirement
	 */
	private function supportsWpVersion($data) {
		return empty($data['requires']) ||
		version_compare($data['requires'], get_bloginfo('version'), '<=');
	}



	/**
	 * Check the PHP version requirement
	 */
	private function supportsPhpVersion($data) {
		return empty($data['requires_php']) ||
		version_compare($data['requires_php'], PHP_VERSION, '<=');
	}



	/**
	 * Prepare transiente resource
	 */
	private function updatePluginsData($data) {
		return (object) [
			'slug'			=> empty($data['slug']) ? $this->config['plugin-slug'] : $data['slug'],
			'new_version'	=> $data['version'],
			'tested'		=> empty($data['tested']) ? null : $data['tested'],
			'package'		=> $data['download_url'],
		];
	}



	/**
	 * Handle the info json data
	 */
	private function infoJson() {

		$data = $this->cache();
		if (!empty($data)) {
			return $data;
		}

		$json = $this->request();
		if (empty($json)) {
			return false;
		}

		$data = @json_decode($json, true);
		if (empty($data)) {
			return false;
		}

		$this->cache($json);

		return $data;
	}



	/**
	 * Check the option cache
	 */
	private function cache($json = null) {

		if (empty($this->config['cache-key'])) {
			return false;
		}

		if (isset($json)) {
			return Prefix::optionUpdate($this->config['cache-key'], $this->jsonEncode($json), false);
		}

		$test = @json_decode(Prefix::option($this->config['cache-key']), true);
		if (empty($test) || empty($test['timestamp']) || empty($test['json'])) {
			return false;
		}

		if ($test['timestamp'] + $this->config['cache-timeout'] <= time()) {
			return false;
		}

		return @json_decode($test['json'], true);
	}



	/**
	 * Encodes the option cache data
	 */
	private function jsonEncode($json) {
		return @json_encode(['timestamp' => time(), 'json' => $json], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
	}



	/**
	 * Remote request
	 */
	private function request() {

		$response = wp_remote_get($this->config['info-json-url'], $this->requestArgs());
		if (is_wp_error($response)) {
			return false;
		}

		if (200 !== wp_remote_retrieve_response_code($response)) {
			return false;
		}

		return trim(wp_remote_retrieve_body($response));
	}



	/**
	 * Prepare request arguments
	 */
	private function requestArgs() {
		return [
			'timeout'	=> $this->config['request-timeout'],
			'headers'	=> $this->config['request-headers'],
		];
	}



}