<?php
/**
 * Preview script for html markup generator
 *
 * @package tutor-droip-elements
 */

namespace TutorLMSDroip\ElementGenerator;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
trait ShareGenerator {

	/**
	 * Generate Course Share elements
	 */
	private function generate_course_share_markup() {
		switch ( $this->element['name'] ) {

			case TDE_APP_PREFIX . '-share-course':
				$course_share = tutor_utils()->get_option( 'enable_course_share', false, true, true );
				if ( ! $course_share ) {
					return '';
				}

				tutor_load_template_from_custom_path( tutor()->path . '/views/course-share.php', array( 'show_share_button' => false ), false );

				$children_html = $this->generate_child_elements();
				return "<div $this->attributes>$children_html</div>";

		}
	}
}
