<?php

namespace BitCode\BitForm\Core\Database;

class FallbackModel extends Model
{
  public function changeMetaKey($fieldKey, $name)
  {
    $prefix = $this->app_db->prefix;
    $sql = "UPDATE {$prefix}bitforms_form_entrymeta SET meta_key = '{$fieldKey}' WHERE meta_key = '{$fieldKey}{$name}'";

    return $this->execute($sql);
  }

  public function changeIntegKey($integId, $conf)
  {
    $prefix = $this->app_db->prefix;
    $sql = "UPDATE {$prefix}bitforms_integration SET integration_details = '%s' WHERE id = %d";
    $values = [$conf, $integId];

    return $this->execute($sql, $values)->getResult();
  }

  public function changeWorkflowKey($workflowId, $logics, $workflowAction)
  {
    $prefix = $this->app_db->prefix;
    $sql = "UPDATE {$prefix}bitforms_workflows SET workflow_condition = '%s', workflow_action = '%s' WHERE id = %d";
    $values = [$logics, $workflowAction, $workflowId];
    return $this->execute($sql, $values)->getResult();
  }

  public function changeSuccessMessageKey($messageId, $title, $content, $config)
  {
    $prefix = $this->app_db->prefix;
    $sql = "UPDATE {$prefix}bitforms_success_messages SET message_title = '%s', message_content = '%s', message_config = '%s' WHERE id = %d";
    $values = [$title, $content, $config, $messageId];
    return $this->execute($sql, $values)->getResult();
  }

  public function changeEmailTempKey($tempId, $sub, $body)
  {
    $prefix = $this->app_db->prefix;
    $sql = "UPDATE {$prefix}bitforms_email_template SET sub = '%s', body = '%s' WHERE id = %d";
    $values = [$sub, $body, $tempId];
    return $this->execute($sql, $values)->getResult();
  }
}
