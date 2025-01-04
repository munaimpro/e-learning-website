<?php

namespace BitCode\BitForm\Core\Database;

use BitCode\BitForm\Core\Util\IpTool;

/**
 * Undocumented class
 */

class ApiModel extends Model
{
  public $_wpdb;

  public function __construct()
  {
    global $wpdb;
    $this->_wpdb = $wpdb;
  }

  public function getForm()
  {
    $result = $this->_wpdb->get_results(
      "
            SELECT form_name,id FROM `{$this->_wpdb->prefix}bitforms_form` WHERE `status`=1 order By created_at DESC
            "
    );
    return $result;
  }

  public function getField($id)
  {
    $result = $this->_wpdb->get_results(
      "
            SELECT form_content,id FROM `{$this->_wpdb->prefix}bitforms_form` WHERE `status`=1 AND `id`='$id'
            "
    );
    return $result;
  }

  public function editEntry($entryID)
  {
    $result = $this->_wpdb->get_results(
      "
            SELECT bitforms_form_entry_id,meta_key,meta_value FROM `{$this->_wpdb->prefix}bitforms_form_entrymeta` WHERE  `bitforms_form_entry_id`='$entryID'
            "
    );
    return $result;
  }

  public function entryDelete($entryID)
  {
    $sql = "DELETE FROM `{$this->_wpdb->prefix}bitforms_form_entrymeta` WHERE `bitforms_form_entry_id` = $entryID";
    $result = $this->_wpdb->query($sql);
    return $result;
  }

  public function findRecord($table_name, $column, $value)
  {
    $result = $this->_wpdb->get_results(
      "
            SELECT $column FROM `{$this->_wpdb->prefix}$table_name` WHERE  `$column`='$value'
            "
    );
    return $result;
  }

  public function noteCreate($formID, $entryID, $note_details)
  {
    $ipTool = new IpTool();
    $user_details = $ipTool->getUserDetail();
    $result = $this->_wpdb->insert(
      "{$this->_wpdb->prefix}bitforms_form_entry_relatedinfo",
      [
        'info_type'    => 'note',
        'info_details' => $note_details,
        'form_id'      => $formID,
        'entry_id'     => $entryID,
        'user_id'      => $user_details['id'],
        'user_ip'      => $user_details['ip'],
        'created_at'   => $user_details['time'],
      ]
    );
    return $result;
  }

  public function noteList()
  {
    $result = $this->_wpdb->get_results(
      "
            SELECT * FROM `{$this->_wpdb->prefix}bitforms_form_entry_relatedinfo` WHERE  `status`=1
            "
    );
    return $result;
  }

  public function getWorkFlow($formID)
  {
    $result = $this->_wpdb->get_results(
      "
            SELECT workflow_name,id FROM `{$this->_wpdb->prefix}bitforms_workflows` WHERE  `form_id`= $formID
            "
    );
    return $result;
  }

  public function noteUpdate($noteID, $note_details)
  {
    $data = ['info_details' => $note_details];
    $result = $this->_wpdb->update(
      "{$this->_wpdb->prefix}bitforms_form_entry_relatedinfo",
      $data,
      [
        'id' => $noteID,
      ]
    );
    return $result;
  }

  public function noteDelete($noteID)
  {
    $sql = "DELETE FROM `{$this->_wpdb->prefix}bitforms_form_entry_relatedinfo` WHERE `id` = $noteID";
    $result = $this->_wpdb->query($sql);
    return $result;
  }

  public function get_form_value($entryID)
  {
    $sql = "SELECT `meta_key`,`meta_value` FROM `{$this->_wpdb->prefix}bitforms_form_entrymeta` where bitforms_form_entry_id=$entryID";
    $result = $this->_wpdb->get_results($sql);
    return $result;
  }

  public function logUpdate($updateValue, $logID)
  {
    if (empty($logID)) {
      return false;
    }
    $sql = "UPDATE `{$this->_wpdb->prefix}bitforms_form_entry_log` SET content='$updateValue' WHERE id=$logID";
    $result = $this->_wpdb->get_results($sql);
    return $result;
  }

  public function getFormId($formID)
  {
    $sql = "SELECT form_id FROM `{$this->_wpdb->prefix}bitforms_form_entries` WHERE id=$formID";
    $result = $this->_wpdb->get_results($sql);
    return $result;
  }

  public function getOnSubmitWorkflow($formID)
  {
    $sql = "SELECT `id`, `workflow_name`, `workflow_type`, `workflow_run`, `workflow_behaviour` 
            FROM `{$this->_wpdb->prefix}bitforms_workflows` 
            WHERE `form_id`=$formID AND `workflow_type`='onsubmit' order by id desc";
    $result = $this->_wpdb->get_results($sql);
    return $result;
  }
}
