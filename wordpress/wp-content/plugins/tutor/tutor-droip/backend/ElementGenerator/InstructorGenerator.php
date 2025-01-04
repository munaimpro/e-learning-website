<?php

/**
 * Preview script for html markup generator
 *
 * @package tutor-droip-elements
 */

namespace TutorLMSDroip\ElementGenerator;

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

/**
 * Class GradebookGenerator
 * This class is used to define all helper functions.
 */
trait InstructorGenerator
{

	/**
	 * Generate instructor markup
	 *
	 * @return string
	 */
	private function generate_instructor_all_markup()
	{
		switch ($this->element['name']) {
			case TDE_APP_PREFIX . '-instructor-list':
				return $this->generate_instructor_list_markup();

			case TDE_APP_PREFIX . '-instructor-list-item':
				return $this->generate_instructor_list_item_markup();

			case TDE_APP_PREFIX . '-instructor-avatar':
				return $this->generate_instructor_avatar_markup();

			case TDE_APP_PREFIX . '-instructor-name':
				return $this->generate_instructor_name_markup();
			case TDE_APP_PREFIX . '-instructor-bio':
				return $this->generate_instructor_bio_markup();

			case TDE_APP_PREFIX . '-instructor-job-title':
				return $this->generate_instructor_job_title_markup();
		}
	}

	/**
	 * Generate instructor list markup
	 *
	 * @return string
	 */
	private function generate_instructor_list_markup()
	{
		$course_id = isset($this->options['post']) ? $this->options['post']->ID : get_the_ID();
		$instructors = tutor_utils()->get_instructors_by_course($course_id);

		$children = '';

		if (is_array($instructors)) {
			foreach ($instructors as $instructor) {
				$children .= call_user_func(
					$this->generate_child_element,
					$this->element['children'][0],
					array_merge(
						$this->options,
						array('instructor' => $instructor)
					)
				);
			}
		}

		return "<div $this->attributes>$children</div>";
	}

	/**
	 * Generate instructor list item markup
	 *
	 * @return string
	 */
	private function generate_instructor_list_item_markup()
	{
		$instructor = $this->options['instructor'];

		$instructor_profile_url = tutor_utils()->profile_url($instructor->ID, true);

		$children = '';

		if (is_array($this->element['children'])) {
			foreach ($this->element['children'] as $child) {
				$children .= call_user_func(
					$this->generate_child_element,
					$child,
					array_merge(
						$this->options,
						array('instructor' => $instructor)
					)
				);
			}
		}

		return "<a $this->attributes href='$instructor_profile_url' >$children</a>";
	}

	/**
	 * Generate instructor name markup
	 *
	 * @return string
	 */
	private function generate_instructor_name_markup()
	{
		$instructor = $this->get_instructor_details();

		return "<div $this->attributes >$instructor->display_name</div>";
	}

	private function generate_instructor_bio_markup()
	{
		$instructor = $this->get_instructor_details();
		if (!$instructor->tutor_profile_bio) return '';
		return "<div $this->attributes >$instructor->tutor_profile_bio</div>";
	}

	private function generate_instructor_job_title_markup()
	{
		$instructor = $this->get_instructor_details();
		if (!$instructor->tutor_profile_job_title) return '';
		return "<div $this->attributes >$instructor->tutor_profile_job_title</div>";
	}

	/**
	 * Generate instructor avatar markup
	 *
	 * @return string
	 */
	private function generate_instructor_avatar_markup()
	{
		$instructor = $this->get_instructor_details();
		if (!$instructor->tutor_profile_photo) return '';
		$avatar = '';
		if ($instructor->tutor_profile_photo) {
			$avatar_src = wp_get_attachment_image_url($instructor->tutor_profile_photo, 'thumbnail');

			$instructor_name = $instructor->display_name;

			$avatar = "<img src=$avatar_src alt=$instructor_name style='width: 100%; height: 100%;' />";
		} else {
			$arr = explode(' ', trim($instructor->display_name));

			$first_char = ! empty($arr[0]) ? tutor_utils()->str_split($arr[0])[0] : '';

			$second_char = ! empty($arr[1]) ? tutor_utils()->str_split($arr[1])[0] : '';

			$initial_avatar = strtoupper($first_char . $second_char);

			$avatar = $initial_avatar;
		}

		return "<div $this->attributes >$avatar</div>";
	}

	private function get_instructor_details()
	{
		$instructor = isset($this->options['instructor']) ? $this->options['instructor'] : false;
		if (!$instructor) {
			$instructor = isset($this->options['user']) ? (object) $this->options['user'] : false;

			if ($instructor) {
				$is_instructor = tutor_utils()->is_instructor($instructor->ID);
				if ($is_instructor) {
					$instructor->tutor_profile_bio = get_user_meta($instructor->ID, '_tutor_profile_bio', true);
					$instructor->tutor_profile_job_title = get_user_meta($instructor->ID, '_tutor_profile_job_title', true);
					$instructor->tutor_profile_photo = get_user_meta($instructor->ID, '_tutor_profile_photo', true);
				}
			}
		}

		return $instructor;
	}
}
