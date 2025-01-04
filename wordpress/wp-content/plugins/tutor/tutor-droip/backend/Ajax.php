<?php
/**
 * Preview script for html markup generator
 *
 * @package tutor-droip-elements
 */

namespace TutorLMSDroip;

use stdClass;
use Tutor\Models\CourseModel;
use TutorLMSDroip\ElementGenerator\Preview;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Ajax
 * This class is used to define all ajax functions.
 *
 * @package TutorLMSDroip
 * @since 1.0.0
 */
class Ajax {

	/**
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'wp_ajax_tutor_handle_api_calls', array( $this, 'tutor_handle_api_calls' ) );
	}

	/**
	 * Handle api calls
	 *
	 * @since 1.0.0
	 */
	public function tutor_handle_api_calls() {
		// Helper::verify_nonce( 'wp_rest' );
		//phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$request_method = sanitize_text_field( isset( $_REQUEST['method'] ) ? $_REQUEST['method'] : null );
		if ( 'enroll_course' === $request_method ) {
			tutor_utils()->checking_nonce();
			//phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$course_id = sanitize_text_field( isset( $_REQUEST['course_id'] ) ? $_REQUEST['course_id'] : null );
			$res       = tutor_utils()->do_enroll( $course_id );
			wp_send_json_success( $res );
		}

		if ( 'complete_course' === $request_method ) {
			tutor_utils()->checking_nonce();

			//phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$course_id = sanitize_text_field( isset( $_REQUEST['course_id'] ) ? $_REQUEST['course_id'] : null );
			$user_id   = get_current_user_id();
			if ( ! $user_id ) {
				wp_send_json_error( 'Please Sign-In' );
			}
			CourseModel::mark_course_as_completed( $course_id, $user_id );

			wp_send_json_success( true );
		}

		if ( 'add_qna' === $request_method ) {
			tutor_utils()->checking_nonce();

			//phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$course_id = sanitize_text_field( isset( $_REQUEST['course_id'] ) ? $_REQUEST['course_id'] : null );
			//phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$comment_parent_id = sanitize_text_field( isset( $_REQUEST['comment_parent_id'] ) ? $_REQUEST['comment_parent_id'] : null );
			//phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$content = sanitize_text_field( isset( $_REQUEST['content'] ) ? $_REQUEST['content'] : null );
			$user    = wp_get_current_user();
			$date    = gmdate( 'Y-m-d H:i:s', tutor_time() );

			//phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$collection_data = json_decode( stripslashes( isset( $_REQUEST['collection_data'] ) ? $_REQUEST['collection_data'] : null ), true );

			if ( ! $content ) {
				wp_send_json_error( 'Invalid request' );
			}

			$data = apply_filters(
				'tutor_qna_insert_data',
				array(
					'comment_post_ID'  => $course_id,
					'comment_author'   => $user->user_login,
					'comment_date'     => $date,
					'comment_date_gmt' => get_gmt_from_date( $date ),
					'comment_content'  => $content,
					'comment_approved' => 'approved',
					'comment_agent'    => 'TutorLMSPlugin',
					'comment_type'     => 'tutor_q_and_a',
					'comment_parent'   => $comment_parent_id,
					'user_id'          => $user->ID,
				)
			);

			global $wpdb;

			$response = $wpdb->insert( $wpdb->comments, $data );

			if ( false === $response ) {
				wp_send_json_error( 'Request failed!' );
			}

			$thread = $this->get_comment( $wpdb->insert_id );

			// comment-item.// -qna-reply.
			$new_element_name = 0 === $comment_parent_id ? 'comment-item' : TDE_APP_PREFIX . '-qna-reply';

			$new_element = Preview::generateQnAElement( $thread, $new_element_name, $collection_data );

			wp_send_json_success(
				array(
					'html'                => $new_element,
					'inserted_comment_id' => $wpdb->insert_id,
				)
			);
		}

		wp_send_json_error( 'Invalid request' );
	}

	/**
	 * Get comment
	 *
	 * @param int $id comment id.
	 * @return object
	 * @since 1.0.0
	 */
	private function get_comment( $id ) {
		$comment = (object) (array) get_comment( $id );

		if ( $comment instanceof stdClass ) {
			$author_posts_page_link = $comment->comment_author_url;

			if ( ! $author_posts_page_link ) {
				$author_posts_page_link = \get_author_posts_url( $comment->user_id );
			}

			$comment->author_profile_picture = get_avatar_url( $comment->user_id );
			$comment->author_posts_page_link = $author_posts_page_link;
		}

		return $comment;
	}
}
