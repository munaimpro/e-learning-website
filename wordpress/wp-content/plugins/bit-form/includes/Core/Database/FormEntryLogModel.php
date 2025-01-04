<?php

namespace BitCode\BitForm\Core\Database;

use WP_Error;

/**
 * Undocumented class
 */

class FormEntryLogModel extends Model
{
  protected static $table = 'bitforms_form_log_details';

  public function geLogHistory($form_id, $entry_id)
  {
    $sql = "SELECT * FROM `{$this->app_db->prefix}bitforms_form_entry_log` where form_entry_id=%d AND form_id=%d order By created_at DESC";
    $logs = $this->execute($sql, [$entry_id, $form_id])->getResult();
    $response = ['success' => true, 'data' => [], 'integrations' => []];
    $ids = [];
    foreach ($logs as $log) {
      $ids[] = (int) $log->id;
    }
    if (isset($logs->errors['result_empty'])) {
      wp_send_json($response);
    } else {
      $allLogId = preg_replace('/"/', '', implode(',', $ids));
      $sql2 = "SELECT * FROM `{$this->app_db->prefix}bitforms_form_log_details` WHERE `log_id` IN ($allLogId)";
      $integrations = $this->execute($sql2)->getResult();

      if (isset($integrations->errors['result_empty'])) {
        $integrations = [];
      } else {
        foreach ($logs as  $key => $log) {
          foreach ($integrations as $integration) {
            if ($integration->log_id === $log->id) {
              $logs[$key]->integration = true;
            }
          }
        }
      }

      wp_send_json(['success' => true, 'data' => $logs, 'integrations' => $integrations]);
    }
  }

  public function entryLogCheck($entry_id, $integ_id)
  {
    if (is_null($entry_id)) {
      return new WP_Error('empty_data', __('Form data is empty', 'bit-form'));
    }
    $sql = "SELECT api_type, response_obj FROM `{$this->app_db->prefix}bitforms_form_entry_log` as el JOIN `{$this->app_db->prefix}bitforms_form_log_details` as ld ON ld.log_id = el.id WHERE form_entry_id = {$entry_id}  AND integration_id = {$integ_id} AND ld.response_type = 'success' ORDER BY el.id DESC LIMIT 1";

    return $this->app_db->get_results($sql);
  }

  public function form_log_insert($data = [])
  {
    if (is_null($data)) {
      return new WP_Error('empty_data', __('Form data is empty', 'bit-form'));
    }
    $result = $this->app_db->insert(
      "{$this->app_db->prefix}bitforms_form_entry_log",
      $data
    );
    return $this->getResult($result);
  }

  public function log_history_insert($data = [])
  {
    if (is_null($data)) {
      return new WP_Error('empty_data', __('Form data is empty', 'bit-form'));
    }
    $result = $this->app_db->insert(
      "{$this->app_db->prefix}bitforms_form_log_details",
      $data
    );
    return $this->getResult($result);
  }

  public function get_form_value($form_id = '')
  {
    $sql = "SELECT `meta_key`,`meta_value` FROM `{$this->app_db->prefix}bitforms_form_entrymeta` where bitforms_form_entry_id=$form_id";
    return $this->execute($sql)->getResult();
  }

  public function logUpdate($updateValue, $logID)
  {
    if (empty($logID)) {
      return false;
    }
    $sql = "UPDATE `{$this->app_db->prefix}bitforms_form_entry_log` SET content='$updateValue' WHERE id=$logID";
    return $this->execute($sql)->getResult();
  }
}
