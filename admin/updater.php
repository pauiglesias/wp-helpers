<?php

namespace MicroDeploy\Package\Admin;

use \MicroDeploy\Package\Helpers\Module;
use \MicroDeploy\Package\Helpers\Notices;
use \MicroDeploy\Package\Helpers\Singleton;
use \MicroDeploy\Package\Helpers\Util;

/**
 * Updater class
 *
 * @package WP-Helpers
 * @subpackage Admin
 */
final class Updater extends Singleton {



	private $isEmptyProduct = false;



	public function update($postId, $post) {

		if (defined('DOING_AUTOSAVE') &&
			DOING_AUTOSAVE) {
			return;
		}

		if (wp_is_post_revision($postId)) {
			return;
		}

		if (!isset($_POST['ID']) ||
			$postId !== (int) $_POST['ID']) {
			return;
		}

		if (null === Util::postParam('product_nonce') ||
			!Util::postParam('product_action')) {
			return;
		}

		if (!wp_verify_nonce(Util::postParam('product_nonce'), Module::file())) {
			wp_die('Credenciales de seguridad inválidas. Vuelva a intentarlo.');
			return;
		}

		static $processed = [];
		if (isset($processed[$postId])) {
			return;
		}

		$processed[$postId] = true;

		clean_post_cache($postId);

		$post = get_post($postId);
		if (!is_object($post)) {
			return;
		}

		// Code...
	}



	public function maybeEmpty($maybeEmpty, $postarr) {

		if (null === Util::postParam('post_nonce') ||
			null === Util::postParam('product_jarl')) {
			return $maybeEmpty;
		}

		$this->isEmptyProduct = true;
		return false;
	}



}