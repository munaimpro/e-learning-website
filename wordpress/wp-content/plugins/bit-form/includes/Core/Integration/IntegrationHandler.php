<?php

namespace BitCode\BitForm\Core\Integration;

use BitCode\BitForm\Admin\Form\AdminFormManager;
use BitCode\BitForm\Admin\Form\Helpers;
use BitCode\BitForm\Core\Database\FormEntryLogModel;
use BitCode\BitForm\Core\Database\FormEntryModel;
use BitCode\BitForm\Core\Database\IntegrationModel;
use BitCode\BitForm\Core\Util\FieldValueHandler;
use BitCode\BitForm\Core\Util\MailNotifier;
use BitCode\BitForm\Frontend\Form\FrontendFormManager;

final class IntegrationHandler
{
  private static $_formID;
  private static $_integrationModel;
  private $_user_details;

  /**
   * Constructor of Integration Handler
   *
   * @param  Integer $formID       If Integration is accessible globally then
   *                               $formID will be 0 and category app
   * @param  Array   $user_details Details of user accessing data
   * @return void
   */
  public function __construct($formID, $user_details = null)
  {
    static::$_formID = $formID;
    static::$_integrationModel = new IntegrationModel();
    $this->_user_details = $user_details;
  }

  public function getAIntegration($integrationID, $integrationCategory = null, $integrationType = null)
  {
    $conditions = [
      'form_id' => static::$_formID,
      'id'      => $integrationID,
    ];
    if (!is_null($integrationType)) {
      $conditions = array_merge($conditions, ['integration_type' => $integrationType]);
    }
    if (!is_null($integrationCategory)) {
      $conditions = array_merge($conditions, ['category' => $integrationCategory]);
    }
    return static::$_integrationModel->get(
      [
        'id',
        'integration_name',
        'integration_type',
        'integration_details',
        'form_id',
      ],
      $conditions
    );
  }

  public function getAllIntegration($integrationCategory = null, $integrationType = null, $status = null, $id = null)
  {
    $conditions = [
      'form_id' => static::$_formID,
    ];
    if (!is_null($integrationType)) {
      $conditions = array_merge($conditions, ['integration_type' => $integrationType]);
    }

    if (!is_null($integrationCategory)) {
      $conditions = array_merge($conditions, ['category' => $integrationCategory]);
    }
    if (!is_null($status)) {
      $conditions = array_merge($conditions, ['status' => 1]);
    }
    if (!is_null($id)) {
      $conditions = array_merge($conditions, ['id' => $id]);
    }
    return static::$_integrationModel->get(
      [
        'id',
        'form_id',
        'integration_name',
        'integration_type',
        'integration_details',
        'status',
      ],
      $conditions
    );
  }

  public function getIntegrationWithoutFormId($integrationCategory = null, $integrationType = null, $status = null, $id = null)
  {
    $conditions = [];
    if (!is_null($integrationType)) {
      $conditions = array_merge($conditions, ['integration_type' => $integrationType]);
    }

    if (!is_null($integrationCategory)) {
      $conditions = array_merge($conditions, ['category' => $integrationCategory]);
    }
    if (!is_null($status)) {
      $conditions = array_merge($conditions, ['status' => 1]);
    }
    if (!is_null($id)) {
      $conditions = array_merge($conditions, ['id' => $id]);
    }
    return static::$_integrationModel->get(
      [
        'id',
        'form_id',
        'integration_name',
        'integration_type',
        'integration_details',
        'status',
      ],
      $conditions
    );
  }

  public function saveIntegration($integrationName, $integrationType, $integrationDetails, $integrationCategory, $status = null)
  {
    if (null === $status) {
      $status = 1;
    }
    return static::$_integrationModel->insert(
      [
        'integration_name'    => $integrationName,
        'integration_type'    => $integrationType,
        'integration_details' => $integrationDetails,
        'category'            => $integrationCategory,
        'form_id'             => static::$_formID,
        'user_id'             => $this->_user_details['id'],
        'user_ip'             => $this->_user_details['ip'],
        'status'              => $status,
        'created_at'          => $this->_user_details['time'],
        'updated_at'          => $this->_user_details['time'],
      ]
    );
  }

  public function updateIntegration($integrationID, $integrationName, $integrationType, $integrationDetails, $integrationCategory, $status = null)
  {
    if (null === $status) {
      $status = 1;
    }
    return static::$_integrationModel->update(
      [
        'integration_name'    => $integrationName,
        'integration_type'    => $integrationType,
        'integration_details' => $integrationDetails,
        'category'            => $integrationCategory,
        'form_id'             => static::$_formID,
        'user_id'             => $this->_user_details['id'],
        'user_ip'             => $this->_user_details['ip'],
        'status'              => $status,
        'updated_at'          => $this->_user_details['time'],
      ],
      [
        'id' => $integrationID,
      ]
    );
  }

  public function duplicateAllInAForm($oldFormId)
  {
    $integCols = ['integration_name', 'integration_type', 'integration_details', 'category', 'form_id', 'user_id', 'user_ip', 'status', 'created_at', 'updated_at'];
    $integDupData = [
      'integration_name',
      'integration_type',
      'integration_details',
      'category',
      static::$_formID,
      $this->_user_details['id'],
      $this->_user_details['ip'],
      'status',
      $this->_user_details['time'],
      $this->_user_details['time'],
    ];
    return static::$_integrationModel->duplicate($integCols, $integDupData, ['form_id' => $oldFormId]);
  }

  public function deleteIntegration($integrationID)
  {
    return static::$_integrationModel->delete(
      [
        'id'      => $integrationID,
        'form_id' => static::$_formID,
      ]
    );
  }

  public static function replaceFieldWithValue($dataToReplaceField, $fieldValues)
  {
    if (empty($dataToReplaceField)) {
      return false;
    }
    if (is_string($dataToReplaceField)) {
      $dataToReplaceField = static::replaceFieldWithValueHelper($dataToReplaceField, $fieldValues);
    } elseif (is_array($dataToReplaceField)) {
      foreach ($dataToReplaceField as $field => $value) {
        if (is_array($value) && 1 === count($value)) {
          $dataToReplaceField[$field] = static::replaceFieldWithValueHelper($value[0], $fieldValues);
        } elseif (is_array($value)) {
          $dataToReplaceField[$field] = static::replaceFieldWithValue($value, $fieldValues);
        } else {
          $dataToReplaceField[$field] = static::replaceFieldWithValueHelper($value, $fieldValues);
        }
      }
    }
    return $dataToReplaceField;
  }

  private static function replaceFieldWithValueHelper($stringToReplaceField, $fieldValues)
  {
    if (empty($stringToReplaceField)) {
      return $stringToReplaceField;
    }
    $fieldPattern = '/\${\w[^ ${}]*}/';
    preg_match_all($fieldPattern, $stringToReplaceField, $matchedField);
    $uniqueFieldsInStr = array_unique($matchedField[0]);
    foreach ($uniqueFieldsInStr as $key => $value) {
      $fieldName = substr($value, 2, strlen($value) - 3);

      $repeaterArr = explode('.', $fieldName);

      if (isset($fieldValues[$fieldName])) {
        $stringToReplaceField = is_string($fieldValues[$fieldName])
        ? str_replace($value, $fieldValues[$fieldName], $stringToReplaceField)
        : wp_json_encode($fieldValues[$fieldName]);
      } elseif (2 === count($repeaterArr) && isset($fieldValues[$repeaterArr[0]])) {
        $repeaterValues = [];
        $repeaterFieldValues = $fieldValues[$repeaterArr[0]];

        foreach ($repeaterFieldValues as $value) {
          if (isset($value[$repeaterArr[1]])) {
            $repeaterValues[] = $value[$repeaterArr[1]];
          }
        }

        $stringToReplaceField = $repeaterValues;
      }
    }
    return $stringToReplaceField;
  }

  public static function updatedHiddenFieldValue($formID)
  {
    $hiddenFields = [];
    $frontendFormManger = new FrontendFormManager($formID);

    if ($frontendFormManger->isHoneypotActive()) {
      $str = '_bitform' . '_' . $formID . '_' . time() . '_';
      $honpotToken = Helpers::honeypotEncryptedToken($str);
      $hiddenFields[] = [
        'name'  => 'b_h_t',
        'value' => $honpotToken
      ];
    }
    $csrfTokens = Helpers::csrfEecrypted();
    $hiddenFields[] = [
      'name'  => 't_identity',
      'value' => $csrfTokens['t_identity'],
    ];
    $hiddenFields[] = [
      'name'  => 'csrf',
      'value' => $csrfTokens['csrf']
    ];
    return $hiddenFields;
  }

  public static function maybeSetCronForIntegration($workFlowReturnedData, $opType, $isFormRequest = true)
  {
    $responseData = [];
    $entryId = $workFlowReturnedData['triggerData']['entryID'];
    $responseData['entry_id'] = $entryId;
    if (isset($workFlowReturnedData['message'])) {
      $formID = $workFlowReturnedData['triggerData']['formID'];
      $responseData['message'] = $workFlowReturnedData['message'];
      $responseData['hidden_fields'] = self::updatedHiddenFieldValue($formID);
      $responseData['msg_id'] = isset($workFlowReturnedData['msg_id']) ? $workFlowReturnedData['msg_id'] : 0;
      if (isset($workFlowReturnedData['msg_duration'])) {
        $responseData['msg_duration'] = $workFlowReturnedData['msg_duration'];
      }
    }
    if (isset($workFlowReturnedData['redirectPage'])) {
      $responseData['redirectPage'] = $workFlowReturnedData['redirectPage'];
    }
    if ('update' === $opType && isset($workFlowReturnedData['updatedData'])) {
      $responseData['updatedData'] = $workFlowReturnedData['updatedData'];
    }
    if (!isset($workFlowReturnedData['triggerData'])) {
      return $responseData;
    }

    $triggerData = $workFlowReturnedData['triggerData'];

    $trnasientData = get_transient("bitform_trigger_transient_{$entryId}");
    $trnasientData = is_string($trnasientData) ? json_decode($trnasientData) : $trnasientData;
    if (!empty($trnasientData['fields'])) {
      $workFlowReturnedData['fields'] = array_merge($workFlowReturnedData['fields'], $trnasientData['fields']);
    }

    if (function_exists('fastcgi_finish_request') || !wp_doing_ajax()) {
      if (!headers_sent()) {
        header('Connection: close');
        $contentType = wp_doing_ajax() || defined('REST_REQUEST') ? 'application/json' : 'text/html';
        header('Content-Type: ' . $contentType . '; charset=' . get_option('blog_charset'));
        status_header(200);
        if (!$isFormRequest) {
          $response = [
            'status'  => 200,
            'code'    => 4000,
            'message' => 'Data Added Successfully!!',
            'success' => true
          ];
        } else {
          $response = [
            'success' => true,
            'data'    => $responseData,
          ];
        }
      }
      ob_start();
      echo wp_doing_ajax() || defined('REST_REQUEST') ? wp_json_encode($response) : $workFlowReturnedData['message'];
      ob_end_flush();
      flush();
      session_write_close();

      if (isset($workFlowReturnedData['integrationRun']) && !$workFlowReturnedData['integrationRun']) {
        $entryModel = new FormEntryModel();
        $updatedStatus = $entryModel->update(
          [
            'status' => 2,
          ],
          [
            'form_id' => $triggerData['formID'],
            'id'      => $triggerData['entryID'],
          ]
        );

        if (!is_wp_error($updatedStatus)) {
          if ($workFlowReturnedData['dflt_template']) {
            $triggerData['fields'] = $workFlowReturnedData['fields'];
            do_action('bf_double_optin_confirmation', $workFlowReturnedData['integrationDetails'], $triggerData);
          } elseif (isset($triggerData['dblOptin'])) {
            foreach ($triggerData['dblOptin'] as $value) {
              MailNotifier::notify($value, $triggerData['formID'], $triggerData['fields'], $triggerData['entryID'], true, $triggerData['logID']);
            }
          }
        }
        if (function_exists('fastcgi_finish_request')) {
          fastcgi_finish_request();
        }

        if (defined('REST_REQUEST') || wp_doing_ajax()) {
          die;
        }
        return $workFlowReturnedData;
      }

      if (isset($triggerData['mail'])) {
        $formManager = new AdminFormManager($triggerData['formID']);
        $formContent = $formManager->getFormContent();
        $submitted_fields = $formContent->fields;
        $fieldValueForMail = FieldValueHandler::formatFieldValueForMail($submitted_fields, $workFlowReturnedData['fields']);
        foreach ($triggerData['mail'] as $value) {
          MailNotifier::notify($value, $triggerData['formID'], $fieldValueForMail, $triggerData['entryID']);
        }
      }
      do_action('bitforms_exec_integrations', $triggerData['integrations'], $workFlowReturnedData['fields'], $triggerData['formID'], $triggerData['entryID'], $triggerData['logID']);

      if (function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request();
      }

      if (defined('REST_REQUEST') || wp_doing_ajax()) {
        die;
      }
      return $workFlowReturnedData;
    }

    $entryID = $triggerData['entryID'];
    if (isset($workFlowReturnedData['cron']) && $workFlowReturnedData['cron'] && !isset($triggerData['mail']) && isset($workFlowReturnedData['integrationRun']) && $workFlowReturnedData['integrationRun']) {
      $eventScheuled = wp_schedule_single_event(time(), 'bitforms_exec_integrations', [$triggerData['integrations'], $workFlowReturnedData['fields'], $triggerData['formID'], $triggerData['entryID'], $triggerData['logID']]);
      $scheduleTime = wp_next_scheduled('bitforms_exec_integrations', [$triggerData['integrations'], $workFlowReturnedData['fields'], $triggerData['formID'], $triggerData['entryID'], $triggerData['logID']]);
      $responseData['cron'] = get_site_url(null, "/wp-cron.php?doing_wp_cron&{$scheduleTime}");
    } elseif (!empty($triggerData['integrations']) || !empty($triggerData['mail']) || isset($workFlowReturnedData['integrationRun']) && !$workFlowReturnedData['integrationRun']) {
      $triggerData['fields'] = $workFlowReturnedData['fields'];
      if (!$workFlowReturnedData['integrationRun'] && isset($workFlowReturnedData['integrationDetails'])) {
        $triggerData['dbl_opt_donf'] = $workFlowReturnedData['integrationDetails'];
        $triggerData['dbl_opt_dflt_template'] = $workFlowReturnedData['dflt_template'];
        $triggerData['integrationRun'] = $workFlowReturnedData['integrationRun'];
      }
      set_transient("bitform_trigger_transient_{$entryID}", $triggerData, HOUR_IN_SECONDS);
      $entryLog = new FormEntryLogModel();
      $queueuEntry = $entryLog->log_history_insert(
        [
          'log_id'         => $triggerData['logID'],
          'integration_id' => 0,
          'api_type'       => wp_json_encode(['type' => 'trigger', 'type_name' => 'Workflow', 'on' => $opType]),
          'response_type'  => 'queued',
          'response_obj'   => wp_json_encode(['status' => 'queued']),
          'created_at'     => current_time('mysql'),
        ]
      );
      $responseData['cronNotOk'] = [
        $triggerData['entryID'],
        $triggerData['logID'],
        $queueuEntry,
      ];
    }
    return $responseData;
  }

  public static function repeaterFieldValueFormatter($fieldValues)
  {
    $formattedArray = [];

    foreach ($fieldValues as $key => $value) {
      if (is_array($value)) {
        foreach ($value as $index => $v) {
          if (is_array($v)) {
            foreach ($v as $k1 => $v1) {
              $formattedArray[$k1][] = $v1;
            }
          } else {
            $formattedArray[$key][] = $v;
          }
        }
      } else {
        $formattedArray[$key] = $value;
      }
    }

    return $formattedArray;
  }

  /**
   *
   * @param mixed $fieldValues
   * @param mixed $returnRepeaterValue default is array
   * @return array  and repeater field value as array or string
   */
  public static function formattedRepeaterValue($fieldValues, $returnRepeaterValue = '')
  {
    // get the repeater field and store it in an array
    $repeaterField = [];
    foreach ($fieldValues as $key => $value) {
      if (!preg_match('/\./', $key)) {
        continue;
      }
      $repeaterField[] = $key;
    }

    // put the value of the child key to the parent repeater field\
    foreach ($repeaterField as $key) {
      list($parentKey, $childKey) = explode('.', $key);
      $repeatValues = isset($fieldValues[$parentKey]) ? $fieldValues[$parentKey] : $key;

      if (!is_array($repeatValues)) {
        $fieldValues[$key] = $fieldValues[$childKey];
        continue;
      }

      foreach ($repeatValues as $value) {
        if (isset($value[$childKey])) {
          if (!isset($fieldValues[$key]) || !is_array($fieldValues[$key])) {
            $fieldValues[$key] = [];
          }
          if (!empty($value[$childKey])) {
            $fieldValues[$key][] = $value[$childKey];
          }
        }
      }
    }

    // convert the array to string repeater field value
    if ('string' === $returnRepeaterValue) {
      foreach ($repeaterField as $key) {
        if (isset($fieldValues[$key]) && is_array($fieldValues[$key])) {
          $fieldValues[$key] = implode(', ', $fieldValues[$key]);
        }
      }
    }
    return $fieldValues;
  }
}
