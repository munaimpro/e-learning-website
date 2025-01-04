<?php

namespace BitCode\BitForm\Core\Util;

use BitCode\BitForm\Core\Database\DB;
use BitCode\BitForm\Core\Database\FormModel;
use WP_Error;

/**
 * Class handling plugin activation.
 *
 * @since 1.0.0
 */
final class Activation
{
  private static $formModel;

  public function __construct()
  {
    static::$formModel = new FormModel();
  }

  public function activate()
  {
    add_action('bitforms_activation', [$this, 'install']);
  }

  public function install()
  {
    $installed = get_option('bitforms_installed');

    if (!$installed) {
      DB::migrate();
      $this->createUploadDir();
      update_option('bitforms_installed', time());
    }

    update_option('bitforms_migrated_to_v2', true);
    $appConfig = get_option('bitform_app_config');
    if (!$appConfig) {
      $config = (object) [];
      $config->delete_table = true;
      update_option('bitform_app_config', $config);
    }
    update_option('bitforms_version', BITFORMS_VERSION);
    $this->layoutUpdate();
    $this->createUploadDir();
    $this->createFrontendPages();
  }

  private function createUploadDir()
  {
    if (!file_exists(BITFORMS_UPLOAD_DIR)) {
      wp_mkdir_p(BITFORMS_UPLOAD_DIR);
    }
    if (!file_exists(BITFORMS_CONTENT_DIR . DIRECTORY_SEPARATOR . 'form-styles')) {
      wp_mkdir_p(BITFORMS_CONTENT_DIR . DIRECTORY_SEPARATOR . 'form-styles');
    }
    if (file_exists(BITFORMS_UPLOAD_DIR) && !file_exists(BITFORMS_UPLOAD_DIR . DIRECTORY_SEPARATOR . '.htaccess')) {
      $htaccessFile = fopen(BITFORMS_UPLOAD_DIR . DIRECTORY_SEPARATOR . '.htaccess', 'w');
      $rules = '
            <IfDefine php_flag>
                php_flag engine off
            </IfDefine>
            Options -Indexes
            Order allow,deny
            Deny from all
            Require all denied
            ';
      fwrite($htaccessFile, $rules);
      fclose($htaccessFile);
    }
    if (file_exists(BITFORMS_UPLOAD_DIR) && !file_exists(BITFORMS_UPLOAD_DIR . DIRECTORY_SEPARATOR . 'index.php')) {
      $indexFile = fopen(BITFORMS_UPLOAD_DIR . DIRECTORY_SEPARATOR . 'index.php', 'w');
      $code = "<?php\n";
      fwrite($indexFile, $code);
      fclose($indexFile);
    }
    if (file_exists(BITFORMS_CONTENT_DIR) && !file_exists(BITFORMS_CONTENT_DIR . DIRECTORY_SEPARATOR . 'index.php')) {
      $indexFile = fopen(BITFORMS_CONTENT_DIR . DIRECTORY_SEPARATOR . 'index.php', 'w');
      $code = "<?php\n";
      fwrite($indexFile, $code);
      fclose($indexFile);
    }
  }

  private function getForms()
  {
    $getForms = static::$formModel->get(
      [
        'id',
        'status',
        'form_content',
      ],
      [
        'status' => [
          'operator' => '!=',
          'value'    => 2,
        ],
      ]
    );
    return $getForms;
  }

  private function layoutUpdate()
  {
    $getForms = $this->getForms();

    if (is_wp_error($getForms)) {
      return $getForms;
    }

    global $wpdb;

    foreach ($getForms as $index => $form) {
      $content = json_decode($form->form_content);
      $layout = $content->layout;
      $updated = false;

      if (is_array($layout)) {
        continue;
      }
      foreach ($layout->lg as $lyIndex => $lg) {
        if ($lg->w < 10) {
          $updated = true;
          $layout->lg[$lyIndex]->h = $lg->h * 20;
          $layout->lg[$lyIndex]->w = $lg->w * 10;
          $layout->lg[$lyIndex]->x = $lg->x * 10;
          $layout->lg[$lyIndex]->y = $lg->y * 20;

          $layout->md[$lyIndex]->h = $layout->md[$lyIndex]->h * 20;
          $layout->md[$lyIndex]->w = $layout->md[$lyIndex]->w * 10;
          $layout->md[$lyIndex]->x = $layout->md[$lyIndex]->x * 10;
          $layout->md[$lyIndex]->y = $layout->md[$lyIndex]->y * 20;

          $layout->sm[$lyIndex]->h = $layout->sm[$lyIndex]->h * 20;
          $layout->sm[$lyIndex]->w = $layout->sm[$lyIndex]->w * 10;
          $layout->sm[$lyIndex]->x = $layout->sm[$lyIndex]->x * 10;
          $layout->sm[$lyIndex]->y = $layout->sm[$lyIndex]->y * 20;

          if (isset($layout->lg[$lyIndex]->maxW)) {
            $layout->lg[$lyIndex]->maxW = $lg->maxW * 10;
          }
          if (isset($layout->lg[$lyIndex]->maxH)) {
            $layout->lg[$lyIndex]->maxH = $lg->maxH * 20;
          }
          if (isset($layout->lg[$lyIndex]->minW)) {
            $layout->lg[$lyIndex]->minW = $lg->minW * 10;
          }
          if (isset($layout->lg[$lyIndex]->minH)) {
            $layout->lg[$lyIndex]->minH = $lg->minH * 20;
          }

          if (isset($layout->md[$lyIndex]->maxW)) {
            $layout->md[$lyIndex]->maxW = $layout->md[$lyIndex]->maxW * 10;
          }
          if (isset($layout->md[$lyIndex]->maxH)) {
            $layout->md[$lyIndex]->maxH = $layout->md[$lyIndex]->maxH * 20;
          }
          if (isset($layout->md[$lyIndex]->minW)) {
            $layout->md[$lyIndex]->minW = $layout->md[$lyIndex]->minW * 10;
          }
          if (isset($layout->md[$lyIndex]->minH)) {
            $layout->md[$lyIndex]->minH = $layout->md[$lyIndex]->minH * 20;
          }

          if (isset($layout->sm[$lyIndex]->maxW)) {
            $layout->sm[$lyIndex]->maxW = $layout->sm[$lyIndex]->maxW * 10;
          }
          if (isset($layout->sm[$lyIndex]->maxH)) {
            $layout->sm[$lyIndex]->maxH = $layout->sm[$lyIndex]->maxH * 20;
          }
          if (isset($layout->sm[$lyIndex]->minW)) {
            $layout->sm[$lyIndex]->minW = $layout->md[$lyIndex]->minW * 10;
          }
          if (isset($layout->sm[$lyIndex]->minH)) {
            $layout->sm[$lyIndex]->minH = $layout->sm[$lyIndex]->minH * 20;
          }
        }
      }

      $getForms[$index]->form_content = wp_json_encode($content);

      $wpdb->query('START TRANSACTION');

      if ($updated) {
        $status = static::$formModel->update(
          [
            'form_content' => $getForms[$index]->form_content,
          ],
          [
            'id' => $form->id,
          ]
        );

        if (is_wp_error($status)) {
          $wpdb->query('ROLLBACK');
          return new WP_Error('update_error', __('Sorry, Error occured in updated form', 'bit-form'));
        }
      }

      $wpdb->query('COMMIT');
    }
  }

  private function createFrontendPages()
  {
    $args = [
      'label'           => __('Bitforms Pages', 'bit-form'),
      'public'          => true,
      'show_ui'         => true,
      'show_in_menu'    => false,
      'capability_type' => 'page',
      'hierarchical'    => false,
      'query_var'       => false,
      'supports'        => ['title'],
      'show_in_rest'    => false
    ];
    register_post_type('bitforms', $args);
    $routes = get_option('bitforms_routes');
    $route_value = [];
    if (!$routes) {
      $file_route_id = $this->insertPage();
      if (!is_wp_error($file_route_id)) {
        $route_value['file'] = $file_route_id;
      }
    } elseif (isset($routes['file'])) {
      if (empty(get_post($routes['file']))) {
        $file_route_id = $this->insertPage();
        if (!is_wp_error($file_route_id)) {
          $route_value['file'] = $file_route_id;
        }
      } else {
        $file_page = ['ID' => $routes['file'], 'post_status' => 'publish'];
        wp_update_post($file_page);
      }
    }
    if (!empty($route_value)) {
      update_option('bitforms_routes', esc_sql($route_value));
    }
    flush_rewrite_rules();
  }

  private function insertPage()
  {
    return  wp_insert_post(
      [
        'post_name'      => 'bitforms-file',
        'comment_status' => 'closed',
        'ping_status'    => 'closed',
        'post_content'   => '<!-- wp:shortcode -->[bitforms-frontend-file /]<!-- /wp:shortcode -->',
        'post_status'    => 'publish',
        'post_type'      => 'bitforms'
      ]
    );
  }
}
