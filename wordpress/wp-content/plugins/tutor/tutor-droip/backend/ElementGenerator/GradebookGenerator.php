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

/**
 * Class GradebookGenerator
 * This class is used to define all helper functions.
 */
trait GradebookGenerator {

	/**
	 * Generate course gradebook markup
	 *
	 * @return string
	 */
	private function generate_course_gradebook_markup() {
		switch ( $this->element['name'] ) {
			case TDE_APP_PREFIX . '-gradebook':
				$course_id = isset( $this->options['post'] ) ? $this->options['post']->ID : get_the_ID();
				$grades    = get_generated_gradebook( 'all', $course_id );

				$child = is_array( $grades ) && count( $grades ) ?
					call_user_func( $this->generate_child_element, $this->element['children'][1], $this->options ) :
					call_user_func( $this->generate_child_element, $this->element['children'][0], $this->options );

				return "<div $this->attributes>$child</div>";

			case TDE_APP_PREFIX . '-has-gradebook':
				if ( function_exists( 'TUTOR_GB' ) ) {
					$grade_book_template = trailingslashit( TUTOR_GB()->path ) . 'views/gradebook.php';
					if ( file_exists( $grade_book_template ) ) {
						$course_id = isset( $this->options['post'] ) ? $this->options['post']->ID : get_the_ID();

						ob_start();
						require_once $grade_book_template;

						return ob_get_clean();
					}
				}
				return '';
			case TDE_APP_PREFIX . '-no-gradebook':
				return $this->generate_common_element();

		}
	}
}
