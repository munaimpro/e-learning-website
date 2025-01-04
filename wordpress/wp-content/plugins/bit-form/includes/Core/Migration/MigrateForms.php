<?php

namespace BitCode\BitForm\Core\Migration;

use BitCode\BitForm\Core\Database\IntegrationModel;
use BitCode\BitForm\Core\Integration\IntegrationHandler;

final class MigrateForms
{
  public function migrateToV2()
  {
    $v1FormContents = get_transient('bitforms_v1_form_contents');
    $newFormContents = [];
    foreach ($v1FormContents as $formContents) {
      $migrationHandler = new Migration(json_decode(wp_json_encode($formContents)));
      $newFormContents[] = $migrationHandler->migrate();
    }
    self::migratePaypal();
    $config = (object) [];
    $config->delete_table = true;
    update_option('bitform_app_config', $config);
    set_transient('bitforms_v1_form_contents', $newFormContents);
    return $newFormContents;
  }

  private static function migratePaypal()
  {
    $integrationHandler = new IntegrationHandler(0);
    $formIntegrations = $integrationHandler->getAllIntegration('app', 'payments');
    if (!is_wp_error($formIntegrations)) {
      return false;
    }
    $integrationModel = new IntegrationModel();

    foreach ($formIntegrations as $payment) {
      $integrationDetails = json_decode($payment->integration_details);
      if ('PayPal' === $integrationDetails->type) {
        $integrationDetails->mode = 'live';
        $integrationDetails->clientSecret = '';
        $integrationModel->update([
          'integration_details' => wp_json_encode($integrationDetails),
        ], [
          'id' => $payment->id,
        ]);
      }
    }
    return true;
  }

  private function dropTableAndRename($deletedTableName, $renamedTableName)
  {
    global $wpdb;
    $deleteTable = "{$wpdb->prefix}bitforms_{$deletedTableName}";
    $renameTable = "{$wpdb->prefix}bitforms_{$renamedTableName}";
    // $wpdb->query("DROP TABLE IF EXISTS $deleteTable");
    // $wpdb->query("RENAME TABLE $renameTable TO $deleteTable");
    $wpdb->query(
      $wpdb->prepare(
        "DROP TABLE IF EXISTS $deleteTable"
      )
    );
    $wpdb->query(
      $wpdb->prepare(
        "RENAME TABLE $renameTable TO $deleteTable"
      )
    );
  }
}
