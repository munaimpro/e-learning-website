<?php
/**
 * Lesson view generator
 *
 * @package tutor-droip-elements
 */

namespace TutorLMSDroip\ElementGenerator;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class LessonGenerator
 * This class is used to define all lesson generator functions.
 */
trait LessonGenerator {

	/**
	 * Post type enum
	 *
	 * @var array
	 */
	private $post_type_enum = array(
		'QUIZ'        => 'tutor_quiz',
		'ASSIGNMENT'  => 'tutor_assignments',
		'LESSON'      => 'lesson',
		'GOOGLE_MEET' => 'tutor-googl,-meet',
		'ZOOM'        => 'tutor_zoom_meeting',
	);

	/**
	 * Generate lessons markup
	 */
	private function generate_lessions_all_markup() {
		switch ( $this->element['name'] ) {
			case TDE_APP_PREFIX . '-lessons':
				return $this->generate_lessons_markup();
			case TDE_APP_PREFIX . '-assignment':
			case TDE_APP_PREFIX . '-googlemeet':
			case TDE_APP_PREFIX . '-lesson-video':
			case TDE_APP_PREFIX . '-lesson-text':
			case TDE_APP_PREFIX . '-quiz':
			case TDE_APP_PREFIX . '-zoom':
				return $this->generate_lesson_markup();
			case TDE_APP_PREFIX . '-lesson-duration':
				return $this->generate_lesson_duration();
			case TDE_APP_PREFIX . '-lesson-access':
				return $this->lesson_aceess();
			case TDE_APP_PREFIX . '-meeting-status':
				return $this->lesson_status();
		}
	}

	/**
	 * Generate lessons markup
	 *
	 * @return string
	 */
	private function generate_lessons_markup() {
		$element_block = $this->group_elements_by_element_name();

		$lessons_query = tutor_utils()->get_course_contents_by_topic( $this->options['post']->ID, -1 );

		$children = '';

		while ( $lessons_query->have_posts() ) {
			$lessons_query->the_post();
			global $post;

			$options = array_merge(
				$this->options,
				array(
					'post' => $post,
				)
			);

			switch ( get_post_type() ) {
				case $this->post_type_enum['QUIZ']:
					$quiz_block = $element_block[ TDE_APP_PREFIX . '-quiz' ];
					$children  .= call_user_func( $this->generate_child_element, $quiz_block['id'], $options );
					break;
				case $this->post_type_enum['ASSIGNMENT']:
					$assignment_block = $element_block[ TDE_APP_PREFIX . '-assignment' ];

					$children .= call_user_func( $this->generate_child_element, $assignment_block['id'], $options );
					break;
				case $this->post_type_enum['LESSON']:
					if ( $this->lesson_has_video() ) {
						$lesson_block = $element_block[ TDE_APP_PREFIX . '-lesson-video' ];

						$children .= call_user_func( $this->generate_child_element, $lesson_block['id'], $options );
					} else {
						$lesson_block = $element_block[ TDE_APP_PREFIX . '-lesson-text' ];

						$children .= call_user_func( $this->generate_child_element, $lesson_block['id'], $options );
					}
					break;

				case $this->post_type_enum['ZOOM']:
					$zoom_block = $element_block[ TDE_APP_PREFIX . '-zoom' ];

					$children .= call_user_func( $this->generate_child_element, $zoom_block['id'], $options );
					break;

				case $this->post_type_enum['GOOGLE_MEET']:
					$googlemeet_block = $element_block[ TDE_APP_PREFIX . '-googlemeet' ];

					$children .= call_user_func( $this->generate_child_element, $googlemeet_block['id'], $options );
					break;

				default:
					$children .= ( '<div>' . get_post_type() . ': ' . get_the_title() . '</div>' );
					break;

			}
		}

		wp_reset_postdata();

		return "<div $this->attributes > $children </div>";
	}

	/**
	 * Generate lesson markup
	 *
	 * @return string
	 */
	private function generate_lesson_markup() {
		global $post;

		$lesson_permalink = $this->lesson_has_access() ? get_the_permalink() : 'javascript:void()';

		$children = '';

		$options = array_merge(
			$this->options,
			array(
				'post' => $post,
			)
		);

		if ( isset( $this->element['children'] ) && $this->element['children'] && is_array( $this->element['children'] ) ) {
			$children = array_reduce(
				$this->element['children'],
				fn ( $carry, $item ) => ( $carry . call_user_func( $this->generate_child_element, $item, $options ) ),
				''
			);
		}

		return "<a $this->attributes href='$lesson_permalink' > $children </a>";
	}

	/**
	 * Generate lesson duration markup
	 *
	 * @return string
	 */
	private function generate_lesson_duration() {
		$duration = tutor_utils()->get_optimized_duration( $this->lesson_play_time() );

		return "<div $this->attributes> $duration </div>";
	}

	/**
	 * Generate lesson access markup
	 *
	 * @return string
	 */
	private function lesson_aceess() {
		$is_locked = $this->lesson_is_locked();

		$child = $is_locked ?
					call_user_func( $this->generate_child_element, $this->element['children'][0], $this->options ) :
					call_user_func( $this->generate_child_element, $this->element['children'][1], $this->options );

		return "<span $this->attributes> $child </span>";
	}

	/**
	 * Generate lesson status markup
	 *
	 * @return string
	 */
	private function lesson_status() {
		if ( ! tutor()->has_pro ) {
			return '';
		}

		global $post;

		$element_blocks = $this->group_elements_by_element_name();

		$live_block = $element_blocks[ TDE_APP_PREFIX . '-meeting-live' ];

		$expired_block = $element_blocks[ TDE_APP_PREFIX . '-meeting-expired' ];

		$zoom_meeting = tutor_zoom_meeting_data( $post->ID );

		$block = null;

		if ( $zoom_meeting->is_expired ) {
			$block = $expired_block;
		} elseif ( $zoom_meeting->is_started ) {
			$block = $live_block;
		}

		$child = $block ? call_user_func( $this->generate_child_element, $block['id'], $this->options ) : '';

		return "<div $this->attributes> $child </div>";
	}

	/**
	 * Check if lesson has video
	 *
	 * @return bool
	 */
	private function lesson_has_video() {
		return (bool) tutor_utils()->get_video_info();
	}

	/**
	 * Check if lesson is locked
	 *
	 * @return bool
	 */
	private function lesson_play_time() {
		$video = tutor_utils()->get_video_info();

		$play_time = $video ? $video->playtime : 0;

		return $play_time;
	}

	/**
	 * Check if lesson is enrolled
	 *
	 * @return bool
	 */
	private function is_enrolled() {
		$course_id = $this->get_course_id();

		return (bool) tutor_utils()->is_enrolled( $course_id );
	}

	/**
	 * Get course id
	 *
	 * @return int
	 */
	private function get_course_id() {
		global $post;

		if ( ! $this->course_id ) {
			$this->course_id = tutor_utils()->get_course_id_by( 'lesson', $post->ID );
		}

		return $this->course_id;
	}

	/**
	 * Check if lesson has access
	 *
	 * @return bool
	 */
	private function lesson_has_access() {
		$course_id = $this->get_course_id();

		return $this->is_enrolled() ||
			(
				get_post_meta( $course_id, '_tutor_is_public_course', true ) == 'yes' &&
				! tutor_utils()->is_course_purchasable( $course_id )
			);
	}

	/**
	 * Check if lesson is locked
	 *
	 * @return bool
	 */
	private function lesson_is_locked() {
		global $post;

		$course_id = $this->get_course_id();

		$is_preview = get_post_meta( $post->ID, '_is_preview', true );

		return ! ( $this->is_enrolled() || $is_preview || \TUTOR\Course_List::is_public( $course_id ) );
	}
}
