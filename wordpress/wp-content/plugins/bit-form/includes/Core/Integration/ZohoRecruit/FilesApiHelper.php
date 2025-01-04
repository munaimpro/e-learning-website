<?php

/**
 * ZohoRecruit Files Api
 *
 */

namespace BitCode\BitForm\Core\Integration\ZohoRecruit;

use BitCode\BitForm\Core\Util\HttpHelper;

/**
 * Provide functionality for Upload files
 */
final class FilesApiHelper
{
  private $_defaultHeader;
  private $_apiDomain;
  private $_payloadBoundary;
  private $_basepath;
  private $_module;

  private $_dataCenter;

  /**
   * @param string  $module       zoho recruit module dname
   * @param object  $tokenDetails Api token details
   * @param integer $formID       ID of the form, for which integration is executing
   * @param integer $entryID      Current submission ID
   */
  public function __construct($module, $dataCenter, $tokenDetails, $formID, $entryID)
  {
    $this->_module = $module;
    $this->_dataCenter = $dataCenter;
    $this->_payloadBoundary = wp_generate_password(24);
    $this->_defaultHeader['Authorization'] = "Zoho-oauthtoken {$tokenDetails->access_token}";
    $this->_defaultHeader['content-type'] = 'multipart/form; boundary=' . $this->_payloadBoundary;
    $this->_apiDomain = \urldecode($tokenDetails->api_domain);
    $this->_basepath = BITFORMS_UPLOAD_DIR . DIRECTORY_SEPARATOR . $formID . DIRECTORY_SEPARATOR . $entryID . DIRECTORY_SEPARATOR;
  }

  /**
   * Helps to execute upload files api
   *
   * @param mixed  $files     Files path
   * @param mixed  $recordID  Record id
   * @param string $zohoField zoho recruit upload fieldname
   *
   * @return array $uploadedFiles ID's of uploaded file in Zoho Recruit
   */
  public function uploadFiles($files, $recordID, $zohoField)
  {
    $uploadFileEndpoint = '';

    if ('Candidate Photo' === $zohoField) {
      $uploadFileEndpoint = "https://recruit.zoho.{$this->_dataCenter}/recruit/private/xml/{$this->_module}/uploadPhoto?Scope=recruitapi&type={$zohoField}&version=2&id={$recordID}";
    } else {
      $uploadFileEndpoint = "https://recruit.zoho.{$this->_dataCenter}/recruit/private/json/{$this->_module}/uploadFile?Scope=ZohoRecruit.modules.all&type={$zohoField}&version=2&id={$recordID}";
    }

    $payload = '';
    if (is_array($files)) {
      foreach ($files as $fileIndex => $fileName) {
        if (file_exists("{$this->_basepath}{$fileName}")) {
          $payload .= '--' . $this->_payloadBoundary;
          $payload .= "\r\n";
          $payload .= 'Content-Disposition: form-data; name="' . 'content' .
              '"; filename="' . basename("{$this->_basepath}{$fileName}") . '"' . "\r\n";
          $payload .= "\r\n";
          $payload .= file_get_contents("{$this->_basepath}{$fileName}");
          $payload .= "\r\n";
        }
      }
    } elseif (file_exists("{$this->_basepath}{$files}")) {
      $payload .= '--' . $this->_payloadBoundary;
      $payload .= "\r\n";
      $payload .= 'Content-Disposition: form-data; name="' . 'content' .
          '"; filename="' . basename("{$this->_basepath}{$files}") . '"' . "\r\n";
      $payload .= "\r\n";
      $payload .= file_get_contents("{$this->_basepath}{$files}");
      $payload .= "\r\n";
    }
    if (empty($payload)) {
      return false;
    }
    $payload .= '--' . $this->_payloadBoundary . '--';
    return HttpHelper::post($uploadFileEndpoint, $payload, $this->_defaultHeader);
  }
}
