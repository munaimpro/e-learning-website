<?php
/**
 * Preview script for html markup generator
 *
 * @package tutor-droip-elements
 */

namespace TutorLMSDroip;

use TutorLMSDroip\ElementGenerator\ElementGenerator;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Backend
 *
 * @package TutorLMSDroip
 */
class Backend {

	/**
	 * Backend constructor.
	 */
	public function __construct() {
		$this->run();
	}

	/**
	 * Run the backend
	 */
	public function run() {
		//phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$action = sanitize_text_field( isset( $_GET['action'] ) ? $_GET['action'] : null );
		if ( 'droip' === $action ) {
			$load_for = sanitize_text_field( isset( $_GET['load_for'] ) ? wp_unslash( $_GET['load_for'] ) : null );
			if ( 'droip-iframe' === $load_for ) {
				new Iframe();
			} else {
				new Editor();
			}
		}
		new ElementGenerator();
		new Pages();
	}
}
