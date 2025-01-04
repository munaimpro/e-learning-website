<?php

/**
 * Handle Form Create,Update,delete Operation
 *
 */

namespace BitCode\BitForm\Admin\Form;

class CustomFieldHandler
{
  public function updatedEntries($formEntries, $fieldDetails)
  {
    foreach ($formEntries['entries'] as $index => $entry) {
      foreach ($fieldDetails as $field) {
        $fieldKey = $field['key'];
        if (isset($field['customType']) && !empty($entry->$fieldKey) && !empty($field['customType']->hiddenValue)) {
          $hiddenvalue = $field['customType']->hiddenValue;
          if ('taxanomy_field' === $field['customType']->fieldType) {
            $hiddenvalue = $field['customType']->hiddenValue;
            if (isset($field['mul'])) {
              $mul = $field['mul'];
            } else {
              $mul = '';
            }

            $value = $this->getTermFieldValue($field['type'], $entry->$fieldKey, $mul, $hiddenvalue);
            $formEntries['entries'][$index]->$fieldKey = $value;
          } elseif ('user_field' === $field['customType']->fieldType) {
            $hiddenvalue = $field['customType']->hiddenValue;
            if (isset($field['mul'])) {
              $mul = $field['mul'];
            } else {
              $mul = '';
            }

            $value = $this->getUserFieldValue($field['type'], $entry->$fieldKey, $mul, $hiddenvalue);
            $formEntries['entries'][$index]->$fieldKey = $value;
          } elseif ('post_field' === $field['customType']->fieldType) {
            $hiddenvalue = $field['customType']->hiddenValue;
            if (isset($field['mul'])) {
              $mul = $field['mul'];
            } else {
              $mul = '';
            }

            $value = $this->getPostFieldValue($field['type'], $entry->$fieldKey, $mul, $hiddenvalue);
            $formEntries['entries'][$index]->$fieldKey = $value;
          }
        }
      }
    }
    return $formEntries;
  }

  public function updatedData($form_fields, $toUpdateValues)
  {
    foreach ($form_fields as $field) {
      if (isset($field['customType']) && !empty($toUpdateValues[$field['key']])) {
        if (isset($field['customType']->hiddenValue)) {
          $hiddenvalue = $field['customType']->hiddenValue;
          if ('taxanomy_field' === $field['customType']->fieldType) {
            if (isset($field['mul'])) {
              $mul = $field['mul'];
            } else {
              $mul = '';
            }

            $value = $this->getTermFieldValue($field['type'], $toUpdateValues[$field['key']], $mul, $hiddenvalue);
            $toUpdateValues[$field['key']] = $value;
          } elseif ('user_field' === $field['customType']->fieldType) {
            if (isset($field['mul'])) {
              $mul = $field['mul'];
            } else {
              $mul = '';
            }

            $value = $this->getUserFieldValue($field['type'], $toUpdateValues[$field['key']], $mul, $hiddenvalue);
            $toUpdateValues[$field['key']] = $value;
          } elseif ('post_field' === $field['customType']->fieldType) {
            if (isset($field['mul'])) {
              $mul = $field['mul'];
            } else {
              $mul = '';
            }

            $value = $this->getPostFieldValue($field['type'], $toUpdateValues[$field['key']], $mul, $hiddenvalue);
            $toUpdateValues[$field['key']] = $value;
          }
        }
      }
    }
    return $toUpdateValues;
  }

  public function getPostFieldValue($type, $fieldValue, $mul, $hiddenvalue)
  {
    $value = '';

    if ('select' === $type) {
      if (true === $mul) {
        $multipleValue = [];
        foreach (explode(',', $fieldValue) as $val) {
          $exists = get_post($val);
          if (false !== $exists) {
            $multipleValue[] = $exists->$hiddenvalue;
          }
        }
        $value = implode(',', $multipleValue);
      } else {
        $exists = get_post($fieldValue);
        if (false !== $exists) {
          $value = is_array($exists->$hiddenvalue) ? $exists->$hiddenvalue : (string) $exists->$hiddenvalue;
        }
      }
    } elseif ('radio' === $type || 'check' === $type) {
      if (is_array(json_decode($fieldValue))) {
        $multipleValues = [];
        foreach (json_decode($fieldValue) as $value) {
          $exists = get_post($value);
          if (false !== $exists) {
            $multipleValues[] = $exists->$hiddenvalue;
          }
        }
        $value = wp_json_encode($multipleValues);
      } else {
        $exists = get_post($fieldValue);
        if (false !== $exists) {
          $value = $exists->$hiddenvalue;
        }
      }
    }
    return $value;
  }

  public function getUserFieldValue($type, $fieldValue, $mul, $hiddenvalue)
  {
    $value = '';
    if ('select' === $type) {
      if (true === $mul) {
        $multipleValue = [];
        foreach (explode(',', $fieldValue) as $val) {
          $exists = get_user_by('ID', $val);
          if (false !== $exists) {
            $multipleValue[] = $exists->data->$hiddenvalue;
          }
        }
        $value = implode(',', $multipleValue);
      } else {
        $exists = get_user_by('ID', $fieldValue);
        if (false !== $exists) {
          $value = is_array($exists->data->$hiddenvalue) ? $exists->data->$hiddenvalue : (string) $exists->data->$hiddenvalue;
        }
      }
    } elseif ('radio' === $type || 'check' === $type) {
      if (is_array(json_decode($fieldValue))) {
        $multipleValues = [];
        foreach (json_decode($fieldValue) as $value) {
          $exists = get_user_by('ID', $value);
          if (false !== $exists) {
            $multipleValues[] = $exists->data->$hiddenvalue;
          }
        }
        $value = wp_json_encode($multipleValues);
      } else {
        $exists = get_user_by('ID', $fieldValue);
        if (false !== $exists) {
          $value = $exists->data->$hiddenvalue;
        }
      }
    }
    return $value;
  }

  public function getTermFieldValue($type, $fieldValue, $mul, $hiddenvalue)
  {
    $value = '';
    if ('select' === $type) {
      if (true === $mul) {
        $multipleValue = [];
        foreach (explode(',', $fieldValue) as $val) {
          $exists = get_term_by('term_taxonomy_id', $val);
          if (false !== $exists) {
            $multipleValue[] = $exists->$hiddenvalue;
          }
        }
        $value = implode(',', $multipleValue);
      } else {
        $exists = get_term_by('term_taxonomy_id', $fieldValue);
        if (false !== $exists) {
          $value = is_array($exists->$hiddenvalue) ? $exists->$hiddenvalue : (string) $exists->$hiddenvalue;
        }
      }
    } elseif ('radio' === $type || 'check' === $type) {
      if (is_array(json_decode($fieldValue))) {
        $multipleValues = [];
        foreach (json_decode($fieldValue) as $value) {
          $exists = get_term_by('term_taxonomy_id', $value);
          if (false !== $exists) {
            $multipleValues[] = $exists->$hiddenvalue;
          }
        }
        $value = wp_json_encode($multipleValues);
      } else {
        $exists = get_term_by('term_taxonomy_id', $fieldValue);
        if (false !== $exists) {
          $value = $exists->$hiddenvalue;
        }
      }
    }
    return $value;
  }
}
