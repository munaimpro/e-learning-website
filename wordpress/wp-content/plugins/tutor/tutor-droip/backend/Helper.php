<?php

/**
 * Preview script for html markup generator
 *
 * @package tutor-droip-elements
 */

namespace TutorLMSDroip;

use Droip\Ajax\ExportImport;

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

/**
 * Class Helper
 * This class is used to define all helper functions.
 */
class Helper
{

	/**
	 * Function to activate droip elements plugin
	 */
	public static function t_d_e_activate()
	{
		self::get_course_template_posts();
	}

	/**
	 * This function will verify nonce
	 * ACT like API calls auth middleware
	 *
	 * @param string $action ajax action name.
	 *
	 * @return void
	 */
	public static function verify_nonce($action = -1)
	{
		$nonce = sanitize_text_field(isset($_SERVER['HTTP_X_WP_NONCE']) ? wp_unslash($_SERVER['HTTP_X_WP_NONCE']) : null);
		if (! wp_verify_nonce($nonce, $action)) {
			wp_send_json_error('Not authorized');
			exit;
		}
	}

	/**
	 * Get course template post
	 *
	 * @return mixed
	 */
	public static function get_course_template_posts()
	{
		$all_templates = get_posts(
			array(
				'post_type'      => 'droip_template',
				'posts_per_page' => -1,
				'post_status'    => ['draft', 'publish'],
			)
		);
		$data = [];
		foreach ($all_templates as $key => $template) {
			$conditions = get_post_meta($template->ID, 'droip_template_conditions', true);
			if ($conditions) {
				foreach ($conditions as $key2 => $condition) {
					if ($condition['category'] === 'courses') {
						$data[] = $template;
					}
				}
			}
		}

		if (count($data) === 0) {
			//create a course template
			$post_id = wp_insert_post(
				array(
					'post_title' => 'Course Details',
					'post_name'  => 'Course Details',
					'post_type'  => 'droip_template'
				)
			);
			$conditions = array(
				array(
					'category' => 'courses',
					'taxonomy' => '*',
					'visibility' => 'show'
				)
			);
			update_post_meta($post_id, 'droip_template_conditions', $conditions);

			if (class_exists('Droip\Ajax\ExportImport')) {
				$template_path = TDE_ROOT_PATH . '/assets/course-details.zip';
				ExportImport::process_droip_template_zip($template_path, false, $post_id);
			}

			$data[] = get_post($post_id);
		}
		return $data;
	}

	public static function upload_layout_pack($obj)
	{
		try {
			foreach ($obj['pages'] as $key => $page) {
				$zip_path = self::download_zip_from_remote($page['src'], $page['ID'] . '.zip');

				$post_id = self::get_post_id_for_template_import($obj['ID'], $page);

				if (isset($page['conditions'])) {
					update_post_meta($post_id, 'droip_template_conditions', $page['conditions']);
				}
				if (class_exists('Droip\Ajax\ExportImport')) {
					ExportImport::process_droip_template_zip($zip_path, false, $post_id);
					unlink($zip_path);
				}
			}
		} catch (\Throwable $th) {
			return false;
		}
		return true;
	}

	private static function get_post_id_for_template_import($parent_id, $page_info)
	{
		global $wpdb;
		$meta_key      = 'droip_template_imported_' . $parent_id . '_' . $page_info['ID'];
		$imported_flag = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}postmeta WHERE meta_key = '" . $meta_key . "'", OBJECT);
		$post_id       = null;
		$post_status   = null;
		foreach ($imported_flag as $key => $p_meta) {
			$post = get_post($p_meta->post_id);
			if ($post && $post->post_type === $page_info['post_type']) {
				$post_id     = $p_meta->post_id;
				$post_status = $post->post_status;
				break;
			}
		}

		if (! $post_id || 'trash' === $post_status) {
			$post_id = wp_insert_post(
				array(
					'post_title'  => $page_info['title'],
					'post_name'   => $page_info['title'],
					'post_type'   => $page_info['post_type'],
					'post_status' => 'publish',
				)
			);
		}

		update_post_meta($post_id, $meta_key, true);
		update_post_meta($post_id, '_wp_page_template', DROIP_FULL_CANVAS_TEMPLATE_PATH);
		update_post_meta($post_id, 'droip_include_wp_header', 'true');
		update_post_meta($post_id, 'droip_include_wp_footer', 'true');
		if ($page_info['ID'] === 'home') {
			update_option('page_on_front', $post_id);
			update_option('show_on_front', 'page');
		}
		return $post_id;
	}

	/**
	 * [download_zip description]
	 *
	 * @param   [type] $remote_ile  [$remote_ile description]
	 * @param   [type] $new_name    [$new_name description]
	 *
	 * @return  [type]               [return description]
	 */
	private static function download_zip_from_remote($remote_ile, $new_name)
	{
		try {
			// Local path to save the downloaded file.
			$local_file = wp_upload_dir()['basedir'] . '/' . $new_name;
			// Download the file from the remote server.
			$file_contents = file_get_contents($remote_ile);
			// Save the file locally.
			if ($file_contents !== false) {
				file_put_contents($local_file, $file_contents);
				return $local_file;
			}
		} catch (\Throwable $th) {
			// throw $th;
		}
		return false;
	}
}
