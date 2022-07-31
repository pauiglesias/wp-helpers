<?php

namespace MicroDeploy\Package\Admin;

use \MicroDeploy\Package\Helpers\Singleton;
use \MicroDeploy\Package\Helpers\Util;

/**
 * Columns class
 *
 * @package WP-Helpers
 * @subpackage Admin
 */
final class Columns extends Singleton {



	/**
	 * Adds custom columns
	 */
	public function columnsHead($columns) {

		$columnKey = Util::key('my_column_key');
		if (isset($columns[$columnKey])) {
			return $columns;
		}

		$columns2 = [];

		foreach ($columns as $key => $value) {
			$columns2[$key] = $value;
			if ('title' == $key) {
				$columns2[$columnKey] = 'My Column';
			}
		}

		return $columns2;
	}



	/**
	 * Content column for each post
	 */
	public function columnsBody($columnName, $postId) {

		if (Util::key('my_column_key') == $columnName) {

			$value = Util::meta($postId, 'post_meta_key');
			if (empty($value)) {
				echo '&mdash;';
				return;
			}

			echo esc_html($value);
		}
	}



}