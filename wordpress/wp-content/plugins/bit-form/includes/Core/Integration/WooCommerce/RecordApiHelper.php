<?php

/**
 * WooCommerce Record Api
 *
 */

namespace BitCode\BitForm\Core\Integration\WooCommerce;

use BitCode\BitForm\Core\Database\FormEntryLogModel;
use BitCode\BitForm\Core\Util\ApiResponse as UtilApiResponse;
use WC_Product_Download;
use WP_Error;

/**
 * Provide functionality for Record insert,upsert
 */
class RecordApiHelper
{
  private $_integrationID;

  private $_logID;

  private $_logResponse;

  public function __construct($integId, $logID)
  {
    $this->_integrationID = $integId;
    $this->_logID = $logID;
    $this->_logResponse = new UtilApiResponse();
  }

  public function executeRecordApi($formID, $entryID, $module, $fieldValues, $fieldMap, $uploadFieldMap, $required)
  {
    $fieldData = [];
    foreach ($fieldMap as $fieldPair) {
      if (!empty($fieldPair->wcField) && !empty($fieldPair->formField)) {
        if ('custom' === $fieldPair->formField && isset($fieldPair->customValue)) {
          $fieldData[$fieldPair->wcField] = $fieldPair->customValue;
        } else {
          $fieldData[$fieldPair->wcField] = $fieldValues[$fieldPair->formField];
        }

        if (in_array($fieldPair->wcField, $required) && empty($fieldValues[$fieldPair->formField])) {
          $error = new WP_Error('REQ_FIELD_EMPTY', wp_sprintf(__('%s is required for woocommerce %s', 'bit-form'), $fieldPair->wcField, $module));
          $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => $module, 'type_name' => 'create'], 'validation', $error);
          return $error;
        }
      }
    }

    $model = new FormEntryLogModel();
    $result = $model->entryLogCheck($entryID, $this->_integrationID);

    $entry_type = 'create';

    if (count($result) && !isset($result->errors['result_empty'])) {
      $entry_type = 'edit';
      $api_type = json_decode($result[0]->api_type);

      if ($api_type->type === $module) {
        $id = $result[0]->response_obj;
      }
    }

    if ('product' === $module) {
      if (!empty($fieldData['tags_input'])) {
        $tags = explode(',', $fieldData['tags_input']);
        unset($fieldData['tags_input']);
      }

      if (!empty($fieldData['post_category'])) {
        $categories = explode(',', $fieldData['post_category']);
        unset($fieldData['post_category']);
      }

      if (!empty($fieldData['_regular_price'])) {
        $price = $fieldData['_regular_price'];
      }

      if (!empty($fieldData['_sale_price'])) {
        $price = $fieldData['_sale_price'];
      }

      if (!empty($fieldData['product_type'])) {
        $product_type = $fieldData['product_type'];
        if ('external' === $product_type && !empty($fieldData['_product_url'])) {
          $product_type = 'external';
        } else {
          $product_type = 'simple';
          unset($fieldData['_product_url'], $fieldData['_button_text']);
        }
        unset($fieldData['product_type']);
      }

      $post_fields = [
        'post_content', 'post_title', 'post_status', 'post_type', 'comment_status', 'post_password', 'menu_order', 'post_excerpt', 'post_date', 'post_date_gmt'
      ];

      $post_inputs = array_intersect_key($fieldData, array_flip($post_fields));
      $meta_inputs = array_diff_key($fieldData, array_flip($post_fields));

      $fieldData = $post_inputs;
      $fieldData['post_type'] = $module;
      $fieldData['meta_input'] = $meta_inputs;

      if (!empty($fieldData['post_date']) || !empty($fieldData['post_date_gmt'])) {
        $fieldData['post_status'] = 'future';
      }

      if (isset($id)) {
        $fieldData['ID'] = $id;
      }

      $product_id = wp_insert_post($fieldData);

      if (isset($product_type)) {
        wp_set_object_terms($product_id, $product_type, 'product_type');
      }

      if (isset($price)) {
        update_post_meta($product_id, '_price', $price);
      }

      if (isset($categories)) {
        wp_set_object_terms($product_id, $categories, 'product_cat');
      }

      if (isset($tags)) {
        wp_set_object_terms($product_id, $tags, 'product_tag');
      }

      if (is_wp_error($product_id) || !$product_id) {
        $response = is_wp_error($product_id) ? $product_id->get_error_message() : 'error';
        return $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'product', 'type_name' => $entry_type], 'error', $response);
      } else {
        $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'product', 'type_name' => $entry_type], 'success', $product_id);
      }
    }

    if ('customer' === $module) {
      $user_fields = ['user_pass', 'user_login', 'user_nicename', 'user_url', 'user_email', 'display_name', 'nickname', 'first_name', 'last_name', 'description', 'locale'];

      $user_inputs = array_intersect_key($fieldData, array_flip($user_fields));
      $meta_inputs = array_diff_key($fieldData, array_flip($user_fields));

      $fieldData = $user_inputs;
      $fieldData['role'] = $module;

      if (isset($id)) {
        $fieldData['ID'] = $id;
      }

      $user_id = wp_insert_user($fieldData);

      if (is_wp_error($user_id) || !$user_id) {
        $response = is_wp_error($user_id) ? $user_id->get_error_message() : 'error';
        return $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'customer', 'type_name' => $entry_type], 'error', $response);
      } else {
        $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'customer', 'type_name' => $entry_type], 'success', $user_id);
      }

      foreach ($meta_inputs as $metaKey => $metaValue) {
        update_user_meta($user_id, $metaKey, $metaValue);
      }

      do_action('woocommerce_update_customer',  $user_id);
    }

    $flag = null;
    if (isset($product_id)) {
      $basepath = BITFORMS_UPLOAD_DIR . DIRECTORY_SEPARATOR . $formID . DIRECTORY_SEPARATOR . $entryID . DIRECTORY_SEPARATOR;

      foreach ($uploadFieldMap as $uploadField) {
        if (!empty($uploadField->formField) && !empty($uploadField->wcField)) {
          if ('product_image' === $uploadField->wcField) {
            $flag = 0;
          }
          if ('product_gallery' === $uploadField->wcField) {
            $flag = 1;
          }
          if ('downloadable_files' === $uploadField->wcField) {
            $flag = 2;
          }

          $attach_ids = '';

          if (!empty($fieldValues[$uploadField->formField])) {
            $uplaodFiles = $fieldValues[$uploadField->formField];
            if ('string' === gettype($fieldValues[$uploadField->formField])) {
              $uplaodFiles = json_decode($fieldValues[$uploadField->formField]);
            }
            if (is_array($uplaodFiles)) {
              foreach ($uplaodFiles as $singleFile) {
                $url = $basepath . $singleFile;
                $attach_id = $this->attach_product_attachments($product_id, $flag, $url, $singleFile);
                if (1 === $flag && $attach_id) {
                  $attach_ids .= ',' . $attach_id;
                }
              }
            } else {
              $filename = $uplaodFiles;
              $url = $basepath . $filename;
              $this->attach_product_attachments($product_id, $flag, $url, $filename);
            }
          }

          if (1 === $flag) {
            update_post_meta($product_id, '_product_image_gallery', $attach_ids);
          }
        }
      }
    }
  }

  public function upload_attachment($product_id, $url)
  {
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $image_url = $url;
    $url_array = explode('/', $url);
    $image_name = $url_array[count($url_array) - 1];
    $image_data = file_get_contents($image_url);

    $upload_dir = wp_upload_dir();
    $unique_file_name = wp_unique_filename($upload_dir['path'], $image_name);
    $filename = basename($unique_file_name);

    if (wp_mkdir_p($upload_dir['path'])) {
      $file = $upload_dir['path'] . '/' . $filename;
    } else {
      $file = $upload_dir['basedir'] . '/' . $filename;
    }
    file_put_contents($file, $image_data);

    $wp_filetype = wp_check_filetype($filename, null);

    $attachment = [
      'post_mime_type' => $wp_filetype['type'],
      'post_title'     => sanitize_file_name($filename),
      'post_content'   => '',
      'post_status'    => 'inherit'
    ];

    $attach_id = wp_insert_attachment($attachment, $file, $product_id);

    $attach_data = wp_generate_attachment_metadata($attach_id, $file);

    wp_update_attachment_metadata($attach_id, $attach_data);

    return $attach_id;
  }

  public function attach_product_attachments($product_id, $flag, $url, $filename)
  {
    $attach_id = $this->upload_attachment($product_id, $url);
    if (0 === $flag) {
      set_post_thumbnail($product_id, $attach_id);
    }

    if (1 === $flag) {
      return $attach_id;
    }

    if (2 === $flag) {
      $this->attach_downloadable_attachments($product_id, $url, $filename);
    }
  }

  public function attach_downloadable_attachments($product_id, $url, $filename)
  {
    if ('yes' !== get_post_meta($product_id, '_downloadable', true)) {
      return false;
    }

    require_once dirname(WC_PLUGIN_FILE) . '/includes/wc-product-functions.php';

    $attach_id = $this->upload_attachment($product_id, $url);
    $download_id = md5($url);
    $file_url = wp_get_attachment_url($attach_id);

    $pd_object = new WC_Product_Download();
    $pd_object->set_id($download_id);
    $pd_object->set_name($filename);
    $pd_object->set_file($file_url);

    $product = wc_get_product($product_id);

    $downloads = $product->get_downloads();
    $downloads[$download_id] = $pd_object;

    $product->set_downloads($downloads);
    $product->save();
  }
}
