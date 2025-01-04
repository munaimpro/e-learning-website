<?php

?><?php

namespace BitCode\BitForm\Core\WorkFlow;

use BitCode\BitForm\Core\Integration\IntegrationHandler;
use BitCode\BitForm\Core\Messages\SuccessMessageHandler;
use BitCode\BitForm\Core\Util\Utilities;

final class Actions
{
  private static $_formID;

  public function __construct($formId)
  {
    static::$_formID = $formId;
  }

  public function setValue($actionDetail, $fieldData, $fields)
  {
    if (!empty($actionDetail->val)) {
      $actionValue = '';
      $fieldType = $fields->{$fieldData[$actionDetail->field]['key']}->typ;
      $evalMathExpr = preg_match('/month|date/', $fieldType);
      $fields->{$fieldData[$actionDetail->field]['key']}->val = '';
      $actionValue = Helper::replaceFieldWithValue($actionDetail->val, $fieldData, !(bool)$evalMathExpr);
      $fields->{$fieldData[$actionDetail->field]['key']}->val = $actionValue;
      $fieldData[$actionDetail->field]['value'] = $actionValue;
    }
    return [$fields, $fieldData];
  }

  public function getActionValue($actionDetail, $fieldData, $fields)
  {
    $actionValue = '';
    if (!empty($actionDetail->val)) {
      $fieldType = $fields->{$fieldData[$actionDetail->field]['key']}->typ;
      $evalMathExpr = preg_match('/month|date/', $fieldType);
      $fields->{$fieldData[$actionDetail->field]['key']}->val = '';
      $actionValue = Helper::replaceFieldWithValue($actionDetail->val, $fieldData, !(bool)$evalMathExpr);
    }
    return $actionValue;
  }

  public function getActiveListIndex($actionDetail, $fieldData, $fields)
  {
    $activeList = $this->getActionValue($actionDetail, $fieldData, $fields);
    $optionsList = $fields->{$fieldData[$actionDetail->field]['key']}->optionsList;
    $activeListIndex = 0;
    foreach ($optionsList as $key => $optionObj) {
      $valueArr = (array) $optionObj;
      $listName = array_keys($valueArr)[0];
      if ($listName === $activeList) {
        $activeListIndex = $key;
        break;
      }
    }

    return $activeListIndex;
  }

  public function show($fields, $fieldData, $actionDetail)
  {
    $fields->{$fieldData[$actionDetail->field]['key']}->valid->hide = false;
    if ('hidden' === $fields->{$fieldData[$actionDetail->field]['key']}->typ) {
      $fields->{$fieldData[$actionDetail->field]['key']}->typ = 'text';
    }
  }

  public function setFieldProperty($actions, $fieldData, $fields)
  {
    if (empty($actions)) {
      return [$fields, $fieldData];
    }
    foreach ($actions as $actionDetail) {
      if (!empty($actionDetail->action) && !empty($actionDetail->field) && isset($fields->{$fieldData[$actionDetail->field]['key']}) && !empty($fields->{$fieldData[$actionDetail->field]['key']})) {
        switch ($actionDetail->action) {
          case 'value':
            $data = $this->setValue($actionDetail, $fieldData, $fields);
            $fields = $data[0];
            $fieldData = $data[1];
            break;
          case 'hide':
            $fields->{$fieldData[$actionDetail->field]['key']}->valid->hide = true;
            break;
          case 'disable':
            $fields->{$fieldData[$actionDetail->field]['key']}->valid->disabled = true;
            break;
          case 'show':
            $this->show($fields, $fieldData, $actionDetail);
            break;
          case 'enable':
            $fields->{$fieldData[$actionDetail->field]['key']}->valid->disabled = false;
            break;
          case 'readonly':
            $fields->{$fieldData[$actionDetail->field]['key']}->valid->readonly = true;
            break;
          case 'writeable':
            $fields->{$fieldData[$actionDetail->field]['key']}->valid->readonly = false;
            break;
          case 'required':
            $fields->{$fieldData[$actionDetail->field]['key']}->valid->required = true;
            break;
          case 'limit':
            $fields->{$fieldData[$actionDetail->field]['key']}->valid->limit = true;
            break;
          case 'min':
            $fields->{$fieldData[$actionDetail->field]['key']}->valid->min = true;
            break;
          case 'max':
            $fields->{$fieldData[$actionDetail->field]['key']}->valid->max = true;
            break;
          case 'activelist':
            $fields->{$fieldData[$actionDetail->field]['key']}->config->activeList = $this->getActiveListIndex($actionDetail, $fieldData, $fields);
            break;
          case 'lbl':
          case 'ct':
            $fields->{$fieldData[$actionDetail->field]['key']}->lbl = $this->getActionValue($actionDetail, $fieldData, $fields);
            break;
          case 'sub-titl':
            $fields->{$fieldData[$actionDetail->field]['key']}->subtitle = $this->getActionValue($actionDetail, $fieldData, $fields);
            break;
          case 'hlp-txt':
            $fields->{$fieldData[$actionDetail->field]['key']}->helperTxt = $this->getActionValue($actionDetail, $fieldData, $fields);
            break;
          case 'placeholder':
            $fields->{$fieldData[$actionDetail->field]['key']}->ph = $this->getActionValue($actionDetail, $fieldData, $fields);
            break;
          case 'title':
            $fields->{$fieldData[$actionDetail->field]['key']}->title = $this->getActionValue($actionDetail, $fieldData, $fields);
            break;
        }
      }
    }
    return [$fields, $fieldData];
  }

  public function setOnSubmitSetFieldValue($actions, $fieldValue)
  {
    if (empty($actions)) {
      return $fieldValue;
    }
    foreach ($actions as $actionDetail) {
      if (!empty($actionDetail->action) && !empty($actionDetail->field)) {
        switch ($actionDetail->action) {
          case 'value':
            if (!empty($actionDetail->val)) {
              $fieldValue[$actionDetail->field] = '';
              $actionValue = Helper::replaceFieldWithValue($actionDetail->val, $fieldValue);
              $fieldValue[$actionDetail->field] = $actionValue;
            }
            break;
        }
      }
    }
    return $fieldValue;
  }

  public function setOnFormSuccess($data, $actions, $fieldValue)
  {
    if (empty($actions)) {
      return $data;
    }

    foreach ($actions as $successActionDetail) {
      $id = $successActionDetail->details->id;
      if (!empty($id)) {
        switch ($successActionDetail->type) {
          case 'successMsg':
            $data['workFlowReturnable'] = $this->confirmationMessage($data['workFlowReturnable'], $id, $fieldValue);
            break;
          case 'redirectPage':
            $data['workFlowReturnable'] = $this->redirectPage($data['workFlowReturnable'], $id, $fieldValue);
            break;
          case 'webHooks':
            if (!$data['isWebHookQueued']) {
              $data['isWebHookQueued'] = true;
            }
            $data['integrationsToExc'][] = $id;
            break;
          case 'mailNotify':
            $data['mailData'][] = $successActionDetail->details;
            break;
          case 'dblOptin':
            $data['dblOptin'][] = $successActionDetail->details;
            break;
          case 'integ':
            $data['integrationsToExc'][] = $id;
            break;
          default:
            break;
        }
      }
    }
    return $data;
  }

  public function confirmationMessage($workFlowReturnable, $successActionDetailId, $fieldValue)
  {
    $id = json_decode($successActionDetailId)->id;
    $messageHandler = new SuccessMessageHandler(static::$_formID);
    $message = $messageHandler->getAMessage($id);
    if (!is_wp_error($message) && !empty($message)) {
      $workFlowReturnable['message'] = Helper::replaceFieldWithValue($message[0]->message_content, $fieldValue);
      if (!empty($workFlowReturnable['message']) && Utilities::isPro()) {
        $workFlowReturnable['message'] = do_shortcode($workFlowReturnable['message']);
      }
      $workFlowReturnable['msg_id'] = $message[0]->id;
      $msgConfig = json_decode($message[0]->message_config);
      if ($msgConfig->autoHide) {
        $workFlowReturnable['msg_duration'] = abs(floatval($msgConfig->duration) * 1000);
      }
    }
    return $workFlowReturnable;
  }

  public function redirectPage($workFlowReturnable, $successActionDetailId, $fieldValue)
  {
    $id = json_decode($successActionDetailId)->id;
    $integrationHandler = new IntegrationHandler(static::$_formID);
    $redirectPage = $integrationHandler->getAIntegration($id, 'form', 'redirectPage');
    if (!is_wp_error($redirectPage) && !empty($redirectPage)) {
      $url = json_decode($redirectPage[0]->integration_details)->url;
      if (!empty($url)) {
        $url = Helper::replaceFieldWithValue($url, $fieldValue);
      }
      $workFlowReturnable['redirectPage'] = empty($url) ? false : esc_url_raw($url);
    }
    return $workFlowReturnable;
  }
}
