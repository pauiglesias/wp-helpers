<?php

namespace MicroDeploy\Package\Helpers;

/**
 * Upload class
 *
 * Uploads a file from a given Url to the WordPress Media Library.
 *
 * @package		WordPress
 * @subpackage	Helpers
 * @version		1.0.0
 * @license		GPLv3
 * @author		Pau Iglesias
 * @link		https://github.com/pauiglesias/wp-helpers
 */
class Upload {



	/**
	 * Upload a file to the media library using a URL.
	 *
	 * Create from this code fragment:
	 * https://gist.github.com/RadGH/966f8c756c5e142a5f489e86e751eacb
	 *
	 * @param string		$url		URL to be uploaded
	 * @param array			$args 		{
	*
	 * 		Optional arguments:
	 *
	 * 			@type 	integer|null	$post_id	The post to be associated with
	 * 			@type	array			$post_data	Post data to override in media_handle_sideload function
	 * 			@type	string|null		$title		The title of the media item
	 *
	 * @return int|false The attachment Id or false if failed
	 */
	public static function fromUrl($url, $args = []) {

		$args = wp_parse_args($args, [
			'post_id'	=> null,
			'post_data'	=> [],
			'title'		=> null,
		]);

		require_once ABSPATH.'/wp-admin/includes/image.php';
		require_once ABSPATH.'/wp-admin/includes/file.php';
		require_once ABSPATH.'/wp-admin/includes/media.php';

		$tmp = download_url($url);

		if (empty($tmp)) {
			self::error(new \WP_Error('tmp', 'Unable to create the temporary file.'));
			return false;
		}

		if (is_wp_error($tmp)) {
			self::error($tmp);
			return false;
		}

		$filename = pathinfo($url, PATHINFO_FILENAME);
		$extension = pathinfo($url, PATHINFO_EXTENSION);

		/**
		 * An extension is required or else WordPress will reject the upload
		 */
		if (empty($extension)) {

			$mime = mime_content_type($tmp);
			$mime = is_string($mime) ? sanitize_mime_type($mime) : false;

			/**
			 * Only allow certain mime types because mime types do not always end in a valid extension (see the .doc example below)
			 */
			$mime_extensions = array(
				// mime_type         => extension (no period)
				'text/plain'         => 'txt',
				'text/csv'           => 'csv',
				'application/msword' => 'doc',
				'image/jpg'          => 'jpg',
				'image/jpeg'         => 'jpeg',
				'image/gif'          => 'gif',
				'image/png'          => 'png',
				'video/mp4'          => 'mp4',
			);

			if (!isset($mime_extensions[$mime])) {
				@unlink($tmp);
				self::error(new \WP_Error('mime', 'Unable to detect the file mime type.'));
				return false;
			}

			$extension = $mime_extensions[$mime];
		}

		$mediaArgs = array(
			'name'		=> "$filename.$extension",
			'tmp_name'	=> $tmp,
		);

		$attachmentId = media_handle_sideload($mediaArgs, empty($args['post_id']) ? 0 : $args['post_id'], $args['title'], $args['post_data']);

		@unlink($tmp);

		if (empty($attachmentId)) {
			self::error(new \WP_Error('media', 'Undefined error on trying to upload Url to the Media Library'));
			return false;
		}

		if (is_wp_error($attachmentId)) {
			self::error($attachmentId);
			return false;
		}

		return (int) $attachmentId;
	}



	/**
	 * Stores or retrieve the last error
	 *
	 * @return null|WP_Error
	 */
	public static function error($value = null) {
		static $error;
		if (isset($value)) {
			$error = $value;
		}
		return $error;
	}



}