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
 * Class ActionboxGenerator
 * This class is used to define all helper functions.
 */
trait AnnouncementsGenerator
{

	/**
	 * Generate announcements markup
	 */
	private function generate_announcements_markup()
	{
		switch ($this->element['name']) {
			case TDE_APP_PREFIX . '-announcements':
				$course_id = isset($this->options['post']) ? $this->options['post']->ID : get_the_ID();
				$announcements = tutor_utils()->get_announcements($course_id);

				$child = count($announcements) > 0 ?
					call_user_func($this->generate_child_element, $this->element['children'][1], $this->options) :
					call_user_func($this->generate_child_element, $this->element['children'][0], $this->options);

				return "<div $this->attributes>$child</div>";

			case TDE_APP_PREFIX . '-has-announcements':
				return $this->generate_common_element();

			case TDE_APP_PREFIX . '-no-announcements':
				return $this->generate_common_element();
		}
	}
}
