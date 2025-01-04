<?php

namespace BitCode\BitForm\Core\Migration;

class WorkFlows
{
  public static function migrate($workFlowsData)
  {
    $migrateWorkFlows = [];
    foreach ($workFlowsData as $workflow) {
      $migrateWorkFlows[] = self::updateWorkflow($workflow);
    }
    return $migrateWorkFlows;
  }

  public static function updateWorkflow($workflow)
  {
    $workflow = (object) $workflow;
    $flow = [
      'id'               => $workflow->id,
      'title'            => $workflow->title,
      'action_type'      => $workflow->action_type,
      'action_behaviour' => $workflow->action_behaviour,
      'action_run'       => $workflow->action_run,
    ];
    $flowProps = [];

    if ('cond' === $workflow->action_behaviour) {
      $flowProps['cond_type'] = 'if';
      $flowProps['logics'] = $workflow->logics;
      if ('onvalidate' === $workflow->action_type) {
        // $flowProps['actions']['fields'] = $workflow->actions;
        $flowProps['actions']['failure'] = $workflow->validateMsg;
      }

      if ('oninput' === $workflow->action_type) {
        $flowProps['actions']['fields'] = $workflow->actions;
        // $flowProps['actions']['success'] = $workflow->successAction;
        $flowProps['actions']['success'] = [];
      }

      if ('onload' === $workflow->action_type) {
        $flowProps['actions']['fields'] = $workflow->actions;
        // $flowProps['actions']['success'] = $workflow->successAction;
      }
      if ('onsubmit' === $workflow->action_type) {
        $flowProps['actions']['fields'] = $workflow->actions;
        $flowProps['actions']['success'] = $workflow->successAction;
        //  integration, webhook,
      }
    } else {
      if ('onload' === $workflow->action_type) {
        $flowProps['cond_type'] = 'always';
        $flowProps['actions']['fields'] = $workflow->actions;
        // $flowProps['actions']['success'] = $workflow->successAction;
      }
      if ('onsubmit' === $workflow->action_type) {
        $flowProps['actions']['fields'] = $workflow->actions;
        $flowProps['actions']['success'] = $workflow->successAction;
      }
    }

    $flow['conditions'][] = (object) $flowProps;

    return (object) $flow;
  }
}
