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
 * Class Preview
 * This class is used to define all preview functions.
 */
class Preview
{
	use LessonGenerator;
	use InstructorGenerator;
	use ShareGenerator;
	use CourseMetaGenerator;
	use ActionboxGenerator;
	use WishListGenerator;
	use CourseThumbnailGenerator;
	use RatingGenerator;
	use AnnouncementsGenerator;
	use QnAGenerator;
	use ResourcesGenerator;
	use GradebookGenerator;
	use TopicsGenerator;

	/**
	 * Droip element object
	 *
	 * @var array $element | element data.
	 */
	private $element = array();
	/**
	 * Droip all elements object
	 *
	 * @var array $elements | array of elements.
	 */
	private $elements = array();
	/**
	 * Droip all style blocks object
	 *
	 * @var array $style_blocks | array of style blocks.
	 */
	private $style_blocks = array();
	/**
	 * Droip all style blocks object
	 *
	 * @var array $style_blocks | array of style blocks.
	 */
	private $attributes = array();
	/**
	 * Droip all options object
	 *
	 * @var array $options | array of options.
	 */
	private $options = array();
	/**
	 * Droip generate child element function
	 *
	 * @var callable $generate_child_element | function.
	 */
	private $generate_child_element = null; // function.
	/**
	 * Droip generate child element with new id function
	 *
	 * @var callable $generate_child_element_with_new_id | function.
	 */
	private $generate_child_element_with_new_id = null; // function.
	/**
	 * Droip element properties
	 *
	 * @var array $properties | array of element properties.
	 */
	private $properties = array();
	/**
	 * LMS course id
	 *
	 * @var int $course_id | course id.
	 */
	private $course_id = null;

	/**
	 * Class constructor
	 *
	 * @param array $props array of element properties.
	 *
	 * @since 1.0.0
	 */
	public function __construct($props)
	{
		$this->element                            = $props['element'];
		$this->elements                           = $props['elements'];
		$this->style_blocks                       = $props['style_blocks'];
		$this->attributes                         = $props['attributes'];
		$this->options                            = $props['options'];
		$this->generate_child_element             = $props['generate_child_element'];
		$this->generate_child_element_with_new_id = $props['generate_child_element_with_new_id'];
		$this->properties                         = $this->element['properties'];
	}


	/**
	 * Generate elements
	 *
	 * $element: the element
	 * $attributes: elements html attributes
	 * $options: options is needed if any dynamic content need to be render
	 * $generate_child_element: this is a helper method for printing nested element or child elements. This method take 2 arg: 1. elemenbt id 2. $options.
	 *
	 * @return string html markup;
	 */
	public function generate_elements()
	{

		switch ($this->element['name']) {
			case TDE_APP_PREFIX . '-wish-list':
			case TDE_APP_PREFIX . '-wish-list-normal':
			case TDE_APP_PREFIX . '-wish-list-loading':
			case TDE_APP_PREFIX . '-wish-list-wishlisted':
			case TDE_APP_PREFIX . '-wish-list-unauthinticate':
				return $this->generate_wish_list_elements();

				// Share Course.
			case TDE_APP_PREFIX . '-share-course':
				return $this->generate_course_share_markup();

				// Course meta.
			case TDE_APP_PREFIX . '-course-meta':
			case TDE_APP_PREFIX . '-course-level':
			case TDE_APP_PREFIX . '-course-level-text':
			case TDE_APP_PREFIX . '-enroll-count':
			case TDE_APP_PREFIX . '-enroll-count-value':
			case TDE_APP_PREFIX . '-course-duration':
			case TDE_APP_PREFIX . '-course-duration-value':
			case TDE_APP_PREFIX . '-course-update':
			case TDE_APP_PREFIX . '-course-update-value':
			case TDE_APP_PREFIX . '-sidebar-meta':
			case TDE_APP_PREFIX . '-sidebar-meta-icon':
			case TDE_APP_PREFIX . '-sidebar-meta-value':
				return $this->generate_course_meta_markup();

			case TDE_APP_PREFIX . '-course-thumbnail-image':
			case TDE_APP_PREFIX . '-course-thumbnail-video':
				return $this->generate_course_thumbnail_elements();

				// Action box.
			case TDE_APP_PREFIX . '-course-enroll-buttons':
			case TDE_APP_PREFIX . '-free-action-box':
			case TDE_APP_PREFIX . '-enroll-button':
			case TDE_APP_PREFIX . '-paid-action-buttons':
			case TDE_APP_PREFIX . '-cart-box':
			case TDE_APP_PREFIX . '-add-to-cart-button':
			case TDE_APP_PREFIX . '-view-cart-button':
			case TDE_APP_PREFIX . '-discounted-price':
			case TDE_APP_PREFIX . '-regular-price':
			case TDE_APP_PREFIX . '-course-action-buttons':
			case TDE_APP_PREFIX . '-start-learning':
			case TDE_APP_PREFIX . '-continue-learning':
			case TDE_APP_PREFIX . '-complete-course':
			case TDE_APP_PREFIX . '-retake-and-certificate':
			case TDE_APP_PREFIX . '-retake-course':
			case TDE_APP_PREFIX . '-view-certificate':
			case TDE_APP_PREFIX . '-enroll-info':
			case TDE_APP_PREFIX . '-enroll-date':
			case TDE_APP_PREFIX . '-lesson-counter':
			case TDE_APP_PREFIX . '-course-progress':
			case TDE_APP_PREFIX . '-progress-percent':
			case TDE_APP_PREFIX . '-progress-bar-inner':
				return $this->generate_actionbox_markup();

			case TDE_APP_PREFIX . '-rating-empty':
			case TDE_APP_PREFIX . '-rating-not-empty':
			case TDE_APP_PREFIX . '-rating-summery':
			case TDE_APP_PREFIX . '-rating-summery-item-index':
			case TDE_APP_PREFIX . '-rating-summery-item-progress':
			case TDE_APP_PREFIX . '-review-add-edit':
			case TDE_APP_PREFIX . '-review-text-input':
				return $this->generate_review_elements();

			case TDE_APP_PREFIX . '-rating-active-icon':
			case TDE_APP_PREFIX . '-rating-inactive-icon':
			case TDE_APP_PREFIX . '-rating-avarage-count':
			case TDE_APP_PREFIX . '-rating-total-count':
				return $this->generate_rating_elements();

			case TDE_APP_PREFIX . '-lessons':
			case TDE_APP_PREFIX . '-assignment':
			case TDE_APP_PREFIX . '-googlemeet':
			case TDE_APP_PREFIX . '-lesson-video':
			case TDE_APP_PREFIX . '-lesson-text':
			case TDE_APP_PREFIX . '-quiz':
			case TDE_APP_PREFIX . '-zoom':
			case TDE_APP_PREFIX . '-lesson-duration':
			case TDE_APP_PREFIX . '-lesson-access':
			case TDE_APP_PREFIX . '-meeting-status':
				// case TDE_APP_PREFIX . '-has-topics':
				return $this->generate_lessions_all_markup();

			case TDE_APP_PREFIX . '-instructor-list':
			case TDE_APP_PREFIX . '-instructor-list-item':
			case TDE_APP_PREFIX . '-instructor-avatar':
			case TDE_APP_PREFIX . '-instructor-name':
			case TDE_APP_PREFIX . '-instructor-bio':
			case TDE_APP_PREFIX . '-instructor-job-title':
				return $this->generate_instructor_all_markup();

			case TDE_APP_PREFIX . '-announcements':
			case TDE_APP_PREFIX . '-has-announcements':
			case TDE_APP_PREFIX . '-no-announcements':
				return $this->generate_announcements_markup();

			case TDE_APP_PREFIX . '-topics':
			case TDE_APP_PREFIX . '-has-topics':
			case TDE_APP_PREFIX . '-no-topics':
				return $this->generate_topics_markup();

			case TDE_APP_PREFIX . '-qna-editor':
				return $this->generate_qna_editor();

			case TDE_APP_PREFIX . '-qna-reply':
				return $this->generate_qna_reply();

			case TDE_APP_PREFIX . '-reply-button':
				return $this->generate_qna_reply_button();

				// Resources.
			case TDE_APP_PREFIX . '-resources':
			case TDE_APP_PREFIX . '-has-resources':
			case TDE_APP_PREFIX . '-resource-download':
			case TDE_APP_PREFIX . '-resource-title':
			case TDE_APP_PREFIX . '-resource-size':
				return $this->generate_resources_markup();

				// Gradebook.
			case TDE_APP_PREFIX . '-gradebook':
			case TDE_APP_PREFIX . '-has-gradebook':
			case TDE_APP_PREFIX . '-no-gradebook':
				return $this->generate_course_gradebook_markup();

			default:
				return $this->generate_common_element();
		}
	}

	/**
	 * Generate common element.
	 *
	 * @param bool $hide | false | true. | Hide element.
	 * @param bool $children_html | false | true. | Hide children.
	 * @return string
	 */
	private function generate_common_element($hide = false, $children_html = false)
	{
		$extra_attributes = '';
		if ($hide) {
			$extra_attributes .= ' data-element_hide="true"';
		}

		$html = '';
		$tag  = isset($this->properties['tag']) ? $this->properties['tag'] : 'div';
		$name = $this->element['name'];
		if (! $children_html) {
			$children_html = $this->generate_child_elements();
		}
		$html = "<$tag $this->attributes data-ele_name='$name' $extra_attributes>$children_html</$tag>";
		return $html;
	}

	/**
	 * Generate child elements.
	 *
	 * @return string
	 */
	private function generate_child_elements()
	{
		$html        = '';
		$child_count = isset($this->element['children']) ? count($this->element['children']) : 0;
		for ($i = 0; $i < $child_count; $i++) {
			$html .= call_user_func($this->generate_child_element, $this->element['children'][$i], $this->options);
		}
		return $html;
	}

	/**
	 * Generate child element by name.
	 *
	 * @return array $element | Element array.
	 */
	private function group_elements_by_element_name()
	{
		return array_reduce(
			$this->element['children'],
			function ($carry, $item) {
				if (isset($this->elements[$item], $this->elements[$item]['name'])) {
					$carry[$this->elements[$item]['name']] = $this->elements[$item];
				}

				return $carry;
			},
			array()
		);
	}
}
