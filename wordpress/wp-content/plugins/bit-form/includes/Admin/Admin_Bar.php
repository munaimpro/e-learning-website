<?php

namespace BitCode\BitForm\Admin;

/**
 * The admin menu and page handler class
 */

use BitCode\BitForm\Core\Form\FormHandler;
use BitCode\BitForm\Core\Integration\IntegrationHandler;
use BitCode\BitForm\Core\Util\DateTimeHelper;
use BitCode\BitForm\Core\Util\FileDownloadProvider;
use BitCode\BitForm\Core\Util\IpTool;
use BitCode\BitForm\Core\Util\Utilities;
use WP_Admin_Bar;

class Admin_Bar
{
  private static $fonts_url = [
    'https://fonts.googleapis.com/css2?family=Outfit:wght@100;300;400;500;600;700&display=swap&famildy=Roboto:wght@300;400;500&display=swap',
    'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap',
  ];

  public function register()
  {
    add_action('in_admin_header', [$this, 'AdminNotices']);
    add_action('admin_menu', [$this, 'AdminMenu']);
    add_action('admin_enqueue_scripts', [$this, 'AdminAssets']);
    add_filter('style_loader_tag', [$this, 'linkTagFilter'], 0, 3);
    add_filter('script_loader_tag', [$this, 'scriptTagFilter'], 0, 3);
    add_action('admin_bar_menu', [$this, 'AdminTopMenu'], 999);
    add_action('admin_enqueue_scripts', [$this, 'bitforms_enqueue_admin_styles']);
  }

  /**
   * Register the admin menu
   *
   * @return void
   */
  public function AdminMenu()
  {
    global $submenu;

    $capability = apply_filters('bitforms_form_access_capability', 'manage_options');
    $menuTitle = Utilities::isPro() ? 'Bit Form Pro' : 'Bit Form';
    add_menu_page(
      __('Bit Form - Most advanced form builder and database management system', 'bit-form'),
      $menuTitle,
      $capability,
      'bitform',
      [$this, 'RootPage'],
      'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 163 163"><path fill="#fff" d="M106 0H57A57 57 0 000 57v49a57 57 0 0057 57h49a57 57 0 0057-57V57a57 57 0 00-57-57zM51 60v3l-1 59v12c0 5-3 8-8 6-2-1-3-4-3-6v-21-55a29 29 0 011-4 5 5 0 015-4h33a4 4 0 004-2c2-3 8-2 10-1a9 9 0 015 9 9 9 0 01-7 8c-3 1-6 0-9-3a3 3 0 00-1-1H51zm73 74c-6 5-12 6-20 6H72a8 8 0 01-4-1 5 5 0 01-2-6 5 5 0 015-3h35c11-1 20-10 21-20 0-11-8-20-18-23a6 6 0 00-1 0H78h-1v14l1 2h18a4 4 0 003-1c4-4 8-3 11-1 4 2 5 7 3 12-2 4-9 6-13 2a6 6 0 00-5-1H71c-3 0-5-2-5-6V82c0-4 1-5 5-6h17a21 21 0 0021-24c-1-9-8-16-16-18a25 25 0 00-6 0H46c-4 0-7-3-7-6s3-5 7-5h42a33 33 0 0132 23c2 11 0 20-6 29l-1 2 5 2c11 4 17 12 19 23 3 12-2 24-13 32z"/></svg>'),
      56
    );
    if (current_user_can($capability)) {
      $entriesCount = '';
      if (self::countUnreadEntries()) {
        $entriesCount = ' <span class="update-plugins">' . self::countUnreadEntries() . '</span>';
      }

      $submenu['bitform'][] = [__('All Forms' . $entriesCount, 'bit-form'), $capability, 'admin.php?page=bitform#/'];
      $submenu['bitform'][] = [__('Form Templates <span class="bf-template-new-badge">New</span>', 'bit-form'), $capability, 'admin.php?page=bitform#/form-templates'];
      $submenu['bitform'][] = [__('App Settings', 'bit-form'), $capability, 'admin.php?page=bitform#/app-settings/recaptcha'];
      $submenu['bitform'][] = [__('SMTP', 'bit-form'), $capability, 'admin.php?page=bitform#/app-settings/smtp'];
      $submenu['bitform'][] = [__('PDF Setting', 'bit-form'), $capability, 'admin.php?page=bitform#/app-settings/pdf'];
      $submenu['bitform'][] = [__('CPT', 'bit-form'), $capability, 'admin.php?page=bitform#/app-settings/cpt'];
      $submenu['bitform'][] = [__('Bit Form API', 'bit-form'), $capability, 'admin.php?page=bitform#/app-settings/api'];
      $submenu['bitform'][] = [__('Payments', 'bit-form'), $capability, 'admin.php?page=bitform#/app-settings/payments'];
      $submenu['bitform'][] = [__('Doc & Support', 'bit-form'), $capability, 'admin.php?page=bitform#/doc-support'];
      if (!Utilities::isPro()) {
        $submenu['bitform'][] = [__('<span class="bf-pro-btn">Upgrade to Pro</span>', 'bit-form'), $capability, 'https://bit-form.com/#pricing', '_blank'];
      }
    }
  }

  public function AdminTopMenu(WP_Admin_Bar $wp_admin_bar)
  {
    $indicator = '';
    if (self::countUnreadEntries()) {
      $indicator = ' <div class="wp-core-ui wp-ui-notification bf-indicator-badge">' . self::countUnreadEntries() . '</div>';
    }

    $wp_admin_bar->add_node([
      'id'    => 'bitform',
      'title' => 'Bit Form' . $indicator,
      'href'  => admin_url('admin.php?page=bitform'),
      'meta'  => [
        'class' => 'bitform-admin-bar',
      ],
    ]);
    $wp_admin_bar->add_node([
      'id'     => 'bitform-all-forms',
      'parent' => 'bitform',
      'title'  => 'All Forms' . $indicator,
      'href'   => admin_url('admin.php?page=bitform#/'),
    ]);
    $wp_admin_bar->add_node([
      'id'     => 'bitform-form-templates',
      'parent' => 'bitform',
      'title'  => 'Form Templates',
      'href'   => admin_url('admin.php?page=bitform#/form-templates'),
    ]);
    $wp_admin_bar->add_node([
      'id'     => 'bitform-app-settings',
      'parent' => 'bitform',
      'title'  => 'App Settings',
      'href'   => admin_url('admin.php?page=bitform#/app-settings/recaptcha'),
    ]);
    $wp_admin_bar->add_node([
      'id'     => 'bitform-smtp',
      'parent' => 'bitform',
      'title'  => 'SMTP',
      'href'   => admin_url('admin.php?page=bitform#/app-settings/smtp'),
    ]);
    $wp_admin_bar->add_node([
      'id'     => 'bitform-pdf-setting',
      'parent' => 'bitform',
      'title'  => 'PDF Setting',
      'href'   => admin_url('admin.php?page=bitform#/app-settings/pdf'),
    ]);
    $wp_admin_bar->add_node([
      'id'     => 'bitform-cpt',
      'parent' => 'bitform',
      'title'  => 'CPT',
      'href'   => admin_url('admin.php?page=bitform#/app-settings/cpt'),
    ]);
    $wp_admin_bar->add_node([
      'id'     => 'bitform-api',
      'parent' => 'bitform',
      'title'  => 'Bit Form API',
      'href'   => admin_url('admin.php?page=bitform#/app-settings/api'),
    ]);
    $wp_admin_bar->add_node([
      'id'     => 'bitform-payments',
      'parent' => 'bitform',
      'title'  => 'Payments',
      'href'   => admin_url('admin.php?page=bitform#/app-settings/payments'),
    ]);
  }

  public function getAllPages()
  {
    $pages = get_pages(['post_status' => 'publish', 'sort_column' => 'post_date', 'sort_order' => 'desc']);
    $allPages = [];
    foreach ($pages as $pageKey => $pageDetails) {
      $allPages[$pageKey]['title'] = $pageDetails->post_title;
      $allPages[$pageKey]['url'] = get_page_link($pageDetails->ID);
    }
    return $allPages;
  }

  /**
   * Load the asset libraries
   *
   * @return void
   */
  public function AdminAssets($current_screen)
  {
    if (!strpos($current_screen, 'bitform')) {
      return;
    }
    $parsed_url = wp_parse_url(get_admin_url());
    $site_url = $parsed_url['scheme'] . '://' . $parsed_url['host'];
    $site_url .= empty($parsed_url['port']) ? null : ':' . $parsed_url['port'];
    $base_path_admin = str_replace($site_url, '', get_admin_url());
    // loading google fonts
    wp_enqueue_style('googleapis-BITFORM-PRECONNECT', 'https://fonts.googleapis.com');
    wp_enqueue_style('gstatic-BITFORM-PRECONNECT-CROSSORIGIN', 'https://fonts.gstatic.com');
    foreach (self::$fonts_url as $i => $font_url) {
      wp_enqueue_style('bf-font-' . $i, $font_url);
    }

    if (defined('BITAPPS_DEV') && BITAPPS_DEV) {
      wp_enqueue_script('vite-client-helper-BITFORM-MODULE', BIT_DEV_URL . '/config/devHotModule.js', [], null);
      wp_enqueue_script('vite-client-BITFORM-MODULE', BIT_DEV_URL . '/@vite/client', [], null);
      wp_enqueue_script('index-BITFORM-MODULE', BIT_DEV_URL . '/main.jsx', [], null);
    }

    if (!defined('BITAPPS_DEV') || (defined('BITAPPS_DEV') && !BITAPPS_DEV)) {
      $build_hash = file_get_contents(BITFORMS_PLUGIN_DIR_PATH . '/build-hash.txt');
      wp_enqueue_script('index-BITFORM-MODULE', BITFORMS_ASSET_URI . "/main-{$build_hash}.js", [], null);
      wp_enqueue_style('bf-css', BITFORMS_ASSET_URI . "/main-{$build_hash}.css");
    }

    // if (defined('BITAPPS_DEV') && BITAPPS_DEV) {
    //   wp_enqueue_script(
    //     'bitforms-vendors',
    //     BITFORMS_ASSET_JS_URI . '/vendors-main.js',
    //     null,
    //     BITFORMS_VERSION,
    //     true
    //   );
    //   wp_enqueue_script(
    //     'bitforms-runtime',
    //     BITFORMS_ASSET_JS_URI . '/runtime.js',
    //     null,
    //     BITFORMS_VERSION,
    //     true
    //   );
    //   wp_enqueue_script(
    //     'bitforms-file',
    //     BITFORMS_ASSET_JS_URI . '/bitforms-file.js',
    //     ['bitforms-vendors', 'bitforms-runtime'],
    //     BITFORMS_VERSION,
    //     true
    //   );
    // }

    if (wp_script_is('wp-i18n')) {
      $deps = ['bitforms-vendors', 'bitforms-runtime', 'bitforms-file', 'wp-i18n'];
    } else {
      $deps = ['bitforms-vendors', 'bitforms-runtime', 'bitforms-file'];
    }
    if (!defined('BITAPPS_DEV')) {
      wp_enqueue_script(
        'bitforms-admin-script',
        BITFORMS_ASSET_JS_URI . '/index.js',
        $deps,
        BITFORMS_VERSION,
        true
      );
    }
    wp_enqueue_script('tinymce_js', includes_url('js/tinymce/') . 'wp-tinymce.php', null, false, true);

    if (!wp_script_is('media-upload')) {
      wp_enqueue_media();
    }

    $plugin_path = BITFORMS_ASSET_URI;

    echo "<meta name='theme-color' content='#13233c' />";

    if (defined('BITAPPS_DEV') && !BITAPPS_DEV) {
      wp_enqueue_style(
        'bitforms-styles',
        BITFORMS_ASSET_URI . '/css/bitforms.css',
        null,
        BITFORMS_VERSION,
        'screen'
      );

      wp_enqueue_style(
        'bitforms-components-styles',
        BITFORMS_ASSET_URI . '/css/components.css',
        null,
        BITFORMS_VERSION,
        'screen'
      );
    }
    $formHandler = FormHandler::getInstance();
    $all_forms = $formHandler->admin->getAllForm();
    $urlQuery = wp_parse_url(FileDownloadProvider::getBaseDownloadURL(), PHP_URL_QUERY);
    $baseDLURL = FileDownloadProvider::getBaseDownloadURL();
    $baseDLURL = empty($urlQuery) ? $baseDLURL . '?' : $baseDLURL . '&';
    $ipTool = new IpTool();
    $user_details = $ipTool->getUserDetail();
    $integrationHandler = new IntegrationHandler(0, $user_details);
    $allFormIntegrations = $integrationHandler->getAllIntegration('app');
    $allFormSettings = [];
    if (!is_wp_error($allFormIntegrations)) {
      $integCount = [];
      foreach ($allFormIntegrations as $integration) {
        $type = $integration->integration_type;
        if (!is_null($type)) {
          if (!isset($integCount[$type])) {
            $integCount[$type] = 1;
          } else {
            $integCount[$type] += 1;
          }
        }
      }
      foreach ($allFormIntegrations as $integration) {
        $type = $integration->integration_type;
        if (!is_null($type)) {
          $integrationDetails = json_decode($integration->integration_details);
          $integrationDetails->id = $integration->id;
          if ($integCount[$type] > 1) {
            if (!isset($allFormSettings[$type])) {
              $allFormSettings[$type] = [];
            }
            array_push($allFormSettings[$type], $integrationDetails);
          } else {
            $allFormSettings[$type] = $integrationDetails;
          }
        }
      }
    }

    $users = get_users(['fields' => ['ID', 'user_nicename', 'user_email', 'display_name']]);
    $userMail = [];
    $userNames = [];
    foreach ($users as $key => $value) {
      $userMail[$key]['label'] = !empty($value->display_name) ? $value->display_name : '';
      $userMail[$key]['value'] = !empty($value->user_email) ? $value->user_email : '';
      $userMail[$key]['id'] = $value->ID;
      $userNames[$value->ID] = ['name' => $value->display_name, 'url' => get_edit_user_link($value->ID)];
    }
    $bits = [
      'configs'             => ['bf_separator' => BITFORMS_BF_SEPARATOR],
      'nonce'               => wp_create_nonce('bitforms_save'),
      'isPro'               => false,
      'siteURL'             => site_url(),
      'assetsURL'           => BITFORMS_ASSET_URI,
      'baseURL'             => $base_path_admin . 'admin.php?page=bitform#',
      'baseDLURL'           => $baseDLURL,
      'styleURL'            => BITFORMS_UPLOAD_BASE_URL . '/form-styles',
      'iconURL'             => BITFORMS_UPLOAD_BASE_URL . '/icons',
      'ajaxURL'             => admin_url('admin-ajax.php'),
      'allForms'            => is_wp_error($all_forms) ? null : $all_forms,
      'allPages'            => is_wp_error($this->getAllPages()) ? [] : $this->getAllPages(),
      'allFormSettings'     => count($allFormSettings) > 0 ? $allFormSettings : (object)[],
      'userMail'            => !empty($userMail) ? $userMail : [],
      'user'                => (object) $userNames,
      'dateFormat'          => get_option('date_format'),
      'timeFormat'          => get_option('time_format'),
      'timeZone'            => DateTimeHelper::wp_timezone_string(),
      'googleRedirectURL'   => get_rest_url() . 'bitform/v1/google',
      'oneDriveRedirectURL' => get_rest_url() . 'bitform/v1/oneDrive',
      'zohoRedirectURL'     => get_rest_url() . 'bitform/v1/zoho',
      'userRoles'           => get_editable_roles(),
      'downloadedPdfFonts'  => is_array(get_option('bitforms_pdf_fonts')) ? get_option('bitforms_pdf_fonts') : [],
      'permission'          => empty(get_option('bitforms_allow_tracking')) ? false : true,
      'templatePath'        => BITFORMS_ROOT_URI . '/static/templates',
      'serverInfo'          => ['php_version' => phpversion(), 'engine' => $_SERVER['SERVER_SOFTWARE'], 'wp_version' => get_bloginfo('version'), 'loaded_extensions' => get_loaded_extensions()],
    ];

    $isMigratingToV2 = get_option('bitforms_migrating_to_v2', false);
    if ($isMigratingToV2) {
      $bits['isMigratingToV2'] = $isMigratingToV2;
    }

    if (defined('BITFORMS_VERSION')) {
      $bits['version'] = BITFORMS_VERSION;
    }

    $changelogVersion = get_option('bitforms_changelog_version', '0.0.0');
    $bits['changelogVersion'] = $changelogVersion;

    $hideAnnouncementModal = (bool) get_option('bitforms_hide_announcement', false);
    $bits['hideAnnouncementModal'] = $hideAnnouncementModal;

    $hideCashbackModal = (bool) get_option('bitforms_hide_cashback', false);
    $bits['hideCashbackModal'] = $hideCashbackModal;

    global $wpdb;
    $hasV1FormTable = $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}bitforms_form_v1'");
    if ($hasV1FormTable) {
      $bits['canRollbackToV1'] = true;
    }

    $bitforms = apply_filters(
      'bitforms_localized_script',
      $bits
    );
    if ('en_US' !== get_locale() && file_exists(BITFORMS_PLUGIN_DIR_PATH . '/languages/generatedString.php')) {
      include_once BITFORMS_PLUGIN_DIR_PATH . '/languages/generatedString.php';
      $bitforms['translations'] = $i18n_strings;
    }
    wp_localize_script('index-BITFORM-MODULE', 'bits', $bitforms);
    // !BIT_DEV && wp_localize_script('bitforms-admin-script', 'bits', $bitforms);
    // echo !BIT_DEV ? '' : "<script>console.log(window.bits=JSON.parse('" . str_replace("'", '', wp_json_encode($bitforms)) . "'))</script>";
    // if (\function_exists('wp_set_script_translations'))
    // wp_set_script_translations('bitforms-admin-script', 'bitform',  BITFORMS_PLUGIN_DIR_PATH . 'languages');
  }

  public function bitforms_enqueue_admin_styles()
  {
    wp_enqueue_style(
      'bitforms-admin-styles',
      BITFORMS_ROOT_URI . '/resources/admin.css',
      [],
      BITFORMS_VERSION,
      'all'
    );
  }

  /**
   * Bitforms apps-root id provider
   * @return void
   */
  public function RootPage()
  {
    require_once BITFORMS_PLUGIN_DIR_PATH . '/views/view-root.php';
  }

  public function AdminNotices()
  {
    global $plugin_page;
    $plugin_page = null === $plugin_page ? '' : $plugin_page;
    if (false === strpos($plugin_page, 'bitform')) {
      return;
    }
    remove_all_actions('admin_notices');
    remove_all_actions('all_admin_notices');
  }

  /**
   * Modify style tags
   *
   * @param string $html link tag
   *
   * @return string new link tag
   */
  public function linkTagFilter($html, $handle, $href)
  {
    $newTag = $html;
    if (preg_match('/BITFORM-PRECONNECT/', $handle)) {
      $newTag = preg_replace('/rel=("|\')stylesheet("|\')/', 'rel="preconnect"', $newTag);
    }
    if (preg_match('/BITFORM-PRELOAD/', $handle)) {
      $newTag = preg_replace('/rel=("|\')stylesheet("|\')/', 'rel="preload"', $newTag);
    }
    if (preg_match('/BITFORM-CROSSORIGIN/', $handle)) {
      $newTag = preg_replace('/<link /', '<link crossorigin ', $newTag);
    }
    if (preg_match('/BITFORM-SCRIPT/', $handle)) {
      $newTag = preg_replace('/<link /', '<link as="script" ', $newTag);
    }
    return $newTag;
  }

  /**
   * Modify script tags
   *
   * @param string $html script tag
   *
   * @return string new script tag
   */
  public function scriptTagFilter($html, $handle, $href)
  {
    $newTag = $html;
    if (preg_match('/BITFORM-MODULE/', $handle)) {
      $newTag = preg_replace('/<script /', '<script type="module" ', $newTag);
    }
    return $newTag;
  }

  private static function countUnreadEntries()
  {
    global $wpdb;
    $unreadEntries = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}bitforms_form_entries WHERE status = 1");
    return $unreadEntries;
  }
}
