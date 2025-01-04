<?php

namespace BitCode\BitForm\Core\Database;

final class Helper
{
  // public static function getTableName($className)
  // {
  //     $className = str_replace('\\', '', $className);
  //     $className = str_replace('Model', '', $className);
  //     return strtolower($className);
  // }
  public function arrValueModifyByLogic($operator, $value)
  {
    $oprators = [
      'equal'          => '["' . implode('","', $value) . '"]',
      'not_equal'      => '["' . implode('","', $value) . '"]',
      'start_with'     => '["' . implode('","', $value) . '"%',
      'not_start_with' => '["' . implode('","', $value) . '"%',
      'end_with'       => '%"' . implode('","', $value) . '"]',
      'not_end_with'   => '%"' . implode('","', $value) . '"]',
      'contain'        => '%"' . implode('","', $value) . '"%',
      'not_contain'    => '%"' . implode('","', $value) . '"%',
      'empty'          => '',
      'not_empty'      => '',
    ];

    if (isset($oprators[$operator])) {
      return $oprators[$operator];
    }

    return $value;
  }

  public function dateQueryList()
  {
    return [
      'today'          => 'Y-m-d_+0 days_=',
      'till_today'     => 'Y-m-d_+0 days_<=',
      'tomorrow'       => 'Y-m-d_+1 days_=',
      'till_tomorrow'  => 'Y-m-d_+1 days_<=',
      'yesterday'      => 'Y-m-d_-1 days_=',
      'till_yesterday' => 'Y-m-d_-1 days_<=',
      'last_7_days'    => 'Y-m-d_-7 days_>=',
      'last_30_days'   => 'Y-m-d_-30 days_>=',
      'last_60_days'   => 'Y-m-d_-60 days_>=',
      'last_90_days'   => 'Y-m-d_-90 days_>=',
      'last_120_days'  => 'Y-m-d_-120 days_>=',
      'next_7_days'    => 'Y-m-d_+7 days_>=',
      'next_30_days'   => 'Y-m-d_+30 days_>=',
      'next_60_days'   => 'Y-m-d_+60 days_>=',
      'next_90_days'   => 'Y-m-d_+90 days_>=',
      'next_120_days'  => 'Y-m-d_+120 days_>=',
      'current_month'  => 'm_+0 month_=',
      'next_month'     => 'm_first day of next month_=',
      'last_month'     => 'm_first day of last month_=',
      'current_week'   => 'W_0 week_=',
      'next_week'      => 'W_+1 week_=',
      'last_week'      => 'W_-1 week_=',
      'current_year'   => 'Y_+0 year_=',
      'next_year'      => 'Y_+1 year_=',
      'last_year'      => 'Y_-1 year_=',
    ];
  }

  public function strValueModifyByLogic($operator, $value)
  {
    $timeInterval = $this->dateQueryList();

    if (isset($timeInterval[$operator])) {
      $data = explode('_', $timeInterval[$operator]);
      if (is_array($data) && count($data) > 0) {
        $value = date($data[0], strtotime($data[1]));
      }
    }

    $oprators = [
      'greater_than'       => (int) $value,
      'less_than'          => (int) $value,
      'greater_than_equal' => (int) $value,
      'less_than_equal'    => (int) $value,
      'start_with'         => $value . '%',
      'end_with'           => '%' . $value,
      'contain'            => '%' . $value . '%',
      'empty'              => '',
      'not_empty'          => '',
      'not_start_with'     => $value . '%',
      'not_end_with'       => '%' . $value,
      'not_contain'        => '%' . $value . '%',
    ];

    if (isset($oprators[$operator])) {
      $value = $oprators[$operator];
    }

    if (is_array($value)) {
      $value = implode(',', $value);
    }

    return $value;
  }

  public function convertToSqlOperator($operator)
  {
    $timeInterval = $this->dateQueryList();

    if (isset($timeInterval[$operator])) {
      $data = explode('_', $timeInterval[$operator]);
      return $data[2];
    }

    $oprators = [
      'greater_than'       => '>',
      'less_than'          => '<',
      'greater_than_equal' => '>=',
      'less_than_equal'    => '<=',
      'equal'              => '=',
      'not_equal'          => '!=',
      'empty'              => '=',
      'not_empty'          => '!=',
      'start_with'         => 'LIKE',
      'not_start_with'     => 'NOT LIKE',
      'end_with'           => 'LIKE',
      'not_end_with'       => 'NOT LIKE',
      'contain'            => 'LIKE',
      'not_contain'        => 'NOT LIKE',
    ];

    if (isset($oprators[$operator])) {
      return $oprators[$operator];
    }

    return $operator;
  }

  public function fieldQueryByDate($fld, $operator, $value, $logic)
  {
    $currentYear = date('Y');

    $sql = '';

    $daysInterval = [
      'today',
      'till_today',
      'tomorrow',
      'till_tomorrow',
      'yesterday',
      'till_yesterday',
      'last_7_days',
      'last_30_days',
      'last_60_days',
      'last_90_days',
      'last_120_days',
      'next_7_days',
      'next_30_days',
      'next_60_days',
      'next_90_days',
      'next_120_days',
    ];
    if (in_array($logic, $daysInterval)) {
      $sql = "`$fld` $operator '$value'";
    }

    if (in_array($logic, ['current_month', 'next_month', 'last_month'])) {
      $sql = "MONTH(`$fld`) $operator '$value' AND YEAR(`$fld`) = '$currentYear'";
    }

    if (in_array($logic, ['current_week', 'next_week', 'last_week'])) {
      $sql = "WEEK(`$fld`) $operator '$value' AND YEAR(`$fld`) = $currentYear";
    }
    return $sql;
  }
}
