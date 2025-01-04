<?php
/**
 * Preview script for html markup generator
 *
 * @package tutor-droip-elements
 */

namespace TutorLMSDroip;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Iframe
 */
class Iframe {

	/**
	 * Class constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$post_id = isset( $_GET['post_id'] ) ? $_GET['post_id'] : null;
		if ( $post_id !== null ) {
			$post = get_post( $post_id );
			if ( $post && ( $post->post_type === TDE_APP_PREFIX . '-course-template' || $post->post_type === TDE_APP_PREFIX . '-courses-template' ) ) {
				// add_action( 'template_include', array( $this, 'load_page_template' ) );
				add_action( 'template_redirect', array( $this, 'load_page_template' ) );
				add_filter( 'droip_assets_should_load', array( $this, 'load_droip_assets' ) );
			}
		}
	}

	/**
	 * Load droip page template
	 * it will include the template file insted of original template file
	 * $loadForIframe = true if load for iframe
	 *
	 * @param string $original wp action for template file load.
	 * @return string template name.
	 */
	public static function load_page_template() {
		get_header() . the_content() . get_footer();
		exit();
	}


	/**
	 * Load droip assets
	 *
	 * @param bool $default_value default value.
	 * @return bool
	 * @since 1.0.0
	 */
	public function load_droip_assets( $default_value ) {
		return true;
	}
}
