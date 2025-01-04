<?php

namespace BitCode\BitForm\Core\WorkFlow;

use BitCode\BitForm\Core\Integration\IntegrationHandler;
use BitCode\BitForm\Core\Messages\SuccessMessageHandler;
use BitCode\BitForm\Core\Util\FieldValueHandler;
use BitCode\BitForm\Core\Util\SmartTags;

final class Helper
{
  public static function getFieldData($fields)
  {
    $fieldData = [];

    foreach ($fields as $fieldKey => $fieldDetail) {
      $fieldData[$fieldKey] = [
        'key'   => $fieldKey,
        'value' => empty($fieldDetail->val) ? '' : $fieldDetail->val,
        'type'  => $fieldDetail->typ,
      ];
      if (isset($fieldDetail->mul)) {
        $fieldData[$fieldKey] =
            array_merge(
              $fieldData[$fieldKey],
              [
                'mul' => $fieldDetail->mul,
              ]
            );
      }
    }
    return $fieldData;
  }

  public static function smartFldMargeFormFld($logics, $fieldData)
  {
    $fieldKeys = SmartTags::smartTagFieldKeys();

    foreach ($logics as $logic) {
      $removeSpecialStr = ['${', '}', '()'];
      if (isset($logic->field) && in_array(str_replace($removeSpecialStr, '', $logic->field), $fieldKeys)) {
        $field = str_replace($removeSpecialStr, '', $logic->field);

        $fldKey = '${' . $field . '}';
        $customValue = isset($logic->smartKey) ? $logic->smartKey : '';
        $val = SmartTags::getSmartTagValue($field, false, $customValue);

        $fieldData[$fldKey] = [
          'key'   => $fldKey,
          'value' => empty($val) ? '' : $val,
          'type'  => is_string($val) ? 'text' : 'array',
        ];
      }
    }
    return $fieldData;
  }

  public static function replaceFieldWithValue($stringToReplaceField, $fieldValues, $evalMathExpr = true)
  {
    $stringToReplaceField = FieldValueHandler::replaceFieldWithValue($stringToReplaceField, $fieldValues);
    if ($evalMathExpr) {
      return self::evalMathExpression($stringToReplaceField);
    }
    return $stringToReplaceField;
  }

  public static function setDefaultSubmitConfirmation($confirmationType, $fieldValue, $formId, $logID = 0)
  {
    $messageId = 0;
    $returnableData = null;
    return [
      'msg_id'       => $messageId,
      'confirmation' => $returnableData,
    ];
    $integrationHandler = new IntegrationHandler($formId);
    switch ($confirmationType) {
      case 'successMsg':
        $successMessageHandler
        = new SuccessMessageHandler($formId);
        $successMessage = $successMessageHandler->getAllMessage();
        if (!is_wp_error($successMessage) && !empty($successMessage) && count($successMessage) > 0) {
          $returnableData = self::replaceFieldWithValue($successMessage[0]->message_content, $fieldValue);
          $messageId = $successMessage[0]->id;
        }
        break;
      case 'redirectPage':
        $redirectPage = $integrationHandler->getAllIntegration('form', 'redirectPage');
        if (!is_wp_error($redirectPage) && !empty($redirectPage) && count($redirectPage) > 0) {
          $url = json_decode($redirectPage[0]->integration_details)->url;
          if (!empty($url)) {
            $url = self::replaceFieldWithValue($url, $fieldValue);
          }
          $returnableData = empty($url) ? '' : esc_url_raw($url);
        }
        break;
      case 'webHooks':
        $webHooks = $integrationHandler->getAllIntegration('form', 'webHooks');
        if (!is_wp_error($webHooks) && !empty($webHooks) && 1 === count($webHooks)) {
          $returnableData = ["{\"id\":{$webHooks[0]->id}}"];
        }
        break;
      default:
        break;
    }

    return [
      'msg_id'       => $messageId,
      'confirmation' => $returnableData,
    ];
  }

  public static function calculte($firstOperand, $secondOperand, $operator)
  {
    switch ($operator) {
      case '+':
        return $firstOperand + $secondOperand;
      case '-':
        return $firstOperand - $secondOperand;
      case '*':
        return $firstOperand * $secondOperand;
      case '/':
        return $firstOperand / $secondOperand;
      case '^':
        return $firstOperand ** $secondOperand;
    }
  }

  public static function filterMailContentType()
  {
    return 'text/html';
  }

  public static function evalMathExpression($stringWithFieldValue)
  {
    $mathExpr = $stringWithFieldValue;
    if (empty($mathExpr)) {
      return $stringWithFieldValue;
    }
    preg_match_all('/[\+\-\*\/\s]+/', $mathExpr, $isMathExpr);
    if (empty($isMathExpr[0])) {
      return $stringWithFieldValue;
    }
    preg_match_all('/\w+/', $mathExpr, $exprValues);
    if (empty($exprValues[0])) {
      return $stringWithFieldValue;
    }
    foreach ($exprValues[0] as $opreands) {
      if (!is_numeric($opreands)) {
        return $stringWithFieldValue;
      }
    }
    $validOperator = ['+', '-', '*', '^', '/'];
    foreach ($isMathExpr[0] as $value) {
      if (!in_array(trim($value), $validOperator)) {
        return $stringWithFieldValue;
      }
    }
    $mathExpr = str_replace(' ', '', $mathExpr);
    $mathExpr = preg_replace('/\{|\[|\(/', '(', $mathExpr);
    $mathExpr = preg_replace('/\}|\]/', ')', $mathExpr);
    $calculated = self::infixToPostfixEvalute($mathExpr);
    if (!is_null($calculated)) {
      return (string) $calculated[0];
    }

    return (string) $stringWithFieldValue;
  }

  public static function infixToPostfixEvalute($expression)
  {
    $operatorStack = [];
    $outputQueue = [];
    $numTemp = null;
    for ($strIndex = 0; $strIndex < strlen($expression); $strIndex++) {
      $token = $expression[$strIndex];
      if ('+' === $token || '-' === $token || '*' === $token || '/' === $token || '^' === $token || '(' === $token || ')' === $token) {
        if (!is_null($numTemp)) {
          $outputQueue[] = $numTemp;
          $numTemp = null;
        }
        $stackSize = count($operatorStack);
        if ($stackSize) {
          $stackTop = $operatorStack[$stackSize - 1];
        }
        if ('(' === $token) {
          $operatorStack[] = $token;
        } elseif (')' === $token) {
          while ('(' !== $operatorStack[count($operatorStack) - 1]) {
            $outputQueue[] = array_pop($operatorStack);
            if ('(' === $operatorStack[count($operatorStack) - 1]) {
              array_pop($operatorStack);
              break;
            }
          }
        } elseif (isset($stackTop) && self::operatorPrecedence($token) > self::operatorPrecedence($stackTop)) {
          $operatorStack[] = $token;
        } elseif ('^' !== $token && $stackSize) {
          $operatorStack[$stackSize - 1] = $token;
          $outputQueue[] = $stackTop;
        } else {
          $operatorStack[] = $token;
        }
        continue;
      }
      $numTemp .= $token;
      if ($strIndex === strlen($expression) - 1 && !is_null($numTemp)) {
        $outputQueue[] = $numTemp;
      }
    }

    if (!is_null($operatorStack)) {
      $outputQueue = array_merge($outputQueue, array_reverse($operatorStack));
    }
    $resultStack = [];
    foreach ($outputQueue as $value) {
      if (is_numeric($value)) {
        $resultStack[] = $value;
        continue;
      }
      $secondOperand = array_pop($resultStack);
      $firstOperand = array_pop($resultStack);
      $resultStack[] = self::calculate($firstOperand, $secondOperand, $value);
    }
    return $resultStack;
  }

  public static function operatorPrecedence($operator)
  {
    $precedence = [
      '+' => 2,
      '-' => 2,
      '*' => 3,
      '/' => 3,
      '^' => 4,
    ];

    return isset($precedence[$operator]) ? $precedence[$operator] : 0;
  }

  public static function calculate($firstOperand, $secondOperand, $operator)
  {
    $calculated = [
      '+' => $firstOperand + $secondOperand,
      '-' => $firstOperand - $secondOperand,
      '*' => $firstOperand * $secondOperand,
      '/' => $firstOperand / $secondOperand,
      '^' => $firstOperand ** $secondOperand,
    ];

    return isset($calculated[$operator]) ? $calculated[$operator] : '';
  }
}
