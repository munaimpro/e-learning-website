<?php

namespace BitCode\BitForm\Core\Util;

use BitCode\BitForm\Core\Database\FormEntryLogModel;

final class ApiResponse
{
  public function apiResponse($logID, $integrationId, $apiType, $responseType, $responseObj)
  {
    if (!$apiType || !$responseObj) {
      return false;
    }
    $apiType = wp_json_encode($apiType);
    $responseObj = wp_json_encode($responseObj);

    $ipTool = new IpTool();
    $user_details = $ipTool->getUserDetail();
    $formEntryModel = new FormEntryLogModel();
    $result = $formEntryModel->log_history_insert(
      [
        'log_id'         => $logID,
        'integration_id' => $integrationId,
        'api_type'       => $apiType,
        'response_type'  => $responseType,
        'response_obj'   => $responseObj,
        'created_at'     => $user_details['time'],
      ]
    );
    return $result;
  }
}
