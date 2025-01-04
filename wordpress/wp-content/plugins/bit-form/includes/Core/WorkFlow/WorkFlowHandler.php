<?php

namespace BitCode\BitForm\Core\WorkFlow;

use BitCode\BitForm\Core\Database\WorkFlowModel;

final class WorkFlowHandler
{
  private static $_formID;
  private static $_workFlowModel;
  private $_user_details;

  public function __construct($formID, array $user_details = null)
  {
    static::$_formID = $formID;
    static::$_workFlowModel = new WorkFlowModel();
    $this->_user_details = $user_details;
  }

  public function getAllworkFlowV1()
  {
    $workFlows = static::$_workFlowModel->get(
      [
        'id',
        'workflow_name',
        'workflow_type',
        'workflow_run',
        'workflow_behaviour',
        'workflow_condition',
        'workflow_action'
      ],
      [
        'form_id' => static::$_formID,
      ],
      null,
      null,
      'id',
      'desc'
    );
    $workFlowsFormated = [];
    if (empty($workFlows) || is_wp_error($workFlows)) {
      return [];
    }
    foreach ($workFlows as $key => $value) {
      $allAction = json_decode($value->workflow_action);
      $workFlow['id'] = $value->id;
      $workFlow['title'] = $value->workflow_name;
      $workFlow['action_type'] = $value->workflow_type;
      $workFlow['action_run'] = $value->workflow_run;
      $workFlow['action_behaviour'] = $value->workflow_behaviour;
      $workFlow['logics'] = json_decode($value->workflow_condition);
      $workFlow['actions'] = $allAction->action;
      $workFlow['successAction'] = $allAction->successAction;
      if (isset($allAction->validateMsg)) {
        $workFlow['validateMsg'] = $allAction->validateMsg;
      }
      if (isset($allAction->avoid_delete)) {
        $workFlow['avoid_delete'] = $allAction->avoid_delete;
      }
      $workFlowsFormated[$key] = $workFlow;
    }

    return $workFlowsFormated;
  }

  public function getAllworkFlow()
  {
    $workFlows = static::$_workFlowModel->get(
      [
        'id',
        'workflow_name',
        'workflow_type',
        'workflow_run',
        'workflow_behaviour',
        'workflow_condition',
        'workflow_order',
      ],
      [
        'form_id' => static::$_formID,
      ],
      null,
      null,
      'workflow_order',
      'ASC'
    );
    $workFlowsFormated = [];
    if (empty($workFlows) || is_wp_error($workFlows)) {
      return [];
    }
    foreach ($workFlows as $workFlowKey => $value) {
      $workFlow['id'] = $value->id;
      $workFlow['title'] = $value->workflow_name;
      $workFlow['action_type'] = $value->workflow_type;
      $workFlow['action_run'] = $value->workflow_run;
      $workFlow['action_behaviour'] = $value->workflow_behaviour;
      $workFlow['conditions'] = json_decode($value->workflow_condition);

      foreach ($workFlow['conditions'] as $conIndex => $condition) {
        if (property_exists($condition, 'logics')) {
          $workFlow['conditions'][$conIndex]->logics = $condition->logics;
        }
        if (property_exists($condition, 'actions')) {
          $workFlow['conditions'][$conIndex]->actions = $condition->actions;
        }
      }

      $workFlowsFormated[$workFlowKey] = $workFlow;
    }

    return $workFlowsFormated;
  }

  public function updateworkFlow($workFlowID, $workFlowDetails, $actionIntegrationDetails, $workFlowOrder, $errorCode = [])
  {
    $conditions = $workFlowDetails->conditions;
    foreach ($conditions as $conIndex => $condition) {
      $actions = $condition->actions;
      if (!empty($actions->success)) {
        foreach ($actions->success as $actionKey => $actionValue) {
          $detailIds = $actionValue->details->id;
          if (!empty($detailIds)) {
            if (is_array($detailIds)) {
              foreach ($detailIds as $key => $id) {
                $actionIntegrationID = \json_decode($id);
                if (isset($actionIntegrationID->index)) {
                  $value = wp_json_encode(['id' => (string) $actionIntegrationDetails[$actionValue->type][$actionIntegrationID->index]]);
                  $errorCode['workflow'] = 1;
                } else {
                  $value = $id;
                }
                $workFlowDetails->conditions[$conIndex]->actions->success[$actionKey]->details->id[$key] = $value;
              }
            } else {
              $actionIntegrationID = \json_decode($detailIds);
              if (isset($actionIntegrationDetails[$actionValue->type])) {
                if (isset($actionIntegrationID->index)) {
                  $value = wp_json_encode(['id' => (string) $actionIntegrationDetails[$actionValue->type][$actionIntegrationID->index]]);
                  $errorCode['workflow'] = 1;
                } else {
                  $value = $actionValue->details->id;
                }

                $workFlowDetails->conditions[$conIndex]->actions->success[$actionKey]->details->id = $value;
              }
            }
          }
          // pdfId update
          if (isset($actionIntegrationDetails['pdfTem']) && !empty($actionValue->details->pdfId)) {
            $pdfDetailId = $actionValue->details->pdfId;
            $actionIntegrationID = \json_decode($pdfDetailId);
            if (isset($actionIntegrationID->index)) {
              $value = wp_json_encode(['id' => (string) $actionIntegrationDetails['pdfTem'][$actionIntegrationID->index]]);
              $errorCode['workflow'] = 1;
            } else {
              $value = $actionValue->details->pdfId;
            }

            $workFlowDetails->conditions[$conIndex]->actions->success[$actionKey]->details->pdfId = $value;
          }
        }
      }
      if (!empty($actions->failure)) {
        $validateMsgID = \json_decode($actions->failure);
        $failure = isset($validateMsgID->index) ?
            wp_json_encode(
              [
                'id' => $actionIntegrationDetails['successMsg'][$validateMsgID->index],
              ]
            )
            : $actions->failure;

        $conditions[$conIndex]->actions->failure = $failure;
      }

      if (!empty($actions->avoid_delete)) {
        $conditions[$conIndex]->actions['avoid_delete'] = $workFlowDetails->avoid_delete;
      }
    }

    $status = static::$_workFlowModel->update(
      [
        'workflow_name'      => $workFlowDetails->title,
        'workflow_type'      => 'delete' === $workFlowDetails->action_run ? 'delete' : $workFlowDetails->action_type,
        'workflow_run'       => $workFlowDetails->action_run,
        'workflow_behaviour' => $workFlowDetails->action_behaviour,
        'workflow_condition' => wp_json_encode($conditions),
        // "workflow_action" => wp_json_encode($workFlowActions),
        'workflow_order' => $workFlowOrder,
        'user_id'        => $this->_user_details['id'],
        'user_ip'        => $this->_user_details['ip'],
        'user_location'  => '',
        'user_device'    => $this->_user_details['device'],
        'updated_at'     => $this->_user_details['time'],
      ],
      [
        'id'      => $workFlowID,
        'form_id' => static::$_formID,
      ]
    );

    if (is_wp_error($status) && 'result_empty' !== $status->get_error_code()) {
      $errorCode['workflow'] = 2;
    }
    return $errorCode;
  }

  public function saveworkFlow($workFlowDetails, $actionIntegrationDetails, $workFlowOrder)
  {
    $conditions = $workFlowDetails->conditions;
    foreach ($conditions as $conIndex => $condition) {
      $actions = $condition->actions;
      if (property_exists($actions, 'success')) {
        foreach ($actions->success as $actionKey => $actionValue) {
          $detailIds = $actionValue->details->id;
          if (!empty($detailIds)) {
            if (is_array($detailIds)) {
              foreach ($detailIds as $key => $id) {
                $actionIntegrationID = \json_decode($id);

                $value = $this->maybeGetSuccessActionID($actionIntegrationID, $actionIntegrationDetails, $actionValue->type);

                $workFlowDetails->conditions[$conIndex]->actions->success[$actionKey]->details->id[$key] = $value ? wp_json_encode(['id' => "$value"]) : $value;
              }
            } else {
              $actionIntegrationID = \json_decode($detailIds);
              $value = $this->maybeGetSuccessActionID($actionIntegrationID, $actionIntegrationDetails, $actionValue->type);
              $workFlowDetails->conditions[$conIndex]->actions->success[$actionKey]->details->id = $value ? wp_json_encode(['id' => "$value"]) : $actionValue->details->id;
            }
          }

          // pdfId update
          if (isset($actionIntegrationDetails['pdfTem']) && !empty($actionValue->details->pdfId)) {
            $pdfDetailId = $actionValue->details->pdfId;
            $actionIntegrationID = \json_decode($pdfDetailId);
            $value = $this->maybeGetSuccessActionID($actionIntegrationID, $actionIntegrationDetails, 'pdfTem');

            $workFlowDetails->conditions[$conIndex]->actions->success[$actionKey]->details->pdfId = $value;
          }
        }
      }

      if (!empty($actions->failure)) {
        $validateMsgID = \json_decode($actions->failure);
        $failure = isset($validateMsgID->index) ?
            wp_json_encode(
              [
                'id' => $actionIntegrationDetails['successMsg'][$validateMsgID->index],
              ]
            )
            : $actions->failure;

        $conditions[$conIndex]->actions->failure = $failure;
      }

      if (!empty($actions->avoid_delete)) {
        $conditions[$conIndex]->actions->avoid_delete = $workFlowDetails->avoid_delete;
      }
    }

    return static::$_workFlowModel->insert(
      [
        'workflow_name'      => $workFlowDetails->title,
        'workflow_type'      => 'delete' === $workFlowDetails->action_run ? 'delete' : $workFlowDetails->action_type,
        'workflow_run'       => $workFlowDetails->action_run,
        'workflow_behaviour' => $workFlowDetails->action_behaviour,
        'workflow_condition' => wp_json_encode($conditions),
        'workflow_order'     => $workFlowOrder,
        'form_id'            => static::$_formID,
        'user_id'            => $this->_user_details['id'],
        'user_ip'            => $this->_user_details['ip'],
        'user_location'      => '',
        'user_device'        => $this->_user_details['device'],
        'created_at'         => $this->_user_details['time'],
        'updated_at'         => $this->_user_details['time'],
      ]
    );
  }

  public function oldupdateworkFlow($workFlowID, $workFlowDetails, $actionIntegrationDetails)
  {
    // $workFlowActions['action'] = $workFlowDetails->actions;
    // foreach ($workFlowDetails->successAction as $successActionkey => $successActionValue) {
    //     if (!empty($successActionValue->details->id)) {
    //         if (is_array($successActionValue->details->id)) {
    //             foreach ($successActionValue->details->id as $key => $value) {
    //                 $actionIntegrationID = \json_decode($value);
    //                 $workFlowDetails->successAction[$successActionkey]->details->id[$key]
    //                     = isset($actionIntegrationID->index) ?
    //                 wp_json_encode(
    //                     [
    //                         'id'=>
    //                         $actionIntegrationDetails[$successActionValue->type][$actionIntegrationID->index]
    //                     ]
    //                 )
    //                 : $value;
    //             }
    //         } else {
    //             $actionIntegrationID = \json_decode($successActionValue->details->id);
    //             $workFlowDetails->successAction[$successActionkey]->details->id
    //                 = isset($actionIntegrationID->index) ?
    //                 wp_json_encode(
    //                     [
    //                         'id'=>
    //                         $actionIntegrationDetails[$successActionValue->type][$actionIntegrationID->index]
    //                     ]
    //                 )
    //                 : $successActionValue->details->id;
    //         }
    //     }
    // }
    // $workFlowActions['success'] = $workFlowDetails->successAction;
    // if (!empty($workFlowDetails->validateMsg)) {
    //     $validateMsgID = \json_decode($workFlowDetails->validateMsg);
    //     $workFlowActions['validateMsg'] = isset($validateMsgID->index) ?
    //     wp_json_encode(
    //         [
    //             'id'=>
    //             $actionIntegrationDetails['successMsg'][$validateMsgID->index]
    //             ]
    //     )
    //             : $workFlowDetails->validateMsg;
    // }
    // if (!empty($workFlowDetails->avoid_delete)) {
    //     $workFlowActions['avoid_delete'] = $workFlowDetails->avoid_delete;
    // }
    return static::$_workFlowModel->update(
      [
        'workflow_name'      => $workFlowDetails->title,
        'workflow_type'      => 'delete' === $workFlowDetails->action_run ? 'delete' : $workFlowDetails->action_type,
        'workflow_run'       => $workFlowDetails->action_run,
        'workflow_behaviour' => $workFlowDetails->action_behaviour,
        'workflow_condition' => wp_json_encode($workFlowDetails->conditions),
        // "workflow_action" => null,
        'user_id'            => $this->_user_details['id'],
        'user_ip'            => $this->_user_details['ip'],
        'user_location'      => '',
        'user_device'        => $this->_user_details['device'],
        'updated_at'         => $this->_user_details['time'],
      ],
      [
        'id'      => $workFlowID,
        'form_id' => static::$_formID,
      ]
    );
  }

  public function duplicateWorkFlow($currentFormID)
  {
    $workFlowCols = [
      'workflow_name', 'workflow_type', 'workflow_run', 'workflow_behaviour',
      'workflow_condition', 'workflow_action', 'form_id', 'user_id',
      'user_ip', 'user_location', 'user_device', 'created_at', 'updated_at',
    ];
    $dupData = [
      'workflow_name',
      'workflow_type',
      'workflow_run',
      'workflow_behaviour',
      'workflow_condition',
      'workflow_action',
      static::$_formID,
      $this->_user_details['id'],
      $this->_user_details['ip'],
      '',
      $this->_user_details['device'],
      $this->_user_details['time'],
      $this->_user_details['time'],
    ];
    return static::$_workFlowModel->duplicate($workFlowCols, $dupData, ['form_id' => $currentFormID]);
  }

  public function deleteworkFlow($workFlowID)
  {
    return static::$_workFlowModel->delete(
      [
        'id'      => $workFlowID,
        'form_id' => static::$_formID,
      ]
    );
  }

  public function maybeGetSuccessActionID($actionIntegrationID, $actionIntegrationDetails, $actionType)
  {
    if (isset($actionIntegrationDetails[$actionType])) {
      if (isset($actionIntegrationID->index)) {
        return $actionIntegrationDetails[$actionType][$actionIntegrationID->index];
      } elseif (isset($actionIntegrationID->id, $actionIntegrationDetails[$actionType][wp_json_encode($actionIntegrationID)])) {
        return $actionIntegrationDetails[$actionType][wp_json_encode($actionIntegrationID)];
      }
    }
    return false;
  }
}
