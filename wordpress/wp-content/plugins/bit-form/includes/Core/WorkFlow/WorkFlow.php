<?php

namespace BitCode\BitForm\Core\WorkFlow;

use BitCode\BitForm\Admin\Form\AdminFormManager;
use BitCode\BitForm\Core\Database\FormEntryMetaModel;
use BitCode\BitForm\Core\Database\WorkFlowModel;
use BitCode\BitForm\Core\Integration\IntegrationHandler;
use BitCode\BitForm\Core\Integration\Integrations;
use BitCode\BitForm\Core\Util\MailNotifier;

final class WorkFlow
{
  private static $_formID;
  private static $_workFlowModel;
  private $_actions;

  private $_user_details;

  public function __construct($formID, $user_details = null)
  {
    static::$_formID = $formID;
    static::$_workFlowModel = new WorkFlowModel();
    $this->_user_details = $user_details;
    $this->_actions = new Actions($formID);
  }

  public function getWorkFlow($workFlowRun, $workFlowType, $workFlowIds = null, $orderColumn = 'id')
  {
    if (null === $workFlowIds) {
      $condition = [
        'form_id' => static::$_formID,
      ];
    } else {
      $condition = [
        'id' => [$workFlowIds],
      ];
    }
    if (!empty($workFlowRun)) {
      $condition = \array_merge(
        $condition,
        [
          'workflow_run' => $workFlowRun,
        ]
      );
    }
    if (!empty($workFlowType)) {
      if (!is_array($workFlowType)) {
        $workFlowType = [$workFlowType];
      }
      $condition = \array_merge(
        $condition,
        [
          'workflow_type' => $workFlowType,
        ]
      );
    }
    $workFlows = static::$_workFlowModel->get(
      [
        'id',
        'workflow_type',
        'workflow_run',
        'workflow_behaviour',
        'workflow_condition',
        'workflow_name',
        'workflow_order',
      ],
      $condition,
      null,
      null,
      $orderColumn,
      'desc'
    );
    if (empty($workFlows) || is_wp_error($workFlows)) {
      return [];
    }

    return $workFlows;
  }

  public function executeOnLoad($workFlowRun, $fields)
  {
    $workFlows = $this->getWorkFlow(['create_edit', $workFlowRun], ['always', 'onload'], null, 'workflow_order');
    $workFlowReturnable = [];
    if (empty($workFlows) || is_wp_error($workFlows)) {
      return [];
    }
    $fieldData = Helper::getFieldData($fields);
    foreach ($workFlows as $workFlow) {
      $conditions = json_decode($workFlow->workflow_condition);
      $conditionBehaviour = $workFlow->workflow_behaviour;
      if ('cond' === $conditionBehaviour) {
        foreach ($conditions as $condition) {
          $type = $condition->cond_type;
          if (!empty($condition->actions->fields)) {
            $conditionStatus = false;

            if (!empty($condition->logics)) {
              $logics = $condition->logics;
              $fieldData = Helper::smartFldMargeFormFld($logics, $fieldData);
              $conditionalLogic = new ConditionalLogic($logics, $fieldData);
              $conditionStatus = $conditionalLogic->getConditionStatus();
            }
            if (($conditionStatus && in_array($type, ['if', 'else-if']) || 'else' === $type)) {
              $data = $this->_actions->setFieldProperty($condition->actions->fields, $fieldData, $fields);
              $fields = $data[0];
              break;
            }
          }
        }
      } elseif ('always' === $conditionBehaviour) {
        if (!empty($conditions[0]->actions->fields)) {
          $data = $this->_actions->setFieldProperty($conditions[0]->actions->fields, $fieldData, $fields);
          $fields = $data[0];
          $fieldData = $data[1];
        }
      }
    }
    $workFlowReturnable['fields'] = $fields;

    return $workFlowReturnable;
  }

  public function executeOnUserInput($workFlowRun)
  {
    $workFlows = $this->getWorkFlow(['create_edit', $workFlowRun], ['always', 'oninput'], null, 'workflow_order');
    $workFlowReturnable = [];
    if (empty($workFlows) || is_wp_error($workFlows)) {
      return $workFlowReturnable;
    }
    $onUserInputRun = [];
    foreach ($workFlows as $index => $value) {
      $conditions = json_decode($value->workflow_condition);
      $onUserInputRun['event_type'] = 'on_input';
      $onUserInputRun['conditions'] = $conditions;

      $workFlowReturnable['onfield_input_conditions'][$index] = $onUserInputRun;
    }
    return $workFlowReturnable;
  }

  public function executeOnValidate($workFlowRun, $fieldData, $fieldValue)
  {
    $workFlows = $this->getWorkFlow(['create_edit', $workFlowRun], 'onvalidate', null, 'workflow_order');
    $workFlowReturnable = [];
    if (empty($workFlows) || is_wp_error($workFlows)) {
      return [];
    }

    foreach ($workFlows as $workFlow) {
      $conditions = json_decode($workFlow->workflow_condition);
      $conditionBehaviour = $workFlow->workflow_behaviour;
      if ('cond' === $conditionBehaviour) {
        foreach ($conditions as $condition) {
          $type = $condition->cond_type;
          if (!empty($condition->actions->failure)) {
            $validateMsg = $condition->actions->failure;
          } else {
            $validateMsg = '{"id":"0"}';
          }
          $conditionStatus = false;
          if (!empty($condition->logics)) {
            $logics = $condition->logics;
            $fieldData = Helper::smartFldMargeFormFld($logics, $fieldData);
            $conditionalLogic = new ConditionalLogic($logics, $fieldData);
            $conditionStatus = $conditionalLogic->getConditionStatus();
          }
          if (($conditionStatus && in_array($type, ['if', 'else-if'])) || 'else' === $type) {
            $workFlowReturnable = $this->_actions->confirmationMessage($workFlowReturnable, $validateMsg, $fieldValue);
            if (empty($workFlowReturnable)) {
              $workFlowReturnable['message'] = '<p>Something error in Form Validation</p>';
            }
            break;
          }
        }
      }
    }
    return $workFlowReturnable;
  }

  public function isExistDoubleOptin($data)
  {
    $data['integrationRun'] = true;
    if (has_action('bf_double_optin_confirmation')) {
      $activeDoubleOpt = (new IntegrationHandler(static::$_formID))->getAllIntegration('double-opt-in', 'double-opt-in', 1);

      if (!is_wp_error($activeDoubleOpt) && count($activeDoubleOpt) > 0) {
        $dplOptinDetails = json_decode($activeDoubleOpt[0]->integration_details);
        if (isset($dplOptinDetails->disable_loggin_user) && !is_user_logged_in() || !isset($dplOptinDetails->disable_loggin_user)) {
          $data['integrationRun'] = false;
          $data['integrationDetails'] = $activeDoubleOpt[0];
          $data['dflt_template'] = isset($dplOptinDetails->dflt_temp) ? true : false;
        }
      }
    }
    return $data;
  }

  public function executeOnSubmit($workFlowRun, $fields, $fieldValue, $entryID, $logID, $workflowsIds = null)
  {
    $workFlows = $this->getWorkFlow(['create_edit', $workFlowRun], ['onsubmit'], $workflowsIds, 'workflow_order');
    $workFlowReturnable = [];
    $workFlowReturnable = $this->isExistDoubleOptin($workFlowReturnable);
    if (empty($workFlows) || is_wp_error($workFlows)) {
      $defaultConfimation = Helper::setDefaultSubmitConfirmation('successMsg', $fieldValue, static::$_formID, 0);
      if (empty($defaultConfimation['confirmation'])) {
        $workFlowReturnable['message'] = 'edit' !== $workFlowRun ? __('Form Submitted Successfully', 'bit-form')
        : __('Entry Updated Successfully', 'bit-form');
      } else {
        $workFlowReturnable['message'] = $defaultConfimation['confirmation'];
      }
      $workFlowReturnable['msg_id'] = 0;
      $workFlowReturnable['dflt_message'] = true;
      $defaultConfimation = Helper::setDefaultSubmitConfirmation('redirectPage', $fieldValue, static::$_formID, 0);

      $workFlowReturnable['redirectPage'] = $defaultConfimation['confirmation'];
      $data = [
        'integrations' => [Helper::setDefaultSubmitConfirmation('webHooks', $fieldValue, static::$_formID, $logID)],
        'entryID'      => $entryID,
        'logID'        => $logID,
        'formID'       => static::$_formID,
      ];
      $workFlowReturnable['triggerData'] = $data;
      $workFlowReturnable['cron'] = false;
      return $workFlowReturnable;
    }

    $fieldData = Helper::getFieldData($fields);

    $isCronOK = !defined('DOING_CRON') && wp_doing_ajax() && (!defined('DISABLE_WP_CRON') || (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON));
    if ($isCronOK) {
      // From wp spawn_cron()
      $gmt_time = microtime(true);
      $lock = get_transient('doing_cron');
      if ($lock > $gmt_time + 10 * MINUTE_IN_SECONDS) {
        $lock = 0;
      }
      if ($lock + WP_CRON_LOCK_TIMEOUT > $gmt_time) {
        $isCronOK = false;
      }
    }

    $onFormSuccessActionDefault = [
      'workFlowReturnable' => $workFlowReturnable,
      'mailData'           => [],
      'dblOptin'           => [],
      'integrationsToExc'  => [],
      'isWebHookQueued'    => false,
    ];

    foreach ($workFlows as $workFlow) {
      $conditions = json_decode($workFlow->workflow_condition);

      $conditionBehaviour = $workFlow->workflow_behaviour;
      if ('cond' === $conditionBehaviour) {
        foreach ($conditions as $condition) {
          $type = $condition->cond_type;
          if (!empty($condition->actions)) {
            $actions = $condition->actions;
            $conditionStatus = false;
            if (!empty($condition->logics)) {
              $logics = $condition->logics;
              $fieldData = Helper::smartFldMargeFormFld($logics, $fieldData);
              $conditionalLogic = new ConditionalLogic($logics, $fieldData);
              $conditionStatus = $conditionalLogic->getConditionStatus();
            }
            $conditionStatus = apply_filters('bitform_filter_workflow_condition_status', $conditionStatus, $condition, $fieldData, $this::$_formID);
            if (($conditionStatus && in_array($type, ['if', 'else-if'])) || 'else' === $type) {
              $fieldValue = $this->_actions->setOnSubmitSetFieldValue($actions->fields, $fieldValue);
              $onFormSuccessActionDefault = $this->_actions->setOnFormSuccess($onFormSuccessActionDefault, $actions->success, $fieldValue);
              $onFormSuccessActionDefault['workFlowReturnable']['fields'] = $fieldValue;
              break;
            }
          }
        }
      } elseif ('always' === $conditionBehaviour) {
        $actions = $conditions[0]->actions;
        $fieldValue = $this->_actions->setOnSubmitSetFieldValue($actions->fields, $fieldValue);
        $onFormSuccessActionDefault = $this->_actions->setOnFormSuccess($onFormSuccessActionDefault, $actions->success, $fieldValue);
        $onFormSuccessActionDefault['workFlowReturnable']['fields'] = $fieldValue;
      }
    }

    $workFlowReturnable = $onFormSuccessActionDefault['workFlowReturnable'];
    $integrationsToExc = $onFormSuccessActionDefault['integrationsToExc'];

    if (empty($workFlowReturnable['message'])) {
      $defaultConfimation = Helper::setDefaultSubmitConfirmation('successMsg', $fieldValue, static::$_formID, $logID);
      $workFlowReturnable['message'] = $defaultConfimation['confirmation'];
      $workFlowReturnable['msg_id'] = 0;

      if (empty($defaultConfimation['confirmation'])) {
        $workFlowReturnable['message'] = 'edit' !== $workFlowRun ? __('Form Submitted Successfully', 'bit-form')
        : __('Entry Updated Successfully', 'bit-form');
      }
      $workFlowReturnable['dflt_message'] = true;
    }
    if (empty($workFlowReturnable['redirectPage'])) {
      $defaultConfimation = Helper::setDefaultSubmitConfirmation('redirectPage', $fieldValue, static::$_formID, $logID);

      $workFlowReturnable['redirectPage'] = $defaultConfimation['confirmation'];
    }
    if (!$onFormSuccessActionDefault['isWebHookQueued']) {
      //checked korte hobe
      $webWooks = Helper::setDefaultSubmitConfirmation('webHooks', $fieldValue, 1, static::$_formID);
      if (!empty($webWooks['confirmation'])) {
        $integrationsToExc[] = $webWooks['confirmation'];
      }
    }

    // if (!empty($integrationsToExc)) {
    $data = [
      'mail'         => $onFormSuccessActionDefault['mailData'],
      'dblOptin'     => $onFormSuccessActionDefault['dblOptin'],
      'integrations' => $integrationsToExc,
      'entryID'      => $entryID,
      'logID'        => $logID,
      'formID'       => static::$_formID,
    ];
    $workFlowReturnable['triggerData'] = $data;
    if ($isCronOK) {
      $workFlowReturnable['cron'] = true;
    } else {
      $workFlowReturnable['cron'] = false;
    }
    // }
    return $workFlowReturnable;
  }

  public function executeOnDelete(AdminFormManager $formManager, $formID, $detetedIds)
  {
    $workFlows = $this->getWorkFlow(['delete'], 'delete');
    $workFlowReturnable = [];
    if (empty($workFlows) || is_wp_error($workFlows) || empty($detetedIds)) {
      return [];
    }
    if (!$formManager instanceof AdminFormManager) {
      $formManager = new AdminFormManager($formID);
    }
    $returnableEntries = $detetedIds;
    $formFields = $formManager->getFieldLabel();
    $entryMeta = new FormEntryMetaModel();
    $entries = $entryMeta->getEntryMeta(
      $formFields,
      $detetedIds
    );
    if (is_wp_error($entries) || empty($entries['entries'])) {
      return $entries;
    }
    foreach ($entries['entries'] as $entry) {
      $fieldValue = (array) $entry;
      unset($fieldValue['entry_id']);
      $fields = $formManager->getFormContentWithValue($fieldValue)->fields;
      $fieldData = Helper::getFieldData($fields);
      foreach ($workFlows as $workFlow) {
        $workFlowBlock = json_decode($workFlow->workflow_condition);
        $conditions = $workFlowBlock->logics;
        $actions = $workFlowBlock->actions;
        $conditionBehaviour = $workFlow->workflow_behaviour;
        if ('cond' === $conditionBehaviour) {
          foreach ($conditions as $condition) {
            $type = $condition->cond_type;
            $conditionalLogic = new ConditionalLogic($condition, $fieldData);
            $conditionStaus = $conditionalLogic->getConditionStatus();
            if (($conditionStaus && in_array($type, ['if', 'else-if'])) || 'else' === $type) {
              $isExists = \array_search($entry->entry_id, $returnableEntries);
              if (!empty($actions->avoid_delete) && false !== $isExists) {
                unset($returnableEntries[$isExists]);
                $returnableEntries = array_values($returnableEntries);
              } elseif (false === $isExists) {
                $returnableEntries[] = $entry->entry_id;
              }
              if (!empty($actions->success)) {
                foreach ($actions->success as $successActionDetail) {
                  switch ($successActionDetail->type) {
                    case 'webHooks':
                      if (!empty($successActionDetail->details->id)) {
                        $webHooks = $successActionDetail->details->id;
                        Integrations::executeIntegrations($webHooks, $fieldValue, static::$_formID);
                      }
                      break;
                    case 'mailNotify':
                      if (!empty($successActionDetail->details->id)) {
                        MailNotifier::notify($successActionDetail->details, static::$_formID, $fieldValue, $entry->entry_id);
                      }
                      break;
                    default:
                      break;
                  }
                }
              }
              break;
            }
          }
        }
      }
    }
    // }
    $workFlowReturnable['entries'] = array_values($returnableEntries);
    return $workFlowReturnable;
  }
}
