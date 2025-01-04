<?php

namespace BitCode\BitForm;

use BitCode\BitForm\BitApps\WPTelemetry\Telemetry\Telemetry;

if (!\defined('ABSPATH')) {
  exit;
}
class BfAnalytics
{
  public function modifyTelemetryData()
  {
    global $wpdb;
    $allForms = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}bitforms_form");

    $bfInfo = [];
    $formsArr = [];
    foreach ($allForms as $form) {
      $formData = [];
      $formData['id'] = $form->id;
      $formContent = json_decode($form->form_content, true);
      $formData['fields'] = $this->fieldCount($formContent['fields']);
      $formData['workflows'] = $this->countRow($form->id, 'bitforms_workflows');
      $formData['pdfTemplate'] = $this->countRow($form->id, 'bitforms_pdf_template');
      $formData['emailTemplate'] = $this->countRow($form->id, 'bitforms_email_template');
      $formData['integrations'] = $this->getIntegrations($form->id);
      $formData['formAbandonment'] = $this->getFormAbandonment($form->id);
      $formData['doubleOptin'] = $this->getDoubleOptin($form->id);
      $formData['tableView'] = $this->countRow($form->id, 'bitforms_frontend_views');
      $formsArr[] = $formData;
    }
    $bfInfo['forms'] = $formsArr;
    $bfInfo['totalForms'] = count($allForms);
    $bfInfo['reCaptchaV3'] = $this->getReCaptchaV3();
    $bfInfo['paymentGateway'] = $this->getPaymentGateway();
    $bfInfo['smtp'] = $this->isSMTPExist();

    return $bfInfo;
  }

  public function analyticsOptIn($permission)
  {
    if (true === $permission) {
      Telemetry::report()->trackingOptIn();
      return true;
    }
    Telemetry::report()->trackingOptOut();
    return false;
  }

  public function isTrackingEnabled()
  {
    return (bool) Telemetry::report()->isTrackingAllowed();
  }

  private function fieldCount($fields)
  {
    $count = [];
    foreach ($fields as $field) {
      if (isset($count[$field['typ']])) {
        $count[$field['typ']]++;
      } else {
        $count[$field['typ']] = 1;
      }
    }
    return $count;
  }

  private function countRow($formId, $tablename)
  {
    global $wpdb;
    $totalRow = $wpdb->get_results(
      $wpdb->prepare(
        "SELECT COUNT(*) as count FROM `{$wpdb->prefix}{$tablename}` WHERE form_id = %d;",
        $formId
      )
    );
    return $totalRow[0]->count || 0;
  }

  private function getIntegrations($formId)
  {
    global $wpdb;
    $integrations = $wpdb->get_results(
      $wpdb->prepare(
        "SELECT integration_type FROM `{$wpdb->prefix}bitforms_integration` WHERE form_id = %d AND category='form';",
        $formId
      )
    );

    $integrationNameArr = array_map(function ($integration) {
      return $integration->integration_type;
    }, $integrations);

    return $integrationNameArr;
  }

  private function getFormAbandonment($formId)
  {
    global $wpdb;
    $formAbandonment = $wpdb->get_results(
      $wpdb->prepare(
        "SELECT integration_details FROM `{$wpdb->prefix}bitforms_integration` WHERE form_id = %d AND category='formAbandonment';",
        $formId
      )
    );
    if (count($formAbandonment) > 0) {
      $formAbandonment = json_decode($formAbandonment[0]->integration_details, true);
    } else {
      $formAbandonment = (object)[];
    }
    return $formAbandonment;
  }

  private function getDoubleOptin($formId)
  {
    global $wpdb;
    $doubleOptin = $wpdb->get_results(
      $wpdb->prepare(
        "SELECT integration_details FROM `{$wpdb->prefix}bitforms_integration` WHERE category='doubleOptin' AND form_id = %d;",
        $formId
      )
    );
    if (count($doubleOptin) > 0) {
      return true;
    }
    return false;
  }

  private function getIntegType($integration_type)
  {
    global $wpdb;
    return $wpdb->get_results(
      $wpdb->prepare(
        "SELECT integration_type, integration_details FROM `{$wpdb->prefix}bitforms_integration` WHERE integration_type=%s;",
        $integration_type
      )
    );
  }

  private function getReCaptchaV3()
  {
    $name = $this->getIntegType('gReCaptchaV3');
    if (count($name) > 0) {
      return true;
    }
    return false;
  }

  private function getPaymentGateway()
  {
    $types = $this->getIntegType('payments');

    $paymentTypeArr = array_map(function ($type) {
      $integration_details = json_decode($type->integration_details, true);
      return $integration_details['type'];
    }, $types);

    return $paymentTypeArr;
  }

  private function isSMTPExist()
  {
    $type = $this->getIntegType('smtp');

    if (count($type) > 0) {
      return true;
    }
    return false;
  }
}
