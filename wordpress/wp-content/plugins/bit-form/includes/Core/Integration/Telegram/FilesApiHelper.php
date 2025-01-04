<?php

/**
 * Telegram Files Api
 *
 */

namespace BitCode\BitForm\Core\Integration\Telegram;

/**
 * Provide functionality for Upload files
 */
final class FilesApiHelper
{
  private $_defaultHeader;
  private $_payloadBoundary;
  private $_basepath;

  /**
   *
   * @param Integer $formID  ID of the form, for which integration is executing
   * @param Integer $entryID Current submission ID
   */
  public function __construct($formID, $entryID)
  {
    $this->_payloadBoundary = wp_generate_password(24);
    $this->_defaultHeader['Content-Type'] = 'multipart/form-data; boundary=' . $this->_payloadBoundary;
    $this->_basepath = BITFORMS_UPLOAD_DIR . DIRECTORY_SEPARATOR . $formID . DIRECTORY_SEPARATOR . $entryID . DIRECTORY_SEPARATOR;
  }

  /**
   * Helps to execute upload files api
   *
   * @param string $apiEndPoint Telegram API base URL
   * @param array  $data        Data to pass to API
   *
   * @return array $uploadResponse Telegram API response
   */
  public function uploadFiles($apiEndPoint, $data)
  {
    $filename = $this->getFileNameWithExtension($data['photo']) ?? $data['photo'];
    $filePath = "{$this->_basepath}{$filename}";
    $mimeType = mime_content_type($filePath);
    $fileType = \explode('/', $mimeType);

    switch ($fileType[0]) {
      case 'image':
        $apiMethod = '/sendPhoto';
        $param = 'photo';
        break;

      case 'audio':
        $apiMethod = '/sendAudio';
        $param = 'audio';
        break;
      case 'video':
        $apiMethod = '/sendVideo';
        $param = 'video';
        break;

      default:
        $apiMethod = '/sendDocument';
        $param = 'document';
        break;
    }
    $uploadFileEndpoint = $apiEndPoint . $apiMethod;

    $data[$param] = new \CURLFILE($filePath);
    if ('photo' !== $param) {
      unset($data['photo']);
    }
    $curl = curl_init();
    curl_setopt_array(
      $curl,
      [
        CURLOPT_URL            => $uploadFileEndpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING       => '',
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => 'POST',
        CURLOPT_POSTFIELDS     => $data,
      ]
    );

    $uploadResponse = curl_exec($curl);

    curl_close($curl);
    return $uploadResponse;
  }

  public function uploadMultipleFiles($apiEndPoint, $data)
  {
    $param = 'media';
    $uploadMultipleFileEndpoint = $apiEndPoint . '/sendMediaGroup';
    $postFields = [
      'chat_id' => $data['chat_id'],
      'caption' => $data['caption']
    ];

    foreach ($data['media'] as $key => $value) {
      $filename = $this->getFileNameWithExtension($value) ?? $value;
      $filePath = "{$this->_basepath}{$filename}";
      $mimeType = mime_content_type($filePath);
      $fileType = \explode('/', $mimeType);
      unset($data['media'][$key]);

      if ('image' === $fileType[0]) {
        $type = 'photo';
      } elseif ('application' === $fileType[0] || 'text' === $fileType[0]) {
        $type = 'document';
      } elseif ('application' === $fileType[0]) {
        $type = 'document';
      } else {
        $type = $fileType[0];
      }

      $media[] = [
        'type'       => $type,
        'media'      => "attach://{$key}.path",
        'caption'    => $data['caption'],
        'parse_mode' => 'HTML'
      ];
      $nameK = "{$key}.path";
      $postFields[$nameK] = new \CURLFILE($filePath);
    }
    $postFields['media'] = wp_json_encode($media);

    if ('media' !== $param) {
      unset($data['media']);
    }

    $curl = curl_init();
    curl_setopt_array(
      $curl,
      [
        CURLOPT_URL            => $uploadMultipleFileEndpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING       => '',
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => 'POST',
        CURLOPT_POSTFIELDS     => $postFields,
        CURLOPT_HTTPHEADER     => [
          'Content-Type: multipart/form-data'
        ],
      ]
    );

    $uploadResponse = curl_exec($curl);
    curl_close($curl);
    return $uploadResponse;
  }

  private function getFileNameWithExtension($url)
  {
    $fileName = basename($url);
    return false !== strpos($fileName, '.') ? $fileName : null;
  }
}
