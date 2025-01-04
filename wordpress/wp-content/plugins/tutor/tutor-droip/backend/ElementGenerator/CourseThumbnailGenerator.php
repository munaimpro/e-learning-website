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
 * Class CourseThumbnailGenerator
 * This class is used to define all helper functions.
 */
trait CourseThumbnailGenerator {
	/**
	 * Generate course thumbnail elements
	 *
	 * @return string
	 */
	private function generate_course_thumbnail_elements() {
		switch ( $this->element['name'] ) {
			case TDE_APP_PREFIX . '-course-thumbnail-image':
				$has_video = tutor_utils()->has_video_in_single();
				if ( $has_video ) {
					return '';
				}
				$tutor_course_img = get_tutor_course_thumbnail_src();
				return "<img src='$tutor_course_img' $this->attributes />";

			case TDE_APP_PREFIX . '-course-thumbnail-video':
				$has_video = tutor_utils()->has_video_in_single();
				if ( ! $has_video ) {
					return '';
				}

				$child = tutor_course_video( false );
				return "<figure $this->attributes>$child</figure>";

		}
	}
}
