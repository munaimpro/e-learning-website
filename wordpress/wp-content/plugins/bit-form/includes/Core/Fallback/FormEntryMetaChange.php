<?php

namespace BitCode\BitForm\Core\Fallback;

use BitCode\BitForm\Admin\Form\AdminFormManager;
use BitCode\BitForm\Core\Database\EmailTemplateModel;
use BitCode\BitForm\Core\Database\FallbackModel;
use BitCode\BitForm\Core\Database\SuccessMessageModel;
use BitCode\BitForm\Core\Form\FormHandler;
use BitCode\BitForm\Core\Integration\IntegrationHandler;
use BitCode\BitForm\Core\WorkFlow\WorkFlowHandler;

class FormEntryMetaChange
{
  private $formID;

  private $fieldKey;

  private $field_name;

  private $fallbackModel;

  public function __construct()
  {
    $this->fallbackModel = new FallbackModel();
  }

  public function changeMeta()
  {
    $formHandler = FormHandler::getInstance();
    $all_forms = $formHandler->admin->getAllForm();
    foreach ($all_forms as $form) {
      $this->formID = $form->id;
      $formManager = new AdminFormManager($this->formID);
      $allFields = $formManager->getFormContent()->fields;
      foreach ($allFields as $fieldKey => $fieldVal) {
        $this->fieldKey = $fieldKey;
        $this->field_name = preg_replace('/[\`\~\!\@\#\$\'\.\s\?\+\-\*\&\|\/\\\!]/', '_', $fieldVal->lbl);
        $this->fallbackModel->changeMetaKey($this->fieldKey, $this->field_name);
        $this->changeIntegKey();
        $this->changeWorkflowKey();
        $this->changeSuccessMessageKey();
        $this->changeEmailTempKey();
      }
    }
  }

  private function changeIntegKey()
  {
    $integrationHandler = new IntegrationHandler($this->formID);

    $formIntegrations = $integrationHandler->getAllIntegration();

    foreach ($formIntegrations as $integration) {
      $integId = $integration->id;
      $integstr = str_replace("{$this->fieldKey}{$this->field_name}", $this->fieldKey, $integration->integration_details);
      $this->fallbackModel->changeIntegKey($integId, $integstr);
    }
  }

  private function changeWorkflowKey()
  {
    $workflowHandler = new WorkFlowHandler($this->formID);
    $formWorkflows = $workflowHandler->getAllworkFlow();
    foreach ($formWorkflows as $workflow) {
      $workflowId = $workflow['id'];
      $logics = wp_json_encode($workflow['logics']);
      $actions = wp_json_encode($workflow['actions']);
      $successAction = wp_json_encode($workflow['successAction']);
      $workflowAction = '{"action":' . $actions . ',"successAction":' . $successAction . '}';

      $logics = str_replace("{$this->fieldKey}{$this->field_name}", $this->fieldKey, $logics);
      $workflowAction = str_replace("{$this->fieldKey}{$this->field_name}", $this->fieldKey, $workflowAction);

      $this->fallbackModel->changeWorkflowKey($workflowId, $logics, $workflowAction);
    }
  }

  private function changeSuccessMessageKey()
  {
    $successMessageModel = new SuccessMessageModel();
    $allSuccessMessages = $successMessageModel->get();

    foreach ($allSuccessMessages as $message) {
      $messageId = $message->id;
      $title = str_replace("{$this->fieldKey}{$this->field_name}", $this->fieldKey, $message->message_title);
      $content = str_replace("{$this->fieldKey}{$this->field_name}", $this->fieldKey, $message->message_content);
      $this->fallbackModel->changeSuccessMessageKey($messageId, $title, $content);
    }
  }

  private function changeEmailTempKey()
  {
    $emailTemplateModel = new EmailTemplateModel();
    $allEmailTemp = $emailTemplateModel->get();

    foreach ($allEmailTemp as $temp) {
      $tempId = $temp->id;
      $sub = str_replace("{$this->fieldKey}{$this->field_name}", $this->fieldKey, $temp->sub);
      $body = str_replace("{$this->fieldKey}{$this->field_name}", $this->fieldKey, $temp->body);
      $this->fallbackModel->changeEmailTempKey($tempId, $sub, $body);
    }
  }
}
