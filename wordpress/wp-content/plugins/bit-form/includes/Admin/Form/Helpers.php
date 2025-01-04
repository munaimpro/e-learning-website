<?php

namespace BitCode\BitForm\Admin\Form;

use BitCode\BitForm\Core\Cryptography\Cryptography;
use BitCode\BitForm\Core\Database\FormEntryModel;
use BitCode\BitForm\Core\Util\Log;
use Exception;

class Helpers
{
  public static $file_upload_types = ['file-up', 'advanced-file-up'];

  public static $repeated_array_type_data_fields = ['check', 'image-select'];

  public static function filterNullEntries($entries)
  {
    $filteredEntries = [];
    foreach ($entries as $entry) {
      foreach ($entry as $key => $value) {
        if (is_null($value)) {
          unset($entry->$key);
        }
      }
      if (count((array) $entry)) {
        $filteredEntries[] = $entry;
      }
    }
    return $filteredEntries;
  }

  public static function scriptLoader($src, $id, $instanceObj = null, $selector = '', $attrs = [], $integrity = null, $contentId = '')
  {
    $attributes = wp_json_encode($attrs);
    $instObj = '';
    if ($instanceObj) {
      $instObj .= <<<INST
script.onload = function () {
  bfSelect('#{$contentId}').querySelectorAll('{$selector}').forEach(function(fld){
    $instanceObj;
  });
}
INST;
    }
    return <<<LOAD_SECRIPT
var script =  document.createElement('script'), integrity = '$integrity', attrs = $attributes, id = '$id';
script.src = '$src';
script.id = id;
if(integrity){
  script.integrity = integrity;
  script.crossOrigin = 'anonymous';
}
if(attrs){
  Object.entries(attrs).forEach(function([key, val]){
    script.setAttribute(key,val);
  })
}
$instObj;
var bodyElm = document.body;
var alreadyExistScriptElm = bodyElm ? bodyElm.querySelector('script#$id'):null;
if(alreadyExistScriptElm){
  bodyElm.removeChild(alreadyExistScriptElm)
}
if(!(window.recaptcha && id === 'g-recaptcha-script')){
  bodyElm.appendChild(script);
}
LOAD_SECRIPT;
  }

  public static function minifyJs($input)
  {
    if ('' === trim($input)) {
      return $input;
    }
    return preg_replace(
      [
        '/ {2,}/',
        '/\s*=\s*/',
        '/\s*,\s*/',
        '/\s+(?=\(|\{|\:|\?)|\t|(?:\r?\n[ \t]*)+/s'
      ],
      [' ', '=', ',', ''],
      $input
    );
  }

  /**
   * @method name : saveFile
   * @description : save js/css field to disk
   * @param  : $path => like(dirName/css), $fileName => main.css, $script
   * @return : boolean
   */
  public static function saveFile($path, $fileName, $script, $fileOpenMode = 'a')
  {
    try {
      $rootDir = BITFORMS_CONTENT_DIR . DIRECTORY_SEPARATOR;
      $path = trim($path, '/');
      $pathArr = explode('/', $path); // like "fieldname/user => [Fieldname, user]
      foreach ($pathArr as $d) {
        $rootDir .= $d . DIRECTORY_SEPARATOR;
        if (!realpath($rootDir)) {
          mkdir($rootDir);
        }
      }
      $fullPath = $rootDir . $fileName;
      $file = fopen($fullPath, $fileOpenMode);
      if (false === $file) {
        throw new Exception("Failed to open file: $fullPath");
      }
      if (false === fwrite($file, $script)) {
        throw new Exception("Failed to write to file: $fullPath");
      }
      if (false === fclose($file)) {
        throw new Exception("Failed to close file: $fullPath");
      }
      return true;
    } catch (\Exception $e) {
      Log::debug_log($e->getMessage());
      return false;
    }
  }

  /**
   * @method name : generatePathDirOrFile
   * @dscription : generate path for js/css file
   * @params : $path => like(dirName/css)
   * @return : a string of full path
   */
  public static function generatePathDirOrFile($path)
  {
    $rootDir = BITFORMS_CONTENT_DIR . DIRECTORY_SEPARATOR;
    $path = trim($path, '/');
    $pathArr = explode('/', $path); // like "fieldname/user => [Fieldname, user]
    foreach ($pathArr as $d) {
      $rootDir .= $d . DIRECTORY_SEPARATOR;
    }
    return rtrim($rootDir, DIRECTORY_SEPARATOR);
  }

  public static function fileRead($filePath)
  {
    $fileContent = '';
    if (file_exists($filePath)) {
      $file = fopen($filePath, 'r');
      $fileContent .= fread($file, filesize($filePath));
      fclose($file);
    }
    return $fileContent;
  }

  public static function getDataFromNestedPath($data, $key)
  {
    $keys = explode('->', $key);
    $lastKey = array_pop($keys);
    $dataType = is_array($data) ? 'array' : (is_object($data) ? 'object' : '');
    if ('array' === $dataType) {
      return self::accessFromArray($data, $keys, $lastKey);
    }
    if ('object' === $dataType) {
      return self::accessFromObject($data, $keys, $lastKey);
    }
  }

  private static function accessFromObject($data, $keys, $lastKey)
  {
    foreach ($keys as $k) {
      if (!property_exists($data, $k)) {
        return null;
      }
      $data = $data->$k;
    }
    return isset($data->$lastKey) ? $data->$lastKey : null;
  }

  private static function accessFromArray($data, $keys, $lastKey)
  {
    foreach ($keys as $k) {
      if (!array_key_exists($k, $data)) {
        return null;
      }
      $data = $data[$k];
    }
    return isset($data[$lastKey]) ? $data[$lastKey] : null;
  }

  public static function setDataToNestedPath($data, $key, $value)
  {
    $keys = explode('->', $key);
    $lastKey = array_pop($keys);
    foreach ($keys as $k) {
      if (!array_key_exists($k, $data)) {
        $data->$k = (object) [];
      }
      $data = $data->$k;
    }
    $data->$lastKey = json_decode(wp_json_encode($value));
    ;
    return $data;
  }

  public static function property_exists_nested($obj, $path = '', $valToCheck = null, $checkNegativeVal = 0)
  {
    $path = explode('->', $path);
    $current = $obj;
    foreach ($path as $key) {
      if (is_object($current)) {
        if (property_exists($current, $key)) {
          $current = $current->{$key};
        } else {
          return false;
        }
      } else {
        return false;
      }
    }
    if (isset($valToCheck)) {
      if ($checkNegativeVal) {
        return $current !== $valToCheck;
      }
      return $current === $valToCheck;
    }
    return true;
  }

  public static function validateEntryTokenAndUser($entryToken, $entryId)
  {
    // check if the user is logged in
    if (is_user_logged_in()) {
      $user = wp_get_current_user();
      if (in_array('administrator', $user->roles) || current_user_can('manage_bitform')) {
        return true;
      }
      $entryModel = new FormEntryModel();
      $entry = $entryModel->get(
        'id, user_id, form_id',
        [
          'id'      => $entryId,
          'user_id' => $user->ID
        ]
      );
      if (!is_wp_error($entry) && !empty($entry)) {
        return true;
      }
    }
    // check if the entry token is valid
    if (isset($entryToken) && $entryToken) {
      $decryptEntryId = Cryptography::decrypt($entryToken, AUTH_SALT);
      if ($decryptEntryId === $entryId) {
        return true;
      }
    }

    return false;
  }

  public static function validateFormEntryEditPermission($formId, $entryId)
  {
    if (is_user_logged_in()) {
      if (current_user_can('manage_bitform') || current_user_can('bitform_entry_edit') || current_user_can('edit_posts')) {
        return true;
      }
    }
    return false;
  }

  public static function honeypotEncryptedToken($str)
  {
    $token = base64_encode(base64_encode($str));
    return $token;
  }

  public static function csrfEecrypted()
  {
    $secretKey = get_option('bf_csrf_secret');
    if (!$secretKey) {
      $secretKey = 'bf-' . time();
      update_option('bf_csrf_secret', $secretKey);
    }
    $tIdenty = base64_encode(random_bytes(32));
    $csrf = \base64_encode(\hash_hmac('sha256', $tIdenty, $secretKey, true));
    return ['csrf' => $csrf, 't_identity' => $tIdenty];
  }

  public static function csrfDecrypted($identy, $token)
  {
    $secretKey = get_option('bf_csrf_secret');
    return \hash_equals(
      \base64_encode(\hash_hmac('sha256', $identy, $secretKey, true)),
      $token
    );
  }

  public static function checkIsIntArr($arr)
  {
    $filteredArray = array_filter($arr, 'is_numeric');
    $intArray = array_map('intval', $filteredArray);
    $result = count($arr) === count($intArray);

    return $result;
  }
}
