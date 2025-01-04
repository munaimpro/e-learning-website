<?php

namespace BitCode\BitForm\Core\Integration\Hubspot;

class Common
{
  public static function formFldMapping($dataFinal, $fieldMap, $data, $type = '')
  {
    foreach ($fieldMap as $value) {
      $triggerValue = $value->formField;
      $actionValue = $value->hubspotField;
      if ('custom' === $triggerValue) {
        $dataFinal[$actionValue] = $value->customValue;
      } elseif (!is_null($data[$triggerValue])) {
        if ('deal' === $type) {
          if (strtotime($data[$triggerValue])) {
            $formated = strtotime($data[$triggerValue]);
            $dataFinal[$actionValue] = $formated;
          } else {
            $dataFinal[$actionValue] = $data[$triggerValue];
          }
        } else {
          $dataFinal[$actionValue] = $data[$triggerValue];
        }
      }
    }
    return $dataFinal;
  }

  public static function customFldMapping($type, $action, $integrationDetails, $data)
  {
    $customFields = self::customFields();
    foreach ($customFields[$type] as $key => $name) {
      if (property_exists($action, $name)) {
        $data[$key] = $integrationDetails->{$name};
      }
    }
    return $data;
  }

  public static function customFields()
  {
    return [
      'lead' => [
        'hs_lead_status'   => 'lead_status',
        'lifecyclestage'   => 'lifecycle_stage',
        'hubspot_owner_id' => 'contact_owner',
      ],
      'deal' => [
        'hubspot_owner_id' => 'contact_owner',
        'deal_type'        => 'dealtype',
        'hs_priority'      => 'priority',
      ],
      'company' => [
        'associatedCompanyIds' => 'company',
        'associatedVids'       => 'contact',
      ],
    ];
  }
}
