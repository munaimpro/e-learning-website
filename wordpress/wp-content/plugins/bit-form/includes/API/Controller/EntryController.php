<?php

namespace BitCode\BitForm\API\Controller;

use BitCode\BitForm\Core\Database\FormModel;
use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;

class EntryController extends WP_REST_Controller
{
  protected static $form;
  protected $formModel;
  protected $form_id;

  public function __construct()
  {
    $this->formModel = new FormModel();
  }

  public function googleAuth()
  {
    $state = $_GET['state'];
    $code = urlencode($_GET['code']);
    // echo $code;
    if (wp_redirect($state . '&code=' . $code, 302)) {
      exit;
    }
  }

  public function oneDriveAuth()
  {
    $state = $_GET['state'];
    $code = urlencode($_GET['code']);
    // echo $code;
    if (wp_redirect($state . '&code=' . $code, 302)) {
      exit;
    }
  }

  public function authRedirect(WP_REST_Request $request)
  {
    $state = $request->get_param('state');
    $parsed_url = wp_parse_url(get_site_url());
    $site_url = $parsed_url['scheme'] . '://' . $parsed_url['host'];
    $site_url .= empty($parsed_url['port']) ? null : ':' . $parsed_url['port'];
    if (false === strpos($state, $site_url)) {
      return new WP_Error('404');
    }
    $params = $request->get_params();
    unset($params['rest_route'], $params['state']);
    if (wp_redirect($state . '&' . http_build_query($params), 302)) {
      exit;
    }
  }
}
