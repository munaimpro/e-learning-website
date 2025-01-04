<?php

/**
 * Preview script for html markup generator
 *
 * @package tutor-droip-elements
 */

namespace TutorLMSDroip\ElementGenerator;

use Tutor\Ecommerce\CartController;
use Tutor\Models\CartModel;
use TUTOR_CERT\Certificate;

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

/**
 * Class ActionboxGenerator
 * This class is used to define all helper functions.
 *
 * @package TutorLMSDroip\ElementGenerator
 */
trait ActionboxGenerator
{
	/**
	 * Generate actionbox markup
	 *
	 * @return string
	 */
	private function generate_actionbox_markup()
	{
		$course_id = isset($this->options['post']) ? $this->options['post']->ID : get_the_ID();
		$ele_name  = $this->element['name'];
		switch ($this->element['name']) {
			case TDE_APP_PREFIX . '-course-enroll-buttons':
				if (tutor_entry_box_buttons($course_id)->show_add_to_cart_btn || tutor_entry_box_buttons($course_id)->show_enroll_btn) {
					return $this->generate_common_element();
				} else {
					return '';
				}
			case TDE_APP_PREFIX . '-free-action-box':
				$price          = apply_filters('get_tutor_course_price', null, $course_id);
				$is_purchasable = tutor_utils()->is_course_purchasable();
				$is_free        = $is_purchasable && $price ? false : true;
				if (! $is_free) {
					return '';
				}
				return $this->generate_common_element();

			case TDE_APP_PREFIX . '-enroll-button':
				if (! tutor_entry_box_buttons($course_id)->show_enroll_btn) {
					return '';
				}
				$children_html  = $this->generate_child_elements();
				$children_html .= "<input type='hidden' name='course_id' value='$course_id' />";
				return "<div $this->attributes data-ele_name='$ele_name'>$children_html</div>";
			case TDE_APP_PREFIX . '-paid-action-buttons':
				if (tutor_entry_box_buttons($course_id)->show_add_to_cart_btn) {
					return $this->generate_common_element();
				} else {
					return '';
				}

			case TDE_APP_PREFIX . '-cart-box':
				$monetize_by = tutor_utils()->get_option('monetize_by');
				if ('tutor' === $monetize_by) {
					$is_course_in_user_cart = CartModel::is_course_in_user_cart(get_current_user_id(), $course_id);
					if (!$is_course_in_user_cart)
						return '';
				} else if ('wc' === $monetize_by) {
					if (! class_exists('WooCommerce')) {
						return '';
					}
					if (! tutor_entry_box_buttons($course_id)->show_add_to_cart_btn) {
						return '';
					}
				}
				$children_html = $this->generate_child_elements();
				return "<div $this->attributes>$children_html</div>";
			case TDE_APP_PREFIX . '-add-to-cart-button':
				$monetize_by = tutor_utils()->get_option('monetize_by');
				if ('tutor' === $monetize_by) {
					$is_course_in_user_cart = CartModel::is_course_in_user_cart(get_current_user_id(), $course_id);
					if ($is_course_in_user_cart)
						return '';

					$children_html  = $this->generate_child_elements();
					$children_html .= "<input type='hidden' name='course_id' value='$course_id' />";
					$children_html .= "<input type='hidden' name='monetize_by' value='$monetize_by' />";
					return $this->generate_common_element(false, $children_html);
				} else if ('wc' === $monetize_by) {
					if (! class_exists('WooCommerce')) {
						return '';
					}
					if (! tutor_entry_box_buttons($course_id)->show_add_to_cart_btn) {
						return '';
					}

					$product_id = tutor_utils()->get_course_product_id($course_id);

					if (tutor_utils()->is_course_added_to_cart($product_id, true)) {
						return '';
					}

					$action_url     = esc_url(apply_filters('tutor_course_add_to_cart_form_action', get_permalink($course_id)));
					$children_html  = $this->generate_child_elements();
					$children_html .= "<input type='hidden' name='action_url' value='$action_url' />";
					$children_html .= "<input type='hidden' name='product_id' value='$product_id' />";
					$children_html .= "<input type='hidden' name='monetize_by' value='$monetize_by' />";
					return $this->generate_common_element(false, $children_html);
				}
				return '';
			case TDE_APP_PREFIX . '-view-cart-button':

				$monetize_by = tutor_utils()->get_option('monetize_by');
				if ('tutor' === $monetize_by) {
					$is_course_in_user_cart = CartModel::is_course_in_user_cart(get_current_user_id(), $course_id);
					if (!$is_course_in_user_cart)
						return '';
					$cart_page_url          = CartController::get_page_url();
					$children_html = $this->generate_child_elements();
					return "<a href='$cart_page_url' $this->attributes>$children_html</a>";
				} else if ('wc' === $monetize_by) {
					if (! class_exists('WooCommerce')) {
						return '';
					}
					$product_id = tutor_utils()->get_course_product_id($course_id);
					$product    = wc_get_product($product_id);

					if (! tutor_utils()->is_course_added_to_cart($product_id, true)) {
						return '';
					}

					$cart_url      = esc_url(wc_get_cart_url());
					$children_html = $this->generate_child_elements();

					return "<a href='$cart_url' $this->attributes>$children_html</a>";
				}




			case TDE_APP_PREFIX . '-discounted-price':
				$monetize_by = tutor_utils()->get_option('monetize_by');
				$sale_price = false;
				if ('tutor' === $monetize_by) {
					$course_price  = tutor_utils()->get_raw_course_price($course_id);
					$regular_price = $course_price->regular_price;
					$sale_price    = $course_price->sale_price;
				} else if ('wc' === $monetize_by) {
					if (! class_exists('WooCommerce')) {
						return '';
					}
					$product_id = tutor_utils()->get_course_product_id($course_id);
					$product    = wc_get_product($product_id);
					if (! $product) {
						return '';
					}
					$sale_price = wc_price($product->get_sale_price());
				}

				if (! $sale_price) {
					return '';
				}
				return "<span $this->attributes>$sale_price</span>";

			case TDE_APP_PREFIX . '-regular-price':
				$monetize_by = tutor_utils()->get_option('monetize_by');
				if ('tutor' === $monetize_by) {
					$course_price  = tutor_utils()->get_raw_course_price($course_id);
					$regular_price = $course_price->regular_price;
				} else if ('wc' === $monetize_by) {
					if (! class_exists('WooCommerce')) {
						return '';
					}
					$product_id = tutor_utils()->get_course_product_id($course_id);
					$product    = wc_get_product($product_id);
					if (! $product) {
						return '';
					}
					$regular_price = wc_price($product->get_regular_price());
				}
				if (! $regular_price) {
					return '';
				}
				return "<span $this->attributes>$regular_price</span>";

			case TDE_APP_PREFIX . '-course-action-buttons':
				$is_course_completed = tutor_utils()->is_completed_course($course_id, get_current_user_id());
				if ((tutor_entry_box_buttons($course_id)->show_start_learning_btn && ! $is_course_completed) || (tutor_entry_box_buttons($course_id)->show_continue_learning_btn && ! $is_course_completed) || tutor_entry_box_buttons($course_id)->show_retake_course_btn || (tutor_entry_box_buttons($course_id)->show_certificate_view_btn && function_exists('TUTOR_CERT'))) {
					return $this->generate_common_element();
				} else {
					return '';
				}

			case TDE_APP_PREFIX . '-start-learning':
				if (! tutor_entry_box_buttons($course_id)->show_start_learning_btn) {
					return '';
				}
				$is_course_completed = tutor_utils()->is_completed_course($course_id, get_current_user_id());
				if ($is_course_completed) {
					return '';
				}
				$lession_url   = tutor_utils()->get_course_first_lesson();
				$children_html = $this->generate_child_elements();
				return "<a href='$lession_url' $this->attributes data-ele_name='$ele_name'>$children_html</a>";

			case TDE_APP_PREFIX . '-continue-learning':
				if (! tutor_entry_box_buttons($course_id)->show_continue_learning_btn) {
					return '';
				}
				$is_course_completed = tutor_utils()->is_completed_course($course_id, get_current_user_id());

				if ($is_course_completed) {
					return '';
				}
				$lession_url   = tutor_utils()->get_course_first_lesson();
				$children_html = $this->generate_child_elements();
				return "<a href='$lession_url' $this->attributes data-ele_name='$ele_name'>$children_html</a>";

			case TDE_APP_PREFIX . '-complete-course':
				if (! tutor_entry_box_buttons($course_id)->show_complete_course_btn) {
					return '';
				}
				$is_course_completed = tutor_utils()->is_completed_course($course_id, get_current_user_id());
				if ($is_course_completed) {
					return '';
				}

				$children_html  = $this->generate_child_elements();
				$children_html .= "<input type='hidden' name='course_id' value='$course_id' />";
				return "<button $this->attributes data-ele_name='$ele_name'>$children_html</button>";

			case TDE_APP_PREFIX . '-retake-and-certificate':
				if (tutor_entry_box_buttons($course_id)->show_retake_course_btn || (tutor_entry_box_buttons($course_id)->show_certificate_view_btn && function_exists('TUTOR_CERT'))) {
					return $this->generate_common_element();
				} else {
					return '';
				}
			case TDE_APP_PREFIX . '-retake-course':
				if (! tutor_entry_box_buttons($course_id)->show_retake_course_btn) {
					return '';
				}
				// TODO: Retake functionality
				return $this->generate_common_element();

			case TDE_APP_PREFIX . '-view-certificate':
				if (! function_exists('TUTOR_CERT')) {
					return '';
				}
				if (! tutor_entry_box_buttons($course_id)->show_certificate_view_btn) {
					return '';
				}
				$is_course_completed = tutor_utils()->is_completed_course($course_id, get_current_user_id());

				if (! $is_course_completed) {
					return '';
				}

				$certificate_url = '';
				if (tutils()->is_addon_enabled(TUTOR_CERT()->basename)) {
					$certificate_url = (new Certificate(true))->get_certificate($course_id);
				}
				$children_html = $this->generate_child_elements();
				return "<a href='$certificate_url' $this->attributes data-ele_name='$ele_name'>$children_html</a>";

			case TDE_APP_PREFIX . '-enroll-info':
				$is_enrolled = tutor_utils()->is_enrolled($course_id);
				if (! $is_enrolled) {
					return '';
				}
				return $this->generate_common_element();

			case TDE_APP_PREFIX . '-enroll-date':
				$is_enrolled = tutor_utils()->is_enrolled($course_id);
				$post_date   = is_object($is_enrolled) && isset($is_enrolled->post_date) ? $is_enrolled->post_date : '';
				$post_date   = tutor_i18n_get_formated_date($post_date, get_option('date_format'));
				return $this->generate_common_element(false, $post_date);

			case TDE_APP_PREFIX . '-lesson-counter':
				$course_progress = tutor_utils()->get_course_completed_percent($course_id, 0, true);
				$html            = $course_progress['completed_count'] . '/' . $course_progress['total_count'];
				return $this->generate_common_element(false, $html);

			case TDE_APP_PREFIX . '-course-progress':
				$is_enrolled = tutor_utils()->is_enrolled($course_id);
				if (! $is_enrolled) {
					return '';
				}
				return $this->generate_common_element();
			case TDE_APP_PREFIX . '-progress-percent':
				$course_progress   = tutor_utils()->get_course_completed_percent($course_id, 0, true);
				$completed_percent = $course_progress['completed_percent'];
				$html              = $completed_percent . '%';
				return $this->generate_common_element(false, $html);

			case TDE_APP_PREFIX . '-progress-bar-inner':
				$id                = $this->element['id'];
				$course_progress   = tutor_utils()->get_course_completed_percent($course_id, 0, true);
				$completed_percent = $course_progress['completed_percent'];

				$html = "<style>[data-droip='$id']{width: $completed_percent% !important;}</style>" . $this->generate_common_element(false);
				return $html;
			default:
				return '';
		}
	}
}
