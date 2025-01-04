<?php

namespace BitCode\BitForm\Core\Hooks;

use BitCode\BitForm\Admin\Admin_Bar;
use BitCode\BitForm\API\Route\Routes;
use BitCode\BitForm\BfAnalytics;
use BitCode\BitForm\Core\Ajax\AjaxService;
use BitCode\BitForm\Core\Capability\Request;
use BitCode\BitForm\Core\Database\FormModel;
use BitCode\BitForm\Core\Fallback\FormFallback;
use BitCode\BitForm\Core\Form\FormHandler;
use BitCode\BitForm\Core\Integration\Integrations;
use BitCode\BitForm\Core\Util\FileDownloadProvider;
use BitCode\BitForm\Core\Util\GutenBlockProvider;
use BitCode\BitForm\Core\Util\Utilities;
use BitCode\BitForm\Frontend\ConversationalFormView;
use BitCode\BitForm\Frontend\StandaloneFormView;

class Hooks
{
  public static function init_hooks()
  {
    add_action('init', [PostType::class, 'registerBitformsPostType']);
    add_action('init', [PostType::class, 'registerCustomPostType']);
    add_action('bitforms_exec_integrations', [Integrations::class, 'integrationExecutionHelper'], 1, 5);
    add_action('init', [Hooks::class, 'localization_setup']);
    add_action('init', [Hooks::class, 'init_classes']);
    add_action('init', [Hooks::class, 'versionUpdateRunFallbacks']);
    add_action('rest_api_init', [Hooks::class, 'registerRoutes']);
    add_filter('plugin_action_links_' . plugin_basename(BITFORMS_PLUGIN_MAIN_FILE), [Hooks::class, 'plugin_action_links']);
    add_action('bitform_dequeue_scripts', [Hooks::class, 'dequeueScripts'], 1000, 100);
    add_action('bitform_dequeue_styles', [Hooks::class, 'dequeueStyles'], 1000, 100);
    add_action('init', [ConversationalFormView::class, 'conversationalFormView']);
    add_action('init', [StandaloneFormView::class, 'standaloneFormView']);
    add_action('wp_footer', [Hooks::class, 'updateBitFormVersion'], 9999, 0);

    // modify data for telemetry
    add_filter(BITFORMS_PREFIX . 'telemetry_additional_data', [new BfAnalytics(), 'modifyTelemetryData'], 10, 1);

    // Allow SVG file uploads in bit form
    add_filter('upload_mimes', [Hooks::class, 'custom_mime_types']);

    // Add Bit Form menu to admin bar by "manage_bitform" capability
    add_filter('bitforms_form_access_capability', [Hooks::class, 'bitformMenuAccessCapability']);
  }

  // Allow SVG file uploads
  public static function custom_mime_types($mimes)
  {
    // Add SVG to the list of allowed mime types
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
  }

  public static function updateBitFormVersion()
  {
    $currentBitFormVersion = BITFORMS_VERSION;
    $installedBitFormVersion = get_option('bitforms_version');
    if ($currentBitFormVersion !== $installedBitFormVersion) {
      (new FormFallback())->resetJsGeneratedPageIds();
      update_option('bitforms_version', $currentBitFormVersion);
    }
  }

  public static function registerRoutes()
  {
    $routes = new Routes();
    $routes->register_routes();
  }

  public static function isGeneratedScript($postId, $oldPost, $updatedPost)
  {
    $oldContent = $oldPost->post_content;
    $updatedContent = $updatedPost->post_content;
    $oldShortCodeMatch = \preg_match_all("/\[bitform\s+id\s*=\s*('|\")\s*(\d+)\s*('|\")\]/", $oldContent, $oldShortCodes);
    $updatedShortCodeMatch = \preg_match_all("/\[bitform\s+id\s*=\s*('|\")\s*(\d+)\s*('|\")\]/", $updatedContent, $updatedShortCodes);

    if ($oldShortCodeMatch && $updatedShortCodeMatch) {
      $oldShortCodes = $oldShortCodes[2];
      $updatedShortCodes = $updatedShortCodes[2];

      $checkEqualArray = array_diff($oldShortCodes, $updatedShortCodes);

      if (!empty($checkEqualArray)) {
        $formModel = new FormModel();
        $value = "%$postId%";
        $condtions = [
          'generated_script_page_ids' => ['operator' => 'LIKE', 'value' => $value],
        ];
        $forms = $formModel->get('generated_script_page_ids,id', $condtions);
        foreach ($forms as $form) {
          $pageIds = json_decode($form->generated_script_page_ids);
          if (property_exists($pageIds, $postId)) {
            unset($pageIds->{$postId});
            $formModel->update(['generated_script_page_ids' => \wp_json_encode($pageIds)], ['id' => $form->id]);
          }
        }
      }
    }
  }

  public static function localization_setup()
  {
    load_plugin_textdomain('bit-form', false, dirname(BITFORMS_PLUGIN_BASENAME) . '/languages');
  }

  public static function init_classes()
  {
    if (Request::Check('admin')) {
      (new Admin_Bar())->register();
    }
    if (Request::Check('ajax')) {
      new AjaxService();
    }
    if (Request::Check('frontend')) {
      $formHandler = new FormHandler();
      $formHandler->frontend;
    }
    if (Request::isPluginPage()) {
      (new FileDownloadProvider())->register();
    }
    if (current_user_can('edit_posts')) {
      (new GutenBlockProvider())->register();
    }
    include BITFORMS_PLUGIN_DIR_PATH . 'includes' . DIRECTORY_SEPARATOR . 'Frontend' . DIRECTORY_SEPARATOR . 'InitRoutes.php';
  }

  public static function versionUpdateRunFallbacks()
  {
    $dir = BITFORMS_PLUGIN_DIR_PATH . 'includes' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Fallback';
    if (is_dir($dir) && is_file($dir . DIRECTORY_SEPARATOR . 'Init.php')) {
      include $dir . DIRECTORY_SEPARATOR . 'Init.php';
    }
  }

  public static function plugin_action_links($links)
  {
    $links[] = '<a href="https://bitapps.pro/docs/bit-form/" target="_blank">' . __('Docs') . '</a>';
    if (!Utilities::isPro()) {
      // $links[] = '<a href="https://www.bitapps.pro/bit-form" target="_blank"><strong>' . __('Upgrade to Pro') . '</strong></a>';
      $links[] = '<a href="https://bitapps.pro/christmas-wordpress-plugin-deal/#bit-form-pricing?link_type=promo&utm_source=bit-form&utm_medium=update_button&utm_campaign=christmas&utm_content=plugins_list_directory" target="_blank"><strong>' . __('Get 50% Off! Christmas Deal.') . '</strong></a>';
    }
    return $links;
  }

  public static function dequeueScripts(...$formIds)
  {
    foreach ($formIds as $formId) {
      wp_deregister_script("bitform-script-{$formId}");
      wp_dequeue_script("bitform-script-{$formId}");
    }
    global $bitform_dequeued_scripts;
    if (!is_array($bitform_dequeued_scripts)) {
      $bitform_dequeued_scripts = [];
    }
    $bitform_dequeued_scripts = array_merge($bitform_dequeued_scripts, $formIds);
  }

  public static function dequeueStyles(...$formIds)
  {
    foreach ($formIds as $formId) {
      wp_deregister_style("bitform-style-{$formId}");
      wp_dequeue_style("bitform-style-{$formId}");
      wp_deregister_style("bitform-style-{$formId}-formid");
      wp_dequeue_style("bitform-style-{$formId}-formid");
    }
    global $bitform_dequeued_styles;
    if (!is_array($bitform_dequeued_styles)) {
      $bitform_dequeued_styles = [];
    }
    $bitform_dequeued_styles = array_merge($bitform_dequeued_styles, $formIds);
  }

  public static function bitformMenuAccessCapability()
  {
    if (current_user_can('manage_bitform')) {
      return 'manage_bitform';
    } elseif (current_user_can('access_bitform')) {
      return 'access_bitform';
    }
    return 'manage_options';
  }
}
