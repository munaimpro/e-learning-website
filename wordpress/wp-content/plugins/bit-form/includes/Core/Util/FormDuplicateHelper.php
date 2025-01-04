<?php

namespace BitCode\BitForm\Core\Util;

/**
 * Helps to relpicate form information for form duplication or export
 *
 */
final class FormDuplicateHelper
{
  /**
   * Create replica
   *
   * @param Array          $data      Data to replicate
   * @param Integer        $oldFormId Id of the form which will be replicated
   * @param Integer|String $newFormId Id of the new form
   *
   * @return $formData Replicated data
   */
  public static function createReplica($data, $oldFormId, $newFormId)
  {
    // $new_lay_fields = static::genarateNewLayoutNField($data['form_content']['layout'], $data['form_content']['fields'], $oldFormId, $newFormId);
    $formName = $data['form_name'];
    $formData = (object)[];
    $formData->form_id = $newFormId;
    $formData->fields = (object) $data['form_content']['fields'];
    $formData->layout = $data['form_content']['layout'];
    $formData->nestedLayouts = $data['form_content']['nestedLayout'];
    $formData->form_name = $formName;
    $formData->formInfo = isset($data['form_content']['formInfo']) ? $data['form_content']['formInfo'] : (object)[];

    if (isset($data['formSettings'])) {
      $formSettings = (array) (!is_string($data['formSettings']) ? $data['formSettings'] : json_decode($data['formSettings']));

      if (isset($formSettings['confirmation'])) {
        $confirmation = !is_string($formSettings['confirmation']) ? wp_json_encode($formSettings['confirmation']) : $formSettings['confirmation'];
        $formSettings['confirmation'] = json_decode(\preg_replace('/(\${bf?)(\d+)([- \d]+\})/', '${1}' . $newFormId . '$3', $confirmation));
      }

      if (isset($formSettings['integrations'])) {
        $integrations = !is_string($formSettings['integrations']) ? wp_json_encode($formSettings['integrations']) : $formSettings['integrations'];
        $integrations = \preg_replace('/(\${bf?)(\d+)([- \d]+\})/', '${1}' . $newFormId . '$3', $integrations);
        $formSettings['integrations'] = json_decode(\preg_replace('/(\"formField\"\s*\:\s*\"bf?)(\d+)([- \d]+)/', '${1}' . $newFormId . '$3', $integrations));
      }

      if (isset($formSettings['mailTem'])) {
        $mailTem = !is_string($formSettings['mailTem']) ? wp_json_encode($formSettings['mailTem']) : $formSettings['mailTem'];
        $formSettings['mailTem'] = json_decode(\preg_replace('/(\${bf?)(\d+)([- \d]+\})/', '${1}' . $newFormId . '$3', $mailTem));
        $formData->formSettings = (object)$formSettings;
      }

      if (isset($formSettings['pdfTem'])) {
        $pdfTem = !is_string($formSettings['pdfTem']) ? wp_json_encode($formSettings['pdfTem']) : $formSettings['pdfTem'];
        $formSettings['pdfTem'] = json_decode(\preg_replace('/(\${bf?)(\d+)([- \d]+\})/', '${1}' . $newFormId . '$3', $pdfTem));
        $formData->formSettings = (object)$formSettings;
      }
    }

    if (isset($data['breakpointSize'])) {
      $formData->breakpointSize = $data['breakpointSize'];
    }
    if (isset($data['style'])) {
      // $styleTemp = !is_string($data['style']) ? wp_json_encode($data['style']) : $data['style'];
      // $formData->style = json_decode(\preg_replace('/(\.?b)(\d+)(-\d+)/', '${1}' . $newFormId . '$3', $styleTemp));
      $formData->style = $data['style'];
    }
    if (isset($data['themeVars'])) {
      $formData->themeVars = $data['themeVars'];
    }
    if (isset($data['staticStyles'])) {
      $formData->staticStyles = $data['staticStyles'];
    }
    if (isset($data['themeColors'])) {
      $formData->themeColors = $data['themeColors'];
    }
    if (isset($data['builderSettings'])) {
      $formData->builderSettings = $data['builderSettings'];
    }

    if (isset($data['workFlows'])) {
      $workFlows = !is_string($data['workFlows']) ? wp_json_encode($data['workFlows']) : $data['workFlows'];
      $workFlows = \preg_replace('/(\${bf?)(\d+)([- \d]+\})/', '${1}' . $newFormId . '$3', $workFlows);
      $workFlows = json_decode(\preg_replace('/(\"bf?)(\d+)([- \d]+\")/', '${1}' . $newFormId . '$3', $workFlows));
      $formData->workFlows = $workFlows;
    }

    if (isset($data['reports'])) {
      $reports = !is_string($data['reports']) ? wp_json_encode($data['reports']) : $data['reports'];
      $reports = json_decode(\preg_replace("/(\"bf?)(\d+)([- \d]+\")/", '${1}' . $newFormId . '$3', $reports));
      $formData->reports = $reports;
    }

    if (isset($data['additional'])) {
      $formData->additional = $data['additional'];
    }

    $formData->customCode = (object) [];
    if (isset($data['customCode']->customCss)) {
      $customCss = !is_string($data['customCode']->customCss) ? wp_json_encode($data['customCode']->customCss) : $data['customCode']->customCss;
      $customCss = \preg_replace('/(\${bf?)(\d+)([- \d]+\})/', '${1}' . $newFormId . '$3', $customCss);
      $formData->customCode->customCss = \preg_replace('/(\"bf?)(\d+)([- \d]+\")/', '${1}' . $newFormId . '$3', $customCss);
    }

    if (isset($data['customCode']->customJs)) {
      $customJs = !is_string($data['customCode']->customJs) ? wp_json_encode($data['customCode']->customJs) : $data['customCode']->customJs;
      $customJs = \preg_replace('/(\${bf?)(\d+)([- \d]+\})/', '${1}' . $newFormId . '$3', $customJs);
      $formData->customCode->customJs = \preg_replace('/(\"bf?)(\d+)([- \d]+\")/', '${1}' . $newFormId . '$3', $customJs);
    }
    return $formData;
  }

  public static function genarateNewLayoutNField($layout, $fields, $oldId, $newId)
  {
    $newField = (object) [];
    foreach ((array)$layout->lg as $ind => $itm) {
      $fld_tmp = $fields->{$layout->lg[$ind]->i};
      $layout->lg[$ind]->i = \preg_replace('/bf?(\d+)(-\d+)/', "b$newId$2", $layout->lg[$ind]->i);
      $layout->md[$ind]->i = \preg_replace('/bf?(\d+)(-\d+)/', "b$newId$2", $layout->md[$ind]->i);
      $layout->sm[$ind]->i = \preg_replace('/bf?(\d+)(-\d+)/', "b$newId$2", $layout->sm[$ind]->i);
      $newField->{$layout->lg[$ind]->i} = json_decode(\preg_replace('/bf?(\d+)(-\d+)/', "b$newId$2", wp_json_encode($fld_tmp)));
    }
    return ['layout' => $layout, 'fields' => $newField];
  }

  public static function calcRowHeight($formStyle, $formID)
  {
    $rowHeight = 0;
    preg_match('/fld-lbl-' . $formID . '\s*{[\w\-\(\)\!\;\,\s\:\.\#]*font-size\s*:\s*(\d+)[px|em|rem|!important]*;/', $formStyle, $fontSize);
    if ($fontSize) {
      $lineHeight = 1;
      preg_match('/fld-lbl-' . $formID . '\s*{[\w\-\(\)\!\;\,\s\:\#]*line-height\s*:\s*([.\d]+)[!important]*;/', $formStyle, $lineHeightMatched);
      if ($lineHeightMatched) {
        $lineHeight = $lineHeightMatched[1];
      }
      $rowHeight = $fontSize[1] * $lineHeight;
    }

    preg_match('/fld-wrp-' . $formID . '\s*{[\w\-\(\)\!\;\,\s\:\#]*padding\s*:\s*([\d\spx|em|rem|]+)[!important]*;/', $formStyle, $padding);
    if ($padding) {
      $rowHeight = $rowHeight + static::cssPropSumByAxis(explode(' ', preg_replace('/px|em|rem|!important/', '', $padding[1])), 'Y');
    }

    preg_match('/input.fld-' . $formID . '\s*,\s*textarea.fld-' . $formID . '\s*{[\w\-\(\)\!\;\,\s\:\#]*margin\s*:\s*([\d\spx|em|rem|]+)[!important]*;/', $formStyle, $margin);
    if ($margin) {
      $rowHeight = $rowHeight + static::cssPropSumByAxis(explode(' ', preg_replace('/px|em|rem|!important/', '', $margin[1])), 'Y');
    }
    preg_match('/input.fld-' . $formID . '\s*,\s*textarea.fld-' . $formID . '\s*{[\w\-\(\)\!\;\,\s\:\#]*[^line\-height]height\s*:\s*([\d\s]+)[px|em|rem|!important]*;/', $formStyle, $height);
    if ($height) {
      $rowHeight = $rowHeight + $height[1];
    } else {
      $rowHeight = $rowHeight + 40;
    }

    return $rowHeight / 2;
  }

  public static function cssPropSumByAxis($value, $axis)
  {
    if (1 === count($value)) {
      $value = array_fill(0, 4, $value[0]);
    }

    if (2 === count($value)) {
      $value[] = $value[0];
      $value[] = $value[1];
    }

    if (3 === count($value)) {
      $value[] = $value[1];
    }
    if ('X' === $axis) {
      return intval($value[1]) + intval($value[3]);
    } else {
      return intval($value[0]) + intval($value[2]);
    }
  }
}
