<?php

/**
 * Provides Base Model Class
 */

namespace BitCode\BitForm\Core\Database;

/**
 * Undocumented class
 */

class FormEntryMetaModel extends Model
{
  protected static $table = 'bitforms_form_entrymeta';

  public function duplicateEntryMeta($data)
  {
    $values[] = $data['duplicateID'];
    $values[] = $data['entryID'];
    $sql = "INSERT INTO $this->table_name (bitforms_form_entry_id,meta_key,meta_value)"
        . ' SELECT %d as bitforms_form_entry_id,meta_key,meta_value'
        . " FROM `$this->table_name` WHERE bitforms_form_entry_id = %d";
    return $this->execute($sql, $values)->getResult();
  }

  public function update(array $data, array $condition)
  {
    $entryID = $condition['bitforms_form_entry_id'];
    if (empty($entryID)) {
      return false;
    }
    $formEntryMeta = $this->get(
      [
        'meta_key',
        'meta_value',
      ],
      [
        'bitforms_form_entry_id' => $entryID,
      ]
    );
    $oldEntries = [];
    foreach ($formEntryMeta as $oldKey => $oldValue) {
      $oldEntries[$oldValue->meta_key] = $oldValue->meta_value;
    }
    $updatedData = [];
    $oldEntriesKey = array_keys($oldEntries);
    foreach ($data as $upKey => $upValue) {
      $updatedData[$upKey] = is_string($upValue) ?
      $upValue :
      wp_json_encode($upValue);
      if (!\in_array($upKey, $oldEntriesKey)) {
        $this->insert(
          [
            'bitforms_form_entry_id' => $entryID,
            'meta_key'               => $upKey,
            'meta_value'             => $updatedData[$upKey],
          ]
        );
        unset($data[$upKey]);
      }
    }
    if (empty($data)) {
      return $updatedData;
    }
    $checkCondition = $this->checkCondition($condition);
    if (is_wp_error($checkCondition)) {
      return $checkCondition;
    }
    $case_part = ' ';
    $all_values = [];
    $condition['meta_key'] = array_keys($data);
    foreach ($data as $key => $value) {
      $value = is_string($value) ? $value : wp_json_encode($value);
      $case_part .= "
            WHEN '$key' THEN " . $this->getFieldFormat($value);
      $all_values[] = $value;
    }
    $formattedCondition = $this->getFormatedCondition($condition);
    if ($formattedCondition) {
      $condition_to_check = $formattedCondition['conditions'];
      $all_values = array_merge($all_values, $formattedCondition['values']);
    } else {
      $condition_to_check = null;
    }

    $sql = "UPDATE $this->table_name
         SET meta_value = ( CASE meta_key
                $case_part
            END
            )";
    $sql .= ' ' . $condition_to_check;
    $status = $this->execute($sql, $all_values)->getResult();
    if (is_wp_error($status) && 'result_empty' !== $status->get_error_code()) {
      return $status;
    }
    return $updatedData;
  }

  public function validQueryCondition($conditions)
  {
    foreach ($conditions as $index => $condition) {
      if (is_object($condition)) {
        if (empty($condition->field) || empty($condition->logic) || empty($condition->val) && !in_array($condition->val, ['0', 0, 0.0], true)) {
          unset($conditions[$index], $conditions[$index + 1]);
        }
      }
    }
    return $conditions;
  }

  public function sqlQryGenerateByFldCondition($conditions)
  {
    $dbHelper = new Helper();
    $sql = '';

    $dateQuery = $dbHelper->dateQueryList();

    $validCondtions = $this->validQueryCondition($conditions);

    foreach ($validCondtions as $condition) {
      if (is_object($condition)) {
        if (is_array($condition->val)) {
          $value = $dbHelper->arrValueModifyByLogic($condition->logic, $condition->val);
        } else {
          $value = $dbHelper->strValueModifyByLogic($condition->logic, $condition->val);
        }

        $operator = $dbHelper->convertToSqlOperator($condition->logic);
        if (!is_array($condition->val) && isset($dateQuery[$condition->val])) {
          $sql .= $dbHelper->fieldQueryByDate($condition->field, $operator, $value, $condition->logic);
        } else {
          if (!is_int($value)) {
            $value = "'" . $value . "'";
          }

          $sql .= "`$condition->field` $operator $value";
        }
      } else {
        $sql .= ' ' . $condition;
      }
    }

    return trim($sql);
  }

  public function queryRecount($selectedMeta, $groupedCondition, $orderCondition, $all_values)
  {
    $entry_table = $this->app_db->prefix . 'bitforms_form_entries';
    $sql = 'SELECT count(*) as count FROM (';
    $sql .= "SELECT $selectedMeta FROM `$this->table_name` em";
    $sql .= " INNER JOIN $entry_table e on e.id = em.bitforms_form_entry_id ";
    $sql .= $groupedCondition . $orderCondition;
    $sql .= ') as retrievedData';
    $countResult = $this->execute($sql, $all_values)->getResult();
    return $countResult[0]->count;
  }

  public function selectedEntryMeta($formFields, $fieldCount)
  {
    $all_values = [];
    $formFieldsNames = [];
    $metaChecker = 0;
    $selectedMeta = '`bitforms_form_entry_id` as entry_id,';
    $selectedMeta .= "e.user_id as '__user_id',";
    $selectedMeta .= "e.user_ip as '__user_ip',";
    $selectedMeta .= "e.status as '__entry_status',";
    $selectedMeta .= "e.user_location as '__user_location',";
    $selectedMeta .= "e.user_device as '__user_device',";
    $selectedMeta .= "e.referer as '__referer',";
    $selectedMeta .= "e.created_at as '__created_at',";
    $selectedMeta .= "e.updated_at as '__updated_at'";

    if ($fieldCount > 0) {
      $selectedMeta .= ',';
    }

    foreach ($formFields as $fieldDetails) {
      $fieldFormat = $this->getFieldFormat($fieldDetails['key']);
      $selectedMeta .= "GROUP_CONCAT(
                CASE
                  `meta_key`
                  WHEN '$fieldFormat' THEN `meta_value`
                END
              ) AS '$fieldFormat'";
      $metaChecker += 1;
      $all_values[] = $fieldDetails['key'];
      $all_values[] = $fieldDetails['key'];
      $formFieldsNames[] = $fieldDetails['key'];
      if ($metaChecker < $fieldCount) {
        $selectedMeta .= ',';
      }
      //#unused  code commented by me##
      // if ( !empty( $filter['global'] ) ) {
      //     $globalFilterString .= " `" . $fieldDetails['key'] . "` LIKE '%%" . $this->getFieldFormat( $filter['global'] ) . "%%' ";
      //     if ( $metaChecker < $fieldCount ) {
      //         $globalFilterString .= " OR ";
      //     }
      //     $globalFilterValues[] = $filter['global'];
      // }
    }

    return [
      'selected_meta'     => $selectedMeta,
      'form_fields_names' => $formFieldsNames,
      'all_values'        => $all_values,
    ];
  }

  public function groupedCondition($condition, $all_values, $fieldConditions)
  {
    $isFldCondition = false;
    $formattedCondition = $this->getFormatedCondition($condition);
    if ($formattedCondition) {
      $groupedCondition = $formattedCondition['conditions'] . 'GROUP BY
            `bitforms_form_entry_id` ';
      $all_values = array_merge($all_values, $formattedCondition['values']);
    } else {
      $groupedCondition = null;
    }
    //#unused  code commented by me##
    //$isRecount = false;

    // if ( !empty( $filter['field'] ) ) {
    //     $isRecount = true;
    //     $filterFieldCount = count( $filter['field'] );
    //     $filterFieldChecker = 0;
    //     if ( $filterFieldCount > 0 ) {
    //         $groupedCondition .= " HAVING ";
    //     }
    //     foreach ( $filter['field'] as $filterFieldKey => $filterFieldDetails ) {
    //         $groupedCondition .= " `$filterFieldDetails->id` ='%%" . $this->getFieldFormat( $filterFieldDetails->value ) . "%%'";
    //         $all_values[] = $filterFieldDetails->value;
    //         if ( $filterFieldChecker < $filterFieldCount ) {
    //             $groupedCondition .= " AND ";
    //         }
    //     }
    // }

    // if ( !empty( $filter['global'] ) && !empty( $globalFilterString ) && !empty( $globalFilterValues ) ) {
    //     $isRecount = true;
    //     if ( !empty( $filter['field'] ) ) {
    //         $groupedCondition .= " AND (" . $globalFilterString . ") ";
    //     } else {
    //         $groupedCondition .= " HAVING $globalFilterString ";
    //     }
    //     $offset = 0;
    //     $all_values = array_merge( $all_values, $globalFilterValues );
    // }

    // if ( !empty( $dateBetweenFilter ) && !empty( $dateBetweenFilter->start_date ) && !empty( $dateBetweenFilter->end_date ) ) {
    //     if ( strpos( $groupedCondition, 'HAVING' ) !== false ) {
    //         $groupedCondition .= " AND  `__created_at` BETWEEN '" . $dateBetweenFilter->start_date . "' AND '" . $dateBetweenFilter->end_date . "' ";
    //     } else {
    //         $groupedCondition .= " HAVING  `__created_at` BETWEEN '" . $dateBetweenFilter->start_date . "' AND '" . $dateBetweenFilter->end_date . "' ";
    //     }
    // }

    $sqlQryByFldCondtion = $this->sqlQryGenerateByFldCondition($fieldConditions);

    if (!empty($sqlQryByFldCondtion)) {
      $isFldCondition = true;
      if (false !== strpos($groupedCondition, 'HAVING')) {
        $groupedCondition .= ' AND (' . $sqlQryByFldCondtion . ') ';
      } else {
        $groupedCondition .= ' HAVING (' . $sqlQryByFldCondtion . ') ';
      }
    }
    return [
      'groupedCondition' => $groupedCondition,
      'all_values'       => $all_values,
      'isFldCondition'   => $isFldCondition,
    ];
  }

  public function orderCondition($formFieldsNames, $sortBy)
  {
    $orderCondition = null;
    if (!empty($sortBy)) {
      $sortableFieldCount = count($sortBy);
      $sortableFieldChecker = 0;
      if ($sortableFieldCount > 0) {
        $orderCondition .= ' ORDER BY ';
      }
      $orderList = '';
      foreach ($sortBy as $sortableFieldKey => $sortableFieldDetails) {
        // $orderCondition .=" ".$this->getFieldFormat($sortableFieldDetails->id);
        $sortableFieldChecker += 1;
        if (in_array($sortableFieldDetails->id, $formFieldsNames)) {
          $orderList .= " `$sortableFieldDetails->id` ";
          $orderFollow = $sortableFieldDetails->desc ? ' DESC ' : ' ASC ';
          $orderList .= ' ' . $orderFollow;
          if ($sortableFieldChecker < $sortableFieldCount) {
            $orderList .= ', ';
          }
        }
      }
      if (empty($orderList)) {
        $orderCondition = null;
      } else {
        $orderCondition .= $orderList;
      }
    }
    $orderCondition .= empty($orderCondition) ? ' ORDER BY `bitforms_form_entry_id` DESC ' : ',`bitforms_form_entry_id` DESC ';
    return $orderCondition;
  }

  public function isEntryMetaExist($conditions)
  {
    $existMetaData = $this->get(
      [
        'meta_key',
        'meta_value',
      ],
      $conditions
    );
    if (!is_wp_error($existMetaData) && count($existMetaData) > 0) {
      return true;
    }
    return false;
  }

  public function getEntryMeta($formFields, $entries, $limit = null, $offset = null, $filter = null, $sortBy = null, $fieldConditions = null, $dateBetweenFilter = null)
  {
    $entry_table = $this->app_db->prefix . 'bitforms_form_entries';
    $fieldCount = count($formFields);
    $getSelectedMetaFldValue = $this->selectedEntryMeta($formFields, $fieldCount);

    $selectedMeta = $getSelectedMetaFldValue['selected_meta'];
    $formFieldsNames = $getSelectedMetaFldValue['form_fields_names'];
    $all_values = $getSelectedMetaFldValue['all_values'];
    $entryIDs = [];
    $entryCount = count($entries);
    // $paginateEntry = empty($sortBy) && empty($filter['field']) && empty($filter['global']);
    // $entries = $paginateEntry ? array_slice($entries, $offset, $limit) : $entries;
    $paginateEntry = false;
    foreach ($entries as $entryDetail) {
      $entryIDs[] = $entryDetail->id;
    }
    if (empty($entryIDs)) {
      return [
        'count'   => 0,
        'entries' => [],
      ];
    }
    $condition['bitforms_form_entry_id'] = $entryIDs;
    $group = $this->groupedCondition($condition, $all_values, $fieldConditions);
    $groupedCondition = $group['groupedCondition'];
    $all_values = $group['all_values'];
    $isFldCondition = $group['isFldCondition'];
    $orderCondition = $this->orderCondition($formFieldsNames, $sortBy);

    $paginate = null;
    if (!\is_null($limit)) {
      $limit = \intval($limit);
      $paginate .= " LIMIT $limit ";
    }
    if (!\is_null($offset)) {
      $offset = \intval($offset);
      $paginate .= " OFFSET  $offset ";
    }
    $sql = "SELECT $selectedMeta FROM `$this->table_name` em";
    $sql .= " INNER JOIN $entry_table e on e.id = em.bitforms_form_entry_id ";
    if ($dateBetweenFilter) {
      $startDate = $dateBetweenFilter->start_date;
      $endDate = $dateBetweenFilter->end_date;
      if ($startDate && $endDate) {
        $sql .= " AND DATE(e.created_at) BETWEEN '$startDate' AND '$endDate' ";
      } elseif ($startDate) {
        $sql .= " AND DATE(e.created_at) >= '$startDate' ";
      } elseif ($endDate) {
        $sql .= " AND DATE(e.created_at) <= '$endDate' ";
      }
    }
    $sql .= $groupedCondition . $orderCondition . $paginate;
    $result = $this->execute($sql, $all_values)->getResult();
    if (is_wp_error($result)) {
      return [
        'count'   => $paginateEntry ? $entryCount : 0,
        'entries' => [],
        'error'   => $result->get_error_message()
      ];
    }
    if ($isFldCondition) {
      $condition['bitforms_form_entry_id'] = $entryIDs;
      $group = $this->groupedCondition($condition, $all_values, $fieldConditions);
      $all_values = $group['all_values'];
      $entryCount = $this->queryRecount($selectedMeta, $group['groupedCondition'], $orderCondition, $all_values);
    }
    $resultedEntries = [
      'count'   => $entryCount,
      'entries' => $result,
    ];
    return $resultedEntries;
  }

  public function getSingleEntryMeta($formFields, $entryId)
  {
    $entry_table = $this->app_db->prefix . 'bitforms_form_entries';
    $fieldCount = count($formFields);
    $getSelectedMetaFldValue = $this->selectedEntryMeta($formFields, $fieldCount);
    $selectedMeta = $getSelectedMetaFldValue['selected_meta'];
    $formFieldsNames = $getSelectedMetaFldValue['form_fields_names'];
    $all_values = $getSelectedMetaFldValue['all_values'];
    $condition['bitforms_form_entry_id'] = [$entryId];
    $group = $this->groupedCondition($condition, $all_values, []);
    $groupedCondition = $group['groupedCondition'];
    $all_values = $group['all_values'];
    $orderCondition = $this->orderCondition($formFieldsNames, null);
    $sql = "SELECT $selectedMeta FROM `$this->table_name` em";
    $sql .= " INNER JOIN $entry_table e on e.id = em.bitforms_form_entry_id ";
    $sql .= $groupedCondition . $orderCondition;
    $result = $this->execute($sql, $all_values)->getResult();

    if (is_wp_error($result)) {
      return [];
    }
    return $result;
  }

  private function csvInjectionPrevent($value)
  {
    $formula = ['=', '-', '+', '@', "\t", "\r"];
    $valueFilter = preg_replace('/[\]["]/i', '', $value);
    if (\in_array(substr($value, 0, 1), $formula, true)) {
      $valueFilter = "'" . trim($valueFilter);
    }

    return $valueFilter;
  }

  public function getExportEntry($formFields, $entries, $formId, $fieldLabels, $limit = null, $sortBy = null, $sortByField = null)
  {
    $entry_table = $this->app_db->prefix . 'bitforms_form_entries';
    $selectedEntryMeta = '`bitforms_form_entry_id` as entry_id,';
    $selectedEntryMeta .= "e.user_id as '__user_id',";
    $selectedEntryMeta .= "e.status as '__entry_status',";
    $selectedEntryMeta .= "e.user_ip as '__user_ip',";
    $selectedEntryMeta .= "e.user_location as '__user_location',";
    $selectedEntryMeta .= "e.user_device as '__user_device',";
    $selectedEntryMeta .= "e.referer as '__referer',";
    $selectedEntryMeta .= "e.created_at as '__created_at',";
    $selectedEntryMeta .= "e.updated_at as '__updated_at',";
    $metaChecker = 0;

    $entryInfo = ['__user_id', '__user_ip', /* '__user_location', */'__user_device',
      '__referer', '__created_at', '__updated_at'];
    $all_values = [];
    if ([] === $formFields) {
      $data = [
        'count'   => 0,
        'entries' => [],
      ];
      wp_send_json_success($data, 200);
    }
    $fieldCount = count($formFields) - count(array_intersect($formFields, $entryInfo));
    $formFieldsNames = [];
    foreach ($formFields as $fldKey) {
      $formFieldsNames[] = $fldKey;
      if (in_array($fldKey, $entryInfo)) {
        continue;
      }
      $fieldFormat = $this->getFieldFormat($fldKey);
      $selectedEntryMeta .= "GROUP_CONCAT(
                CASE
                  `meta_key`
                  WHEN '$fieldFormat' THEN `meta_value`
                END
              ) AS '$fieldFormat'";
      $metaChecker += 1;
      $all_values[] = $fldKey;
      $all_values[] = $fldKey;
      if ($metaChecker < $fieldCount) {
        $selectedEntryMeta .= ',';
      }
    }
    $entryIDs = [];
    foreach ($entries as $entryDetail) {
      $entryIDs[] = $entryDetail->id;
    }
    if (empty($entryIDs)) {
      return [
        'count'   => 0,
        'entries' => [],
      ];
    }
    $condition['bitforms_form_entry_id'] = $entryIDs;
    $formattedCondition = $this->getFormatedCondition($condition);
    $groupedCondition = null;
    if ($formattedCondition) {
      $groupedCondition = $formattedCondition['conditions'] . ' GROUP BY
            `bitforms_form_entry_id` ';
      $all_values = array_merge($all_values, $formattedCondition['values']);
    }
    $order = 'DESC' === $sortBy ? 'DESC ' : 'ASC ';
    $orderField = \is_null($sortByField) ? 'bitforms_form_entry_id' : "`$sortByField`";

    $orderCondition = "ORDER BY $orderField $order ";
    if (!\is_null($limit)) {
      $limitInt = \intval($limit);
      $limit = " LIMIT $limitInt ";
    }
    $sql = "SELECT $selectedEntryMeta FROM `$this->table_name` em";
    $sql .= " INNER JOIN $entry_table e on e.id = em.bitforms_form_entry_id ";
    $sql .= $groupedCondition . $orderCondition . $limit;

    $result = $this->execute($sql, $all_values)->getResult();
    $allData = [];
    $entry_id = 'entry_id';
    $users = get_users(['fields' => ['ID', 'display_name']]);
    $userNames = [];
    foreach ($users as $key => $value) {
      $userNames[$value->ID] = $value->display_name;
    }
    foreach ($result as $key => $value) {
      foreach ($formFieldsNames as $formFieldName) {
        $allData[$key]['entry_id'] = preg_replace('/[\]["]/i', '', $value->$entry_id);
        if ('__user_id' === $formFieldName && intval($value->$formFieldName) > 0) {
          $allData[$key][$formFieldName] = $userNames[$value->$formFieldName];
        } elseif ('__user_ip' === $formFieldName) {
          $allData[$key][$formFieldName] = long2ip((int)$value->$formFieldName);
        } else {
          $allData[$key][$formFieldName] = preg_replace('/[\]["]/i', '', $value->$formFieldName);
        }
      }
    }

    if (is_wp_error($result)) {
      wp_send_json_error('Internal server error', 500);
    } else {
      $downloadableFieldType = ['file-up', 'signature', 'advanced-file-up'];
      foreach ($fieldLabels as $field) {
        foreach ($allData as $index => $entry) {
          if (array_key_exists($field['key'], $entry) && in_array($field['type'], $downloadableFieldType, true)) {
            $key = $field['key'];
            if (empty($entry[$key])) {
              continue;
            }
            $_upload_dir = BITFORMS_UPLOAD_DIR . DIRECTORY_SEPARATOR . $formId . DIRECTORY_SEPARATOR . $entry['entry_id'];
            $imageArray = explode(',', $entry[$key]);
            if (is_array($imageArray)) {
              $fileData = [];
              foreach ($imageArray as $file) {
                $path = "bitforms/bitforms-file-$formId/?formID=$formId&entryID=" . $entry['entry_id'] . "&fileID=$file";
                if (file_exists($_upload_dir . DIRECTORY_SEPARATOR . $file)) {
                  $fileData[] = site_url($path, null);
                }
              }
              $allData[$index][$key] = implode(',', $fileData);
            } else {
              $uploadedFile = explode('_', $entry[$key]);
              $path = "bitforms/bitforms-file-$formId/?formID=$formId&entryID=" . $entry['entry_id'] . '&fileID=' . $uploadedFile[0];
              if (file_exists($_upload_dir . DIRECTORY_SEPARATOR . $uploadedFile[0])) {
                $allData[$index][$key] = site_url($path, null);
              }
            }
          }
        }
      }
      wp_send_json_success($allData, 200);
    }
  }
}
