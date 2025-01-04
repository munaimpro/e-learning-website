<?php

namespace BitCode\BitForm\Core\Util;

final class FileDownloadProvider
{
  public function register()
  {
    add_action('template_redirect', [$this, 'authCheckandFrceDownloadHelper']);
    add_shortcode('bitforms-frontend-file', [$this, 'handleFileDownload']);
  }

  public function handleFileDownload()
  {
    if (!isset($_GET['formID']) || !isset($_GET['entryID']) || !isset($_GET['fileID'])) {
      global $wp_query;
      $wp_query->set_404();
      status_header(404);
      get_template_part(404);
      exit();
    }
    $formID = intval(sanitize_text_field($_GET['formID']));
    $entryID = intval(sanitize_text_field($_GET['entryID']));
    $fileID = sanitize_file_name($_GET['fileID']);
    $filePath = BITFORMS_UPLOAD_DIR . DIRECTORY_SEPARATOR . $formID . DIRECTORY_SEPARATOR . $entryID . DIRECTORY_SEPARATOR . $fileID;
    if (is_readable($filePath)) {
      $this->fileDownloadORView($filePath, true);
    }
  }

  public static function getBaseDownloadURL()
  {
    $routes = get_option('bitforms_routes');
    if (isset($routes['file'])) {
      $file_page = get_post($routes['file']);
      if (empty($file_page)) {
        $file_route_id = wp_insert_post(
          [
            'post_name'      => 'bitforms-file',
            'comment_status' => 'closed',
            'ping_status'    => 'closed',
            'post_content'   => '<!-- wp:shortcode -->[bitforms-frontend-file /]<!-- /wp:shortcode -->',
            'post_status'    => 'publish',
            'post_type'      => 'bitforms'
          ]
        );
        $routes['file'] = $file_route_id;
        update_option('bitforms_routes', $routes);
        $file_page_slug = get_post_permalink($file_route_id);
      } else {
        $file_page_slug = get_post_permalink($file_page->ID);
      }
    } else {
      $file_route_id = wp_insert_post(
        [
          'post_name'      => 'bitforms-file',
          'comment_status' => 'closed',
          'ping_status'    => 'closed',
          'post_content'   => '<!-- wp:shortcode -->[bitforms-frontend-file /]<!-- /wp:shortcode -->',
          'post_status'    => 'publish',
          'post_type'      => 'bitforms'
        ]
      );
      $route_value = [];
      $route_value['file'] = $file_route_id;
      update_option('bitforms_routes', $route_value);
      $file_page_slug = get_post_permalink($file_route_id);
    }

    return $file_page_slug;
  }

  public function authCheckandFrceDownloadHelper()
  {
    if (!is_singular('bitforms')) {
      return;
    }
    global $post;
    if (!empty($post->post_content)) {
      $shortCodeRegex = get_shortcode_regex();
      preg_match_all('/' . $shortCodeRegex . '/', $post->post_content, $regexMatchGroups);
      if (!empty($regexMatchGroups[2]) && in_array('bitforms-frontend-file', $regexMatchGroups[2]) && is_user_logged_in()) {
        $file = $this->isRequestedFileExists();
        if ($file) {
          $this->fileDownloadORView($file, isset($_GET['download']));
        } else {
          $this->show404();
        }
      } else {
        auth_redirect();
      }
    }
  }

  private function show404()
  {
    global $wp_query;
    $wp_query->set_404();
    status_header(404);
    get_template_part(404);
    exit();
  }

  private function isRequestedFileExists()
  {
    if (!isset($_GET['formID']) || !isset($_GET['entryID']) || !isset($_GET['fileID'])) {
      return false;
    }
    $formID = intval(sanitize_text_field($_GET['formID']));
    $entryID = intval(sanitize_text_field($_GET['entryID']));
    $fileID = sanitize_file_name($_GET['fileID']);
    $filePath = BITFORMS_UPLOAD_DIR . DIRECTORY_SEPARATOR . $formID . DIRECTORY_SEPARATOR . $entryID . DIRECTORY_SEPARATOR . $fileID;
    if (is_readable($filePath)) {
      return $filePath;
    }

    return false;
  }

  private function fileDownloadORView($filePath, $forceDownload = false)
  {
    if ($forceDownload) {
      header('Content-Type: application/force-download');
      header('Content-Type: application/octet-stream');
      header('Content-Type: application/download');
      header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
    } else {
      $fileInfo = wp_check_filetype($filePath);
      $content_types = 'text/plain';
      if ($fileInfo['type'] && $fileInfo['ext']) {
        $content_types = $fileInfo['type'];
        $ext = $fileInfo['ext'];
        if (in_array($ext[1], ['txt', 'php', 'html', 'xhtml', 'json'])) {
          $content_types = 'text/plain';
        }
      }
      header('Content-Disposition:filename="' . basename($filePath) . '"');
      header("Content-Type: $content_types");
    }
    header('Content-Description: File Transfer');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filePath));
    header('Content-Transfer-Encoding: binary ');
    flush();
    readfile($filePath);
    die();
  }
}
