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
 * Class ActionboxGenerator
 * This class is used to define all helper functions.
 */
trait TopicsGenerator {

	/**
	 * Generate topics markup
	 */
	private function generate_topics_markup() {
		switch ( $this->element['name'] ) {
			case TDE_APP_PREFIX . '-topics':
				$course_id = isset( $this->options['post'] ) ? $this->options['post']->ID : get_the_ID();
				$topics = tutor_utils()->get_topics($course_id);

				$child = ! is_null( $topics ) && count( $topics->posts ) > 0 ?
					call_user_func( $this->generate_child_element, $this->element['children'][1], $this->options ) :
					call_user_func( $this->generate_child_element, $this->element['children'][0], $this->options );

				return "<div $this->attributes>$child</div>";

			case TDE_APP_PREFIX . '-has-topics':
				return $this->generate_common_element();

			case TDE_APP_PREFIX . '-no-topics':
				return $this->generate_common_element();

		}
	}
}
