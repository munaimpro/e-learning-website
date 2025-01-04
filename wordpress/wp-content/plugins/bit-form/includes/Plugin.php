<?php

namespace BitCode\BitForm;

/**
 * Main class for the plugin.
 *
 * @since 1.0.0-alpha
 */

use BitCode\BitForm\BitApps\WPTelemetry\Telemetry\Telemetry;
use BitCode\BitForm\BitApps\WPTelemetry\Telemetry\TelemetryConfig;
use BitCode\BitForm\Core\Database\DB;
use BitCode\BitForm\Core\Database\FormModel;
use BitCode\BitForm\Core\Fallback\FormFallback;
use BitCode\BitForm\Core\Hooks\Hooks;
use BitCode\BitForm\Core\Util\Activation;
use BitCode\BitForm\Core\Util\Deactivation;
use BitCode\BitForm\Core\Util\Uninstallation;

final class Plugin
{
  /**
   * Main instance of the plugin.
   *
   * @since 1.0.0-alpha
   * @var   Plugin|null
   */
  private static $instance = null;

  /**
   * Registers the plugin with WordPress.
   *
   * @since 1.0.0-alpha
   */
  public function register()
  {
    add_action('plugins_loaded', [$this, 'init_plugin']);
    (new Activation())->activate();
    (new Deactivation())->register();
    (new Uninstallation())->register();

    $this->initWpTelemetry();
  }

  private function commaReplace($options, $lbl, $val)
  {
    foreach ($options as $key => $option) {
      if (!empty($option[$lbl]) && !empty($option[$val])) {
        $options[$key][$val] = str_replace(',', '_', $option[$val]);
      }
      if (!empty($option[$lbl]) && empty($option[$val])) {
        $options[$key][$val] = str_replace(',', '_', $option[$lbl]);
      }
    }
    return $options;
  }

  private function replaceOptionSeparator($formContent)
  {
    $updated = false;
    foreach ($formContent['fields'] as $key => $field) {
      if ('check' === $field['typ']) {
        $formContent['fields'][$key]['opt'] = $this->commaReplace($field['opt'], 'lbl', 'val');
        $updated = true;
      }
      if ('select' === $field['typ']) {
        $formContent['fields'][$key]['opt'] = $this->commaReplace($field['opt'], 'label', 'value');
        $updated = true;
      }
    }
    return [$formContent, $updated];
  }

  public function optionFallBack()
  {
    $formModel = new FormModel();
    $forms = $formModel->get(
      ['id', 'form_content']
    );

    foreach ($forms as $form) {
      $formContent = json_decode($form->form_content, true);
      $optReplaceComma = $this->replaceOptionSeparator($formContent);
      if ($optReplaceComma[1]) {
        $formModel->update(
          [
            'form_content' => wp_json_encode($optReplaceComma[0]),
          ],
          [
            'id' => $form->id,
          ]
        );
      }
    }
  }

  /**
   * Updates bit form content tables on wp db
   *
   * @return void
   */
  public function update_tables()
  {
    if (!current_user_can('manage_options')) {
      return;
    }

    global $bitforms_db_version;
    $installed_db_version = get_site_option('bitforms_db_version');
    if ($installed_db_version !== $bitforms_db_version) {
      DB::migrate();
    }
  }

  /**
   * Plugin action links
   *
   * @param  array $links
   *
   * @return array
   */
  public function init_plugin()
  {
    $currentBitFormVersion = BITFORMS_VERSION;
    $installedBitFormVersion = get_option('bitforms_version');
    if ($currentBitFormVersion !== $installedBitFormVersion) {
      (new FormFallback())->resetJsGeneratedPageIds();
    }
    Hooks::init_hooks();
    $this->update_tables();
    do_action('bitform_loaded');
  }

  /**
   * Retrieves the main instance of the plugin.
   *
   * @since 1.0.0-alpha
   *
   * @return BITFORM Plugin main instance.
   */
  public static function instance()
  {
    return static::$instance;
  }

  public function plugin_action_links($links)
  {
    $links[] = '<a href="https://bitapps.pro/docs/bit-form/" target="_blank">' . __('Docs') . '</a>';

    return $links;
  }

  /**
   * Loads the plugin main instance and initializes it.
   *
   * @param  string $main_file Absolute path to the plugin main file.
   *
   * @return bool True if the plugin main instance could be loaded, false otherwise.
   */
  public static function load($main_file)
  {
    if (null !== static::$instance) {
      return false;
    }

    static::$instance = new static($main_file);
    static::$instance->register();
    return true;
  }

  private function initWpTelemetry()
  {
    TelemetryConfig::setSlug(Config::SLUG);
    TelemetryConfig::setTitle(Config::TITLE);
    TelemetryConfig::setVersion(Config::VERSION);
    TelemetryConfig::setPrefix(Config::VAR_PREFIX);

    TelemetryConfig::setServerBaseUrl('https://wp-api.bitapps.pro/public/');
    TelemetryConfig::setTermsUrl('https://bitapps.pro/terms-of-service/');
    TelemetryConfig::setPolicyUrl('https://bitapps.pro/privacy-policy/');

    Telemetry::report()->addPluginData()->init();
    Telemetry::feedback()->init();
  }
}
