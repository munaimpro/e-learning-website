<?php

namespace BitCode\BitForm\API\Route;

use BitCode\BitForm\API\Controller\EntryController;
use WP_REST_Controller;
use WP_REST_Server;

class Routes extends WP_REST_Controller
{
  private $entryController;

  protected $namespace;

  protected $rest_base;

  public function __construct()
  {
    $this->namespace = 'bitform';
    $this->rest_base = 'v1';
    $this->entryController = new EntryController();
  }

  public function register_routes()
  {
    /* google sheet route */
    register_rest_route(
      $this->namespace,
      $this->rest_base . '/google/',
      [
        [
          'methods'             => WP_REST_Server::READABLE,
          'callback'            => [$this->entryController, 'googleAuth'],
          'permission_callback' => '__return_true'
        ]

      ]
    );
    // oneDrive rest route
    register_rest_route(
      $this->namespace,
      $this->rest_base . '/oneDrive/',
      [
        [
          'methods'             => WP_REST_Server::READABLE,
          'callback'            => [$this->entryController, 'oneDriveAuth'],
          'permission_callback' => '__return_true'
        ]

      ]
    );

    // zoho
    register_rest_route(
      $this->namespace,
      $this->rest_base . '/zoho/',
      [
        [
          'methods'             => WP_REST_Server::READABLE,
          'callback'            => [$this->entryController, 'authRedirect'],
          'permission_callback' => '__return_true'
        ]

      ]
    );
  }
}
