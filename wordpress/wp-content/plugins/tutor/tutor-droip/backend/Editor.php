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
 * Class Editor
 * This class is used to define all editor functions.
 */
class Editor {

	/**
	 * Class constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'load_assets' ), 100 );
	}

	/**
	 * Load assets
	 *
	 * @since 1.0.0
	 */
	public function load_assets() {
		wp_enqueue_script( TDE_APP_PREFIX . '-tutor-droip-elements', TDE_PLUGIN_ROOT_BASE . 'build/js/editor.min.js', array( 'wp-i18n', 'droip-editor' ), '1.0.0', true );
		wp_enqueue_style( TDE_APP_PREFIX . '-tutor-droip-elements', TDE_PLUGIN_ROOT_BASE . 'build/css/editor.min.css', null, '1.0.0' );
	}
}
