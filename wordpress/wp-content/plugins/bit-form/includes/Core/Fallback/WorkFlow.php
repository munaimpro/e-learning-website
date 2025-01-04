<?php

namespace BitCode\BitForm\Core\Fallback;

use BitCode\BitForm\Core\Database\WorkFlowModel;
use BitCode\BitForm\Core\Util\IpTool;

class WorkFlow
{
  public function conditionalLogic()
  {
    $workFlowUpdatedSatus = true;
    $workFlowModel = new WorkFlowModel();
    $workFlows = $workFlowModel->get(
      [
        'id',
        'workflow_behaviour',
        'workflow_condition',
        'workflow_action',
      ],
      [],
      null,
      null,
      'id'
    );
    if (is_wp_error($workFlows) || count($workFlows) <= 0) {
      return '';
    }
    $ipTool = new IpTool();
    $user_details = $ipTool->getUserDetail();

    foreach ($workFlows as $index => $workflow) {
      $decode = \json_decode($workflow->workflow_action);
      $logic = \json_decode($workflow->workflow_condition);
      $beheviour = $workflow->workflow_behaviour;
      $cond = 'cond' === $beheviour ? 'if' : 'always';

      $workFlows[$index]->workflow_condition = [
        'cond_type' => $cond,
        'actions'   => [
          'fields'            => $decode->action ? $decode->action : [],
          'success'           => $decode->successAction ? $decode->successAction : [],
          'failure'           => $decode->validateMsg ? $decode->validateMsg : '',
        ],
      ];
      if ('cond' === $beheviour) {
        $workFlows[$index]->workflow_condition['logics'] = $logic;
      }
      $updatedWorkFlow = wp_json_encode([$workFlows[$index]->workflow_condition]);
      $updated = $workFlowModel->update(
        [
          'workflow_condition' => $updatedWorkFlow,
          'updated_at'         => $user_details['time'],
        ],
        [
          'id' => $workflow->id,
        ]
      );
      if (is_wp_error($updated)) {
        $workFlowUpdatedSatus = false;
        break;
      }
    }
    if ($workFlowUpdatedSatus) {
      global $wpdb;
      $wpdb->query(
        "ALTER TABLE `{$wpdb->prefix}bitforms_workflows` DROP `workflow_action`"
      );
    }
  }
}
