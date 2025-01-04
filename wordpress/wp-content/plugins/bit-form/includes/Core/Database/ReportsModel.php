<?php
/**
 * Provides Report Model Class
 */

namespace BitCode\BitForm\Core\Database;

use WP_Error;

/**
 *
 */
class ReportsModel extends Model
{
  protected static $table = 'bitforms_reports';

  public function validateReportFields($reportData, $fieldNames)
  {
    $reportDetails = [];
    switch ($reportData->type) {
      case 'table':
        $tableAction = 'table_ac';
        $reportDetails = !is_string($reportData->details) ? $reportData->details : json_decode($reportData->details);
        if (!empty($reportDetails->order)) {
          $initialOrder = $fieldNames;
          $columnOrder = $reportDetails->order;
          foreach ($reportDetails->order as $key => $value) {
            if (isset($initialOrder[$value])) {
              unset($initialOrder[$value]);
            } elseif (!isset($initialOrder[$value])) {
              unset($columnOrder[$key]);
            }
          }
          $columnOrder = array_values($columnOrder);
          if (!empty($initialOrder)) {
            foreach ($initialOrder as $name => $label) {
              $columnOrder[] = $name;
            }
          }
          $columnOrder[] = $tableAction;
          array_unshift($columnOrder, 'selection', 'sl');
          $reportDetails->order = $columnOrder;
        }
        if (!empty($reportDetails->hiddenColumns)) {
          $hiddenColumns = $reportDetails->hiddenColumns;
          foreach ($reportDetails->hiddenColumns as $key => $value) {
            if (!isset($fieldNames[$value]) && ('sl' !== $value && 'selection' !== $value && 'table_ac' !== $value)) {
              unset($hiddenColumns[$key]);
            }
          }
          $reportDetails->hiddenColumns = array_values($hiddenColumns);
        }
        break;

      default:
        break;
    }
    return $reportDetails;
  }

  public function bulkDelete(array $condition = null, $check_operator = null)
  {
    if (
      !\is_null($condition)
      && \is_array($condition)
      && array_keys($condition) !== range(0, count($condition) - 1)
    ) {
      $delete_condition = $condition;
    } else {
      return new WP_Error(
        'deletion_error',
        __('At least 1 condition needed', 'bit-form')
      );
    }
    $formatted_conditions = $this->getFormatedCondition($delete_condition, $check_operator);
    if ($formatted_conditions) {
      $condition_to_check = $formatted_conditions['conditions'];
      $all_values = $formatted_conditions['values'];
    } else {
      $condition_to_check = null;
      return new WP_Error(
        'deletion_error',
        __('At least 1 condition needed', 'bit-form')
      );
    }
    $result = $this->app_db->query(
      $this->app_db->prepare(
        "DELETE FROM $this->table_name $condition_to_check",
        $all_values
      )
    );
    return $this->getResult($result);
  }
}
