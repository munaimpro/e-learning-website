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
 * Class Rating genrator
 *
 * @package TutorLMSDroip\ElementGenerator
 */
trait RatingGenerator {

	/**
	 * Generate Rating elements
	 *
	 * @return string
	 */
	private function generate_rating_elements() {
		$ele_name            = $this->element['name'];
		$course_id           = isset( $this->options['post'] ) ? $this->options['post']->ID : get_the_ID();
		$show_course_ratings = apply_filters( 'tutor_show_course_ratings', true, $course_id );
		if ( ! $show_course_ratings ) {
			return '';
		}

		switch ( $this->element['name'] ) {
			case TDE_APP_PREFIX . '-rating-active-icon':
				$current_rating = 0;
				if ( isset( $this->options['my_rating_input_generator'] ) && $this->options['my_rating_input_generator'] === true ) {
					$current_rating = 5;
				} elseif ( isset( $this->options['componentType'], $this->options['comment'] ) && $this->options['componentType'] === 'comments' ) {
						$current_rating = get_comment_meta( $this->options['comment']->comment_ID, 'tutor_rating', true );
				} else {
					$course_rating  = tutor_utils()->get_course_rating( $course_id );
					$current_rating = number_format( $course_rating->rating_avg, 2, '.', '' );
				}

				$html = '';
				for ( $i = 1; $i <= 5; $i++ ) {
					if ( $i <= round( (int) $current_rating ) ) {
						$html .= $this->generate_common_element();
					}
				}
				return $html;

			case TDE_APP_PREFIX . '-rating-inactive-icon':
				$current_rating = 0;
				if ( isset( $this->options['my_rating_input_generator'] ) && $this->options['my_rating_input_generator'] === true ) {
					$current_rating = 0;
				} elseif ( isset( $this->options['componentType'], $this->options['comment'] ) && $this->options['componentType'] === 'comments' ) {
						$current_rating = get_comment_meta( $this->options['comment']->comment_ID, 'tutor_rating', true );
				} else {
					$course_rating  = tutor_utils()->get_course_rating( $course_id );
					$current_rating = number_format( $course_rating->rating_avg, 2, '.', '' );
				}

				$html = '';
				for ( $i = 1; $i <= 5; $i++ ) {
					if ( $i > round( (int) $current_rating ) ) {
						$html .= $this->generate_common_element();
					}
				}
				return $html;

			case TDE_APP_PREFIX . '-rating-avarage-count':
				if ( isset( $this->options['tutor_review_avg_count'] ) ) {
					$settings = isset( $this->properties['settings'] ) ? $this->properties['settings'] : array();
					$template = isset( $settings['template'] ) ? $settings['template'] : '{{avarage}}';
					$html     = str_replace( '{{avarage}}', $this->options['tutor_review_avg_count'], $template );

					return $this->generate_common_element( false, $html );
				} else {
					$course_id     = isset( $this->options['post'] ) ? $this->options['post']->ID : get_the_ID();
					$course_rating = tutor_utils()->get_course_rating( $course_id );
					$settings      = isset( $this->properties['settings'] ) ? $this->properties['settings'] : array();
					$template      = isset( $settings['template'] ) ? $settings['template'] : '{{avarage}}';
					$html          = str_replace( '{{avarage}}', $course_rating->rating_avg, $template );
					return $this->generate_common_element( false, $html );
				}

			case TDE_APP_PREFIX . '-rating-total-count':
				if ( isset( $this->options['tutor_review_summery_value'] ) ) {
					$settings = isset( $this->properties['settings'] ) ? $this->properties['settings'] : array();
					$template = isset( $settings['template'] ) ? $settings['template'] : '{{avarage}}';
					$html     = str_replace( '{{count}}', $this->options['tutor_review_summery_value'], $template );

					return $this->generate_common_element( false, $html );
				} else {
					$course_rating = tutor_utils()->get_course_rating( $course_id );
					$settings      = isset( $this->properties['settings'] ) ? $this->properties['settings'] : array();
					$template      = isset( $settings['template'] ) ? $settings['template'] : '{{avarage}}';
					$html          = str_replace( '{{count}}', $course_rating->rating_count, $template );
					return $this->generate_common_element( false, $html );
				}
		}
	}

	/**
	 * Generate Review elements
	 *
	 * @return string
	 */
	private function generate_review_elements() {
		$ele_name            = $this->element['name'];
		$course_id           = isset( $this->options['post'] ) ? $this->options['post']->ID : get_the_ID();
		$show_course_ratings = apply_filters( 'tutor_show_course_ratings', true, $course_id );
		if ( ! $show_course_ratings ) {
			return '';
		}
		switch ( $this->element['name'] ) {
			case TDE_APP_PREFIX . '-rating-empty':
				$reviews = $this->get_tutor_reviews();
				if ( ! is_array( $reviews ) || ! count( $reviews ) ) {
					return $this->generate_common_element();
				} else {
					return '';
				}

			case TDE_APP_PREFIX . '-rating-not-empty':
				$reviews = $this->get_tutor_reviews();
				if ( ! is_array( $reviews ) || ! count( $reviews ) ) {
					return '';
				} else {
					// $this->options['tutor_reviews'] = $reviews;
					return $this->generate_common_element();
				}

			case TDE_APP_PREFIX . '-rating-summery':
				$course_rating = tutor_utils()->get_course_rating( $course_id );

				$html  = '';
				$child = $this->element['children'][0];
				foreach ( $course_rating->count_by_value as $key => $value ) {
					$rating_count_percent = ( $value > 0 ) ? ( $value * 100 ) / $course_rating->rating_count : 0;
					$html                .= call_user_func(
						$this->generate_child_element_with_new_id,
						$this->elements,
						$this->style_blocks,
						$child,
						array_merge(
							$this->options,
							array(
								'tutor_review_summery_index' => $key,
								'tutor_review_summery_value' => $value,
								'tutor_review_count_percent' => $rating_count_percent,
								'tutor_review_avg_count' => $value, // TODO: need to recheck avarage count.
							)
						)
					);
				}
				return $this->generate_common_element( false, $html );

			case TDE_APP_PREFIX . '-rating-summery-item-index':
				return $this->generate_common_element( false, $this->options['tutor_review_summery_index'] );

			case TDE_APP_PREFIX . '-rating-summery-item-progress':
				$id   = $this->element['id'];
				$per  = $this->options['tutor_review_count_percent'];
				$html = "<style>[data-droip='$id']{width: $per% !important;}</style>" . $this->generate_common_element( false );
				return $html;

			case TDE_APP_PREFIX . '-review-add-edit':
				// TODO: check user can review or not. if not return "";.
				$my_rating = tutor_utils()->get_reviews_by_user( 0, 0, 150, false, $course_id, array( 'approved', 'hold' ) );
				if ( ! $my_rating || !$course_id) {
					return '';
				}
				$this->options['my_rating']                 = $my_rating;
				$this->options['my_rating_input_generator'] = true;
				$children_html                              = $this->generate_child_elements();
				$active_rating                              = $my_rating ? $my_rating->rating : 0;
				$review_id                                  = $my_rating ? $my_rating->comment_ID : null;
				$children_html                             .= "<input type='hidden' name='course_id' value='$course_id' />";
				$children_html                             .= "<input type='hidden' name='review_id' value='$review_id' />";
				return "<div $this->attributes data-ele_name='$ele_name' data-active_rating='$active_rating'>$children_html</div>";

			case TDE_APP_PREFIX . '-review-text-input':
				$my_rating = $this->options['my_rating'];
				$html      = stripslashes( $my_rating ? $my_rating->comment_content : '' );
				return $this->generate_common_element( false, $html );

		}
	}

	/**
	 * Get Tutor Reviews
	 *
	 * @return array
	 */
	private function get_tutor_reviews() {
		$per_page        = tutor_utils()->get_option( 'pagination_per_page', 10 );
		$offset          = 0;// TODO: need to check tutor functionality.
		$current_user_id = get_current_user_id();
		$course_id       = isset( $this->options['post'] ) ? $this->options['post']->ID : get_the_ID();
		$reviews         = tutor_utils()->get_course_reviews( $course_id, $offset, $per_page, false, array( 'approved' ), $current_user_id );
		return $reviews;
	}
}
