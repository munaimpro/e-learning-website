<?php

namespace BitCode\BitForm\Core\Fallback;

use BitCode\BitForm\Admin\Form\AdminFormHandler;
use BitCode\BitForm\Core\Database\DB;
use BitCode\BitForm\Core\Database\FormModel;
use BitCode\BitForm\Core\Form\FormHandler;
use BitCode\BitForm\Core\Migration\MigrateForms;
use BitCode\BitForm\Core\Migration\MigrationHelper;

final class FormFallback
{
  public function resetJsGeneratedPageIds()
  {
    $formModel = new FormModel();
    $formModel->update(
      [
        'generated_script_page_ids' => wp_json_encode((object) []),
      ],
      [
        'status' => 1
      ]
    );
  }

  public function v1formMigragion()
  {
    $olderVersion = get_option('bitforms_version');
    $isMigratedToV2 = get_option('bitforms_migrated_to_v2');
    $isMigratingToV2 = get_option('bitforms_migrating_to_v2');
    if (!$isMigratedToV2 && !$isMigratingToV2 && $olderVersion) {
      $formHandler = FormHandler::getInstance();
      if (!$formHandler->admin) {
        $formHandler->admin = new AdminFormHandler();
      }
      $formHandler->admin->startMigrationProcess();
      update_site_option('bitforms_db_version', '2.0');
      DB::migrate();
      $migrateFormsHandler = new MigrateForms();
      $all_forms = $migrateFormsHandler->migrateToV2();
      foreach ($all_forms as $form) {
        $formatFormData = MigrationHelper::formatFormData($form);
        $formHandler->admin->updateForm(null, $formatFormData);
      }
    }
  }
}
