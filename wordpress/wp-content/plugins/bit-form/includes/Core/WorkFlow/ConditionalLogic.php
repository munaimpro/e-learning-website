<?php

namespace BitCode\BitForm\Core\WorkFlow;

final class ConditionalLogic
{
  protected $_workflow_condition;
  protected $_data;

  public function __construct($workFlowCondition, $data)
  {
    $this->_workflow_condition = $workFlowCondition;
    $this->_data = $data;
  }

  public function valueToCheck($logic)
  {
    if ((isset($this->_data[$this->_workflow_condition->field]['mul']) && $this->_data[$this->_workflow_condition->field]['mul'])
        || 'check' === $this->_data[$this->_workflow_condition->field]['type']
    ) {
      $fieldValue = !empty($this->_data[$this->_workflow_condition->field]['value']) ? $this->_data[$this->_workflow_condition->field]['value'] : [];
      if (is_string($fieldValue)) {
        if ('[' === $fieldValue[0] && ']' === $fieldValue[strlen($fieldValue) - 1]) {
          $fieldValue = json_decode($fieldValue);
        } else {
          $fieldValue = explode(',', $fieldValue);
        }
      }
      return [\explode(',', $this->_workflow_condition->val), $fieldValue];
    }
    $default = [
      'between'     => $this->_data[$this->_workflow_condition->field]['value'] === $this->_workflow_condition->val,
      'equal'       => $this->_data[$this->_workflow_condition->field]['value'] === $this->_workflow_condition->val,
      'not_equal'   => $this->_data[$this->_workflow_condition->field]['value'] !== $this->_workflow_condition->val,
      'contain'     => isset($this->_data[$this->_workflow_condition->field]['value']) && false !== stripos($this->_data[$this->_workflow_condition->field]['value'], $this->_workflow_condition->val),
      'not_contain' => false === stripos($this->_data[$this->_workflow_condition->field]['value'], $this->_workflow_condition->val),
      'contain_all' => isset($this->_data[$this->_workflow_condition->field]['value']) && false !== stripos($this->_data[$this->_workflow_condition->field]['value'], $this->_workflow_condition->val),
    ];

    return $default[$logic];
  }

  public function isBetween()
  {
    $fldValue = $this->_data[$this->_workflow_condition->field]['value'];
    $actionsValue = json_decode($this->_workflow_condition->val);
    if (is_object($actionsValue)) {
      $min = empty($actionsValue->min) ? '' : $actionsValue->min;
      $max = empty($actionsValue->max) ? '' : $actionsValue->max;
      return $fldValue >= $min && $fldValue <= $max;
    }
    return false;
  }

  public function isEqual()
  {
    $valueToCheckFun = self::valueToCheck('equal');

    if (!is_array($valueToCheckFun)) {
      return $valueToCheckFun;
    }

    $valueToCheck = $valueToCheckFun[0];
    $fieldValue = $valueToCheckFun[1];

    if (count($valueToCheck) !== count($fieldValue)) {
      return false;
    }
    $checker = 0;
    foreach ($valueToCheck as $value) {
      if (!empty($fieldValue) && \in_array($value, $fieldValue)) {
        $checker = $checker + 1;
      }
    }
    if ($checker === count($valueToCheck) && count($valueToCheck) === count($fieldValue)) {
      return true;
    }
    return false;
  }

  public function isNotEqual()
  {
    $valueToCheckFun = self::valueToCheck('not_equal');

    if (!is_array($valueToCheckFun)) {
      return $valueToCheckFun;
    }

    $valueToCheck = $valueToCheckFun[0];

    $fieldValue = $valueToCheckFun[1];

    $valueToCheckLenght = count($valueToCheck);
    if ($valueToCheckLenght !== count($fieldValue)) {
      return true;
    }
    $checker = 0;
    foreach ($valueToCheck as $value) {
      if (!in_array($value, $fieldValue)) {
        $checker += 1;
      }
    }
    return $valueToCheckLenght === $checker;
  }

  public function isNull()
  {
    return empty($this->_data[$this->_workflow_condition->field]['value']);
  }

  public function isNotNull()
  {
    return !empty($this->_data[$this->_workflow_condition->field]['value']);
  }

  public function isContain()
  {
    $valueToCheckFun = self::valueToCheck('contain');

    if (!is_array($valueToCheckFun)) {
      return $valueToCheckFun;
    }

    $valueToCheck = $valueToCheckFun[0];
    $fieldValue = $valueToCheckFun[1];

    $checker = 0;
    foreach ($valueToCheck as $value) {
      if (\in_array($value, $fieldValue)) {
        $checker = $checker + 1;
      }
    }
    if ($checker > 0) {
      return true;
    }
    return false;
  }

  public function isContainAll()
  {
    $valueToCheckFun = self::valueToCheck('contain_all');

    if (!is_array($valueToCheckFun)) {
      return $valueToCheckFun;
    }

    $valueToCheck = $valueToCheckFun[0];
    $fieldValue = $valueToCheckFun[1];

    $checker = 0;
    foreach ($valueToCheck as $value) {
      if (\in_array($value, $fieldValue)) {
        $checker = $checker + 1;
      }
    }
    if ($checker >= count($valueToCheck)) {
      return true;
    }
    return false;
  }

  public function isNotContain()
  {
    $valueToCheckFun = self::valueToCheck('not_contain');

    if (!is_array($valueToCheckFun)) {
      return $valueToCheckFun;
    }

    $valueToCheck = $valueToCheckFun[0];
    $fieldValue = $valueToCheckFun[1];

    $checker = 0;
    foreach ($valueToCheck as $value) {
      if (!in_array($value, $fieldValue)) {
        $checker = $checker + 1;
      }
    }
    if ($checker === count($valueToCheck)) {
      return true;
    }
    return false;
  }

  public function isGreaterThenValue()
  {
    if (!isset($this->_data[$this->_workflow_condition->field]['value'])) {
      return false;
    }
    return isset($this->_data[$this->_workflow_condition->field]['value']) && $this->_data[$this->_workflow_condition->field]['value'] > $this->_workflow_condition->val;
  }

  public function isLessThenValue()
  {
    if (!isset($this->_data[$this->_workflow_condition->field]['value'])) {
      return false;
    }
    return isset($this->_data[$this->_workflow_condition->field]['value']) && $this->_data[$this->_workflow_condition->field]['value'] < $this->_workflow_condition->val;
  }

  public function isGreatertOrEqual()
  {
    if (!isset($this->_data[$this->_workflow_condition->field]['value'])) {
      return false;
    }
    return isset($this->_data[$this->_workflow_condition->field]['value']) && $this->_data[$this->_workflow_condition->field]['value'] >= $this->_workflow_condition->val;
  }

  public function isLessOrEqual()
  {
    if (!isset($this->_data[$this->_workflow_condition->field]['value'])) {
      return false;
    }
    return isset($this->_data[$this->_workflow_condition->field]['value']) && $this->_data[$this->_workflow_condition->field]['value'] <= $this->_workflow_condition->val;
  }

  public function isStarttWithString()
  {
    if (!isset($this->_data[$this->_workflow_condition->field]['value'])) {
      return false;
    }
    return isset($this->_data[$this->_workflow_condition->field]['value']) && 0 === stripos($this->_data[$this->_workflow_condition->field]['value'], $this->_workflow_condition->val);
  }

  public function isEndWithString()
  {
    if (!isset($this->_data[$this->_workflow_condition->field]['value'])) {
      return false;
    }
    $fieldValue = $this->_data[$this->_workflow_condition->field]['value'];
    $fieldValueLength = strlen($this->_data[$this->_workflow_condition->field]['value']);
    $compareValue = strtolower($this->_workflow_condition->val);
    $compareValueLength = strlen($this->_workflow_condition->val);
    $fieldValueEnds = strtolower(substr($fieldValue, $fieldValueLength - $compareValueLength, $fieldValueLength));
    return $compareValue === $fieldValueEnds;
  }

  public function logicOnFieldActionValue($actionDetail, $fieldData, $fields)
  {
    if (!empty($actionDetail->val) && !empty($fieldData[$actionDetail->field]['key'])) {
      $actionValue = Helper::replaceFieldWithValue($actionDetail->val, $fieldData);
      $fields->{$fieldData[$actionDetail->field]['key']}->val = Helper::evalMathExpression($actionValue);
      $fieldData[$actionDetail->field]['value'] = $actionValue;
    }
    return $fieldData;
  }

  public function logicOnFieldActionShow($actionDetail, $fieldData, $fields)
  {
    $fields->{$fieldData[$actionDetail->field]['key']}->valid->hide = false;
    if ('hidden' === $fields->{$fieldData[$actionDetail->field]['key']}->typ) {
      $fields->{$fieldData[$actionDetail->field]['key']}->typ = 'text';
    }
    return $fieldData;
  }

  public function logicOnFieldActions($workFlowReturnable, $fieldData, $fields, $actionDetail)
  {
    return [
      'value'    => self::logicOnFieldActionValue($actionDetail, $fieldData, $fields),
      'hide'     => $fields->{$fieldData[$actionDetail->field]['key']}->valid->disabled = true,
      'enable'   => $fields->{$fieldData[$actionDetail->field]['key']}->valid->disabled = false,
      'disable'  => $fields->{$fieldData[$actionDetail->field]['key']}->valid->disabled = true,
      'readonly' => $fields->{$fieldData[$actionDetail->field]['key']}->valid->readonly = true,
      'show'     => self::logicOnFieldActionShow($actionDetail, $fieldData, $fields),
    ];
    // return $workFlowReturnable['fields'];
  }

  public function getConditionStatus()
  {
    $workflows = $this->_workflow_condition;
    if (is_array($this->_workflow_condition)) {
      foreach ($this->_workflow_condition as $sskey => $ssvalue) {
        if (!is_string($ssvalue)) {
          $this->_workflow_condition = $ssvalue;
          $isCondition = $this->getConditionStatus();

          if (0 === $sskey) {
            $conditionStatus = $isCondition;
          }

          if ($sskey - 1 >= 0 && is_string($workflows[$sskey - 1])) {
            switch (strtolower($workflows[$sskey - 1])) {
              case 'or':
                $conditionStatus = $conditionStatus || $isCondition;
                break;

              case 'and':
                $conditionStatus = $conditionStatus && $isCondition;
                break;

              default:
                break;
            }
          }
        }
      }
      return (bool) $conditionStatus;
    } else {
      $this->_workflow_condition->val = Helper::replaceFieldWithValue($this->_workflow_condition->val, $this->_data);

      return $this->isLogicMatch($this->_workflow_condition->logic);
    }
  }

  public function isLogicMatch($logic)
  {
    if (isset($this->_workflow_condition->smartKey)) {
      $this->_workflow_condition->field = str_replace('()', '', $this->_workflow_condition->field);
    }

    if (!isset($this->_data[$this->_workflow_condition->field])) {
      return false;
    }
    switch (strtolower($logic)) {
      case 'equal':
        return self::isEqual();

      case 'not_equal':
        return self::isNotEqual();

      case 'null':
        return self::isNull();

      case 'not_null':
        return self::isNotNull();

      case 'contain':
        return self::isContain();

      case 'contain_all':
        return self::isContainAll();

      case 'not_contain':
        return self::isNotContain();

      case 'greater':
        return self::isGreaterThenValue();

      case 'less':
        return self::isLessThenValue();

      case 'greater_or_equal':
        return self::isGreatertOrEqual();

      case 'less_or_equal':
        return self::isLessOrEqual();

      case 'start_with':
        return self::isStarttWithString();

      case 'end_with':
        return self::isEndWithString();

      case 'between':
        return self::isBetween();

      default:
        return false;
    }
  }
}
