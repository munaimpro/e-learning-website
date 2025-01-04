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

trait WishListGenerator {

	/**
	 * Generate Wish List elements
	 *
	 * @return string
	 */
	private function generate_wish_list_elements() {
		$ele_name = $this->element['name'];
		switch ( $this->element['name'] ) {
			case TDE_APP_PREFIX . '-wish-list':
				$course_id      = isset( $this->options['post'] ) ? $this->options['post']->ID : get_the_ID();
				$is_wish_listed = tutor_utils()->is_wishlisted( $course_id );
				$is_wish_listed = $is_wish_listed ? 'true' : 'false';
				$children_html  = $this->generate_child_elements();
				return "<a href='javascript:void(0);' $this->attributes data-course_id='$course_id' data-wishlist='$is_wish_listed' data-ele_name='$ele_name'>$children_html</a>";

			case TDE_APP_PREFIX . '-wish-list-normal':
				if ( ! is_user_logged_in() ) {
					return '';
				}
				$course_id      = isset( $this->options['post'] ) ? $this->options['post']->ID : get_the_ID();
				$is_wish_listed = tutor_utils()->is_wishlisted( $course_id );
				return $this->generate_common_element( $is_wish_listed );

			case TDE_APP_PREFIX . '-wish-list-loading':
				if ( ! is_user_logged_in() ) {
					return '';
				}
				return $this->generate_common_element( true );// alaways hide

			case TDE_APP_PREFIX . '-wish-list-wishlisted':
				if ( ! is_user_logged_in() ) {
					return '';
				}
				$course_id      = isset( $this->options['post'] ) ? $this->options['post']->ID : get_the_ID();
				$is_wish_listed = tutor_utils()->is_wishlisted( $course_id );
				return $this->generate_common_element( ! $is_wish_listed );

			case TDE_APP_PREFIX . '-wish-list-unauthinticate':
				if ( is_user_logged_in() ) {
					return '';
				}
				$tutor_dashboard_url = tutor_utils()->tutor_dashboard_url();
				$name                = $this->element['name'];
				$children_html       = $this->generate_child_elements();
				return "<a $this->attributes href='$tutor_dashboard_url' data-ele_name='$name'>$children_html</a>";

		}
	}
}
