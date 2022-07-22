<?php

namespace MicroDeploy\Package\Admin;

use \MicroDeploy\Package\Helpers\Module;
use \MicroDeploy\Package\Helpers\Notices;
use \MicroDeploy\Package\Helpers\Singleton;
use \MicroDeploy\Package\Helpers\Util;

/**
 * Metabox class
 *
 * @package WP-Helpers
 * @subpackage Admin
 */
final class Metabox extends Singleton {



	/**
	 * Declare metaboxes
	 */
	public function metaboxes($postType, $post) {
		add_meta_box(Util::key('myMetaboxKey'), 'Metabox name', [$this, 'metaboxContent'], 'my-screen', 'advanced', 'high');
	}



	/**
	 * Show the metabox data
	 */
	public function metaboxContent($post) {

		// Some data retrieval ?>

		<input type="hidden" name="<?php Util::escAttr_('metabox_nonce'); ?>" value="<?php echo esc_attr(wp_create_nonce(Module::file())); ?>">

		<?php
	}



}