<?php

namespace BitCode\BitForm\Core\Util;

use BitCode\BitForm\Admin\Form\Helpers;

final class FieldValueHandler
{
  public static function replaceFieldWithValue($stringToReplaceField, $fieldValues)
  {
    if (empty($stringToReplaceField)) {
      return $stringToReplaceField;
    }
    if (!is_string($stringToReplaceField)) {
      $stringToReplaceField = wp_json_encode($stringToReplaceField);
    }
    $stringToReplaceField = static::replaceSmartTagWithValue($stringToReplaceField);
    $fieldPattern = '/\${\w[^ ${}]*}/';
    preg_match_all($fieldPattern, $stringToReplaceField, $matchedField);
    if (empty($matchedField)) {
      return $stringToReplaceField;
    }
    $uniqueFieldsInStr = array_unique($matchedField[0]);
    foreach ($uniqueFieldsInStr as $key => $value) {
      $fieldName = substr($value, 2, strlen($value) - 3);
      $fieldValue = null;
      if (isset($fieldValues[$fieldName])) {
        $targetFieldValue = isset($fieldValues[$fieldName]['value']) ? $fieldValues[$fieldName]['value'] : $fieldValues[$fieldName];
        if ('array' === gettype($targetFieldValue) || 'object' === gettype($targetFieldValue)) {
          foreach ((array) $targetFieldValue as $singleTargetVal) {
            if (isset($fieldValue)) {
              if (is_numeric($fieldValue) && is_numeric($singleTargetVal)) {
                $fieldValue = $fieldValue + $singleTargetVal;
              } else {
                $fieldValue = "$fieldValue,  $singleTargetVal";
              }
            } else {
              $fieldValue = $singleTargetVal;
            }
          }
          // $fieldValue = wp_json_encode($targetFieldValue);
        } else {
          $fieldValue = strval($targetFieldValue);
        }
        $stringToReplaceField = str_replace($value, $fieldValue, $stringToReplaceField);
      } else {
        $stringToReplaceField = str_replace($value, '', $stringToReplaceField);
      }
    }

    // check if the string is a function like : "${_bf_calc(${b27-5}*10)}"
    // TO DO: Implement the function properly
    // if (self::isFunction($stringToReplaceField)) {
    //   $functionName = self::getFunctionName($stringToReplaceField);

    //   switch ($functionName) {
    //     case '_bf_calc':
    //       return self::getFunctionParameter($stringToReplaceField);
    //     case '_bf_count':
    //       return self::getCountValue($stringToReplaceField);
    //     default:
    //       return 0;
    //   }
    // }
    return $stringToReplaceField;
  }

  /**
   * Summary of getCountValue - get the count value from the function string "${_bf_count(item-1, item-2)}" => 2
   *
   * @param string $functionString
   * @return int
   */
  private static function getCountValue(string $functionString): int
  {
    $options = self::getFunctionParameter($functionString);
    $option = explode(',', $options);
    return count($option);
  }

  /**
   * Summary of getFunctionParameter - get the function parameter from the function string "${_bf_calc(2*10)}" => 2*10
   *
   * @param string $functionString
   * @return string
   */
  private static function getFunctionParameter(string $functionString): string
  {
    $regexPattern = '/\(([^)]*)\)/';
    preg_match($regexPattern, $functionString, $matches);
    return $matches[1];
  }

  /**
   * Summary of isFunction - check if the string is a function "${_bf_calc(${b27-5}*10)}" or not "${_bf_date}"
   *
   * @param string $functionString
   * @return bool true if the string is a function else false
   */
  private static function isFunction(string $functionString): bool
  {
    $regexPattern = '/\([^)]*\)/';
    return preg_match($regexPattern, $functionString);
  }

  /**
   * Summary of getFunctionName - get the function name from the function string "${_bf_calc(${b27-5}*10)}" => _bf_calc
   *
   * @param string $functionString
   * @return string
   */
  private static function getFunctionName(string $functionString): string
  {
    $regexPattern = '/\b([a-zA-Z_][a-zA-Z0-9_]*)\(/';
    preg_match($regexPattern, $functionString, $matches);
    return $matches[1];
  }

  public static function validateMailArry($emailAddresses, $fieldValues)
  {
    if (!is_array($emailAddresses)) {
      return [FieldValueHandler::replaceFieldWithValue($emailAddresses, $fieldValues)];
    }
    foreach ($emailAddresses as $key => $email) {
      if (!is_email($email)) {
        $email = FieldValueHandler::replaceFieldWithValue($email, $fieldValues);
        if (is_email($email)) {
          $emailAddresses[$key] = $email;
        }
      }
    }
    return $emailAddresses;
  }

  public static function replaceSmartTagWithValue($fieldValues)
  {
    $fieldPattern = '/(\${_[^{]*?)(?=\})}/';
    $matchPattern = preg_match_all($fieldPattern, $fieldValues, $matchedField);
    if (!$matchPattern) {
      return $fieldValues;
    }

    $ajaxRequest = false;
    if (isset($_REQUEST['action']) && 'bitforms_trigger_workflow' === $_REQUEST['action']) {
      $ajaxRequest = true;
    }

    foreach (array_unique($matchedField[0]) as $value) {
      $fieldName = trim(substr($value, 2, strlen($value) - 3));

      $matches = preg_match('/\("*([^\)]+"*)\)/', $value, $matchCustomFormat);

      $customValue = '';
      if ($matches) {
        $removeQuote = ["'", '"'];
        $customValue = str_replace($removeQuote, '', $matchCustomFormat[1]);
        $fieldName = str_replace($matchCustomFormat[0], '', $fieldName);
      }

      $tagFieldValues = SmartTags::getSmartTagValue($fieldName, $ajaxRequest, $customValue);
      $fieldValues = str_replace($value, $tagFieldValues, $fieldValues);
    }
    return $fieldValues;
  }

  public static function isEmpty($val)
  {
    if (empty($val) && !in_array($val, ['0', 0, 0.0], true)) {
      return true;
    }
    return false;
  }

  public static function formatFieldValueForMail($fields, $fieldValues)
  {
    $formattedFldValues = $fieldValues;
    $repeaterFieldKey = [];
    $file_upload_types = Helpers::$file_upload_types;
    $repeated_array_type_data_fields = Helpers::$repeated_array_type_data_fields;
    foreach ($fields as $fldKey => $fldData) {
      if (in_array($fldData->typ, $file_upload_types)) {
        continue;
      }
      if (array_key_exists($fldKey, $fieldValues)) {
        $value = $fieldValues[$fldKey];
        // if (is_array($value)) {
        //   $formattedFldValues[$fldKey] = htmlspecialchars(implode(', ', $value));
        // } else {
        //   $formattedFldValues[$fldKey] = htmlspecialchars($value);
        // }

        if (is_array($value)) {
          $arrValue = '';
          foreach ($value as $v) {
            if (is_array($v)) {
              foreach ($v as $k1 => $v1) {
                if (array_key_exists($k1, $repeaterFieldKey)) {
                  $oldValue = $repeaterFieldKey[$k1];
                  if (is_array($v1) && in_array($fields->{$k1}->typ, $repeated_array_type_data_fields)) {
                    $newValues = '[' . implode(', ', $v1) . '] ';
                    if (!preg_match('/\[.*\]/', $oldValue)) {
                      $oldValue = '[' . $oldValue . '] ';
                    }
                  } else {
                    $newValues = $v1;
                  }
                  $repeaterFieldKey[$k1] = $oldValue . ', ' . $newValues;
                } else {
                  if (!empty($v1) && is_array($v1)) {
                    $repeaterFieldKey[$k1] = htmlspecialchars(implode(', ', $v1));
                  } else {
                    $repeaterFieldKey[$k1] = htmlspecialchars($v1);
                  }
                }
              }
            } else {
              $arrValue .= $v . ', ';
            }
          }
          $formattedFldValues[$fldKey] = htmlspecialchars(rtrim($arrValue, ', '));
          $arrValue = '';
        } else {
          $formattedFldValues[$fldKey] = htmlspecialchars($value);
        }
        if ('textarea' === $fldData->typ) {
          $formattedFldValues[$fldKey] = nl2br(htmlspecialchars($value));
        }
        if ('date' === $fldData->typ && !empty($value)) {
          $formattedFldValues[$fldKey] = date_i18n(get_option('date_format'), strtotime(htmlspecialchars($value)));
        }
      }
    }

    $merge_values = array_merge($fieldValues, $formattedFldValues);
    $merge_values = array_merge($merge_values, $repeaterFieldKey);
    return $merge_values;
  }

  public static function changeImagePathInHTMLString($html_body, $path)
  {
    $html_body = preg_replace_callback(
      '/<img\s+src="([^"]+)"/i',
      function ($matches) use ($path) {
        $src = $matches[1];
        if (parse_url($src, PHP_URL_SCHEME) === null) {
          $src = $path . ltrim($src, '/');
        }
        return '<img src="' . htmlspecialchars($src, ENT_QUOTES) . '"';
      },
      $html_body
    );

    return $html_body;
  }
}