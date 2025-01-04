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
 * Class CourseMetaGenerator
 * This class is used to define all helper functions.
 */
trait CourseMetaGenerator {
	/**
	 * Generate course meta markup
	 *
	 * @return string
	 */
	private function generate_course_meta_markup() {
		switch ( $this->element['name'] ) {
			case TDE_APP_PREFIX . '-course-meta':
				$children_html = $this->generate_child_elements();
				return "<div $this->attributes>$children_html</div>";

			case TDE_APP_PREFIX . '-course-level':
				if ( ! tutor_utils()->get_option( 'enable_course_level', true, true ) ) {
					return '';
				}

				return $this->generate_common_element();

			case TDE_APP_PREFIX . '-course-level-text':
				$course_id = isset( $this->options['post'] ) ? $this->options['post']->ID : get_the_ID();
				$level_text = get_tutor_course_level( $course_id );
				return "<span $this->attributes>$level_text</span>";

			case TDE_APP_PREFIX . '-enroll-count':
				if ( ! tutor_utils()->get_option( 'enable_course_total_enrolled' ) ) {
					return '';
				}

				return $this->generate_common_element();

			case TDE_APP_PREFIX . '-enroll-count-value':
				$count_value = tutor_utils()->count_enrolled_users_by_course();
				return "<span $this->attributes>$count_value</span>";

			case TDE_APP_PREFIX . '-course-duration':
				$status = tutor_utils()->get_option( 'enable_course_duration' );
				if ( ! $status ) {
					return '';
				}
				return $this->generate_common_element();

			case TDE_APP_PREFIX . '-course-duration-value':
				$course_id = isset( $this->options['post'] ) ? $this->options['post']->ID : get_the_ID();
				$duration  = get_post_meta( $course_id, '_course_duration', true );

				if ( ! $duration ) {
					return '';
				}

				$hour      = (int) $duration['hours'];
				$hour_text = $hour > 1 ? 'hours' : 'hour';

				$minute      = (int) $duration['minutes'];
				$minute_text = $minute > 1 ? 'minutes' : 'minute';

				return "<span $this->attributes>{$duration['hours']} {$hour_text} {$duration['minutes']} {$minute_text}</span>";

			case TDE_APP_PREFIX . '-course-update':
				$update = get_tutor_option( 'enable_course_update_date' );
				if ( ! $update ) {
					return '';
				}

				return $this->generate_common_element();

			case TDE_APP_PREFIX . '-course-update-value':
				$date = get_the_modified_date( get_option( 'date_format' ) );
				return "<span $this->attributes>$date</span>";

			case TDE_APP_PREFIX . '-sidebar-meta':
				$course_id = isset( $this->options['post'] ) ? $this->options['post']->ID : get_the_ID();

				$sidebar_metas = apply_filters( 'tutor/course/single/sidebar/metadata', array(), get_the_ID() );
				$html          = '';
				foreach ( $sidebar_metas as $key => $meta ) {
					if ( ! $meta['value'] ) {
						continue;
					}
					$this->options['sidebar_meta'] = $meta;
					$child                         = '';
					$child_count                   = isset( $this->element['children'] ) ? count( $this->element['children'] ) : 0;
					for ( $i = 0; $i < $child_count; $i++ ) {
						$child .= call_user_func( $this->generate_child_element, $this->element['children'][ $i ], $this->options );
					}
					$html .= "<div $this->attributes>$child</div>";
				}
				return "<div $this->attributes>$html</div>";

			case TDE_APP_PREFIX . '-sidebar-meta-icon':
				if ( ! isset( $this->options['sidebar_meta'] ) ) {
					return '';
				}
				$sidebar_meta = $this->options['sidebar_meta'];
				$lebel        = $sidebar_meta['label'];
				$icon_class   = $sidebar_meta['icon_class'];
				$html         = "<span $this->attributes><i class='$icon_class' aria-labelledby='$lebel'></i></span>";
				return $html;

			case TDE_APP_PREFIX . '-sidebar-meta-value':
				if ( ! isset( $this->options['sidebar_meta'] ) ) {
					return '';
				}
				$sidebar_meta = $this->options['sidebar_meta'];
				$value        = $sidebar_meta['value'];
				$html         = "<span $this->attributes>$value</span>";
				return $html;

		}
	}
}
