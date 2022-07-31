<?php

namespace MicroDeploy\Package\Admin;

use \MicroDeploy\Package\Helpers\Module;
use \MicroDeploy\Package\Helpers\Notices;
use \MicroDeploy\Package\Helpers\Singleton;
use \MicroDeploy\Package\Helpers\Util;

/**
 * Settings class
 *
 * @package WP-Helpers
 * @subpackage Admin
 */
final class Settings extends Singleton {



	/**
	 * Handle form submit
	 */
	public function handle() {

		if (!$this->verifyNonce('nonce_settings')) {
			return;
		}

		// Update code or function call ...

		Notices::success('Data successfully updated');
	}



	/**
	 * Verifies submit nonce
	 */
	private function verifyNonce($key) {

		if (!wp_verify_nonce(Util::postParam($key), Module::file())) {
			Notices::error('Not valid security credentials. Please try again.');
			return false;
		}

		return true;
	}



	/**
	 * Display page content
	 */
	public function display() {

		$nonce = esc_attr(wp_create_nonce(Module::file()));

		?><div class="wrap">

			<h1 style="margin-bottom: 25px;">WC STEL Order API</h1>

			<form method="post" style="margin-bottom: 50px;">

				<h2>Section</h2>

				<input type="hidden" name="<?php Util::escAttr_('nonce_settings'); ?>" value="<?php echo $nonce; ?>">
				<input type="hidden" name="<?php Util::escAttr_('job1'); ?>" value="1">

				<input type="submit" class="button button-primary" value="Save Data">

			</form>

			<form method="post" style="margin-bottom: 50px;">

				<h2>Section 2</h2>

				<input type="hidden" name="<?php Util::escAttr_('nonce_settings'); ?>" value="<?php echo $nonce; ?>">
				<input type="hidden" name="<?php Util::escAttr_('job2'); ?>" value="1">

				<input type="submit" class="button button-primary" value="Save Data">

			</form>

		</div><?php

	}



}