<?php

namespace BitCode\BitForm\Core\Util;

use BitCode\BitForm\Core\Form\FormManager;
use BitCode\BitForm\enshrined\svgSanitize\Sanitizer;

final class FileHandler
{
  public function rmrf($dir)
  {
    if (is_dir($dir)) {
      $objects = scandir($dir);
      foreach ($objects as $object) {
        if ('.' !== $object && '..' !== $object) {
          if (is_dir($dir . DIRECTORY_SEPARATOR . $object) && !is_link($dir . DIRECTORY_SEPARATOR . $object)) {
            $this->rmrf($dir . DIRECTORY_SEPARATOR . $object);
          } else {
            wp_delete_file($dir . DIRECTORY_SEPARATOR . $object);
          }
        }
      }
      rmdir($dir);
    } else {
      wp_delete_file($dir);
    }
  }

  public function cpyr($source, $destination)
  {
    if (is_dir($source)) {
      mkdir($destination);
      // chmod($destination, 0744);
      $objects = scandir($source);
      foreach ($objects as $object) {
        if ('.' !== $object && '..' !== $object) {
          if (is_dir($source . DIRECTORY_SEPARATOR . $object) && !is_link($source . DIRECTORY_SEPARATOR . $object)) {
            cpyr($source . DIRECTORY_SEPARATOR . $object, $destination . DIRECTORY_SEPARATOR . $object);
          } elseif (is_file($source . DIRECTORY_SEPARATOR . $object)) {
            copy($source . DIRECTORY_SEPARATOR . $object, $destination . DIRECTORY_SEPARATOR . $object);
            // chmod($destination. DIRECTORY_SEPARATOR .$object, 0644);
          } else {
            symlink($source . DIRECTORY_SEPARATOR . $object, $destination . DIRECTORY_SEPARATOR . $object);
          }
        }
      }
    } else {
      copy($source, $destination);
    }
  }

  public function moveUploadedFiles($file_details, $form_id, $entry_id)
  {
    $file_upoalded = [];
    $_upload_dir = BITFORMS_UPLOAD_DIR . DIRECTORY_SEPARATOR . $form_id . DIRECTORY_SEPARATOR . $entry_id;
    wp_mkdir_p($_upload_dir);
    if (is_array($file_details['name'])) {
      foreach ($file_details['name'] as $key => $value) {
        //check accepted filetype in_array($file_details['name'][$key], $supported_files) else \
        if (!empty($value)) {
          $fileNameCount = 1;
          // $file_upoalded[$key] = time()."_$value";
          $file_upoalded[$key] = sanitize_file_name($value);
          while (file_exists($_upload_dir . DIRECTORY_SEPARATOR . $file_upoalded[$key])) {
            $fileNameWithSeparator = BITFORMS_BF_SEPARATOR . $fileNameCount;
            $file_upoalded[$key] = sanitize_file_name(preg_replace('/(.[a-z A-Z 0-9]+)$/', "{$fileNameWithSeparator}$1", $value));
            $fileNameCount = $fileNameCount + 1;
            if (11 === $fileNameCount) {
              break;
            }
          }
          $move_status = \move_uploaded_file($file_details['tmp_name'][$key], $_upload_dir . DIRECTORY_SEPARATOR . $file_upoalded[$key]);
          if (!$move_status) {
            unset($file_upoalded[$key]);
          }
        }
      }
    } else {
      if (!empty($file_details['name'])) {
        $fileNameCount = 1;
        $file_upoalded[0] = sanitize_file_name($file_details['name']);
        while (file_exists($_upload_dir . DIRECTORY_SEPARATOR . $file_upoalded[0])) {
          $fileNameWithSeparator = BITFORMS_BF_SEPARATOR . $fileNameCount;
          $file_upoalded[0] = sanitize_file_name(preg_replace('/(.[a-z A-Z 0-9]+)$/', "{$fileNameWithSeparator}$1", $file_details['name']));
          $fileNameCount = $fileNameCount + 1;
          if (11 === $fileNameCount) {
            break;
          }
        }
        $move_status = \move_uploaded_file($file_details['tmp_name'], $_upload_dir . DIRECTORY_SEPARATOR . $file_upoalded[0]);
        if (!$move_status) {
          unset($file_upoalded[0]);
        }
      }
    }
    return $file_upoalded;
  }

  public function deleteFiles($form_id, $entry_id, $files)
  {
    $_upload_dir = BITFORMS_UPLOAD_DIR . DIRECTORY_SEPARATOR . $form_id . DIRECTORY_SEPARATOR . $entry_id;
    foreach ($files as $name) {
      wp_delete_file($_upload_dir . DIRECTORY_SEPARATOR . $name);
    }
  }

  public static function getFileUploadError($code)
  {
    $errors = [
      0 => __('Unknown upload error', 'bit-form'),
      1 => __('The uploaded file exceeds the upload_max_filesize directive in php.ini.', 'bit-form'),
      2 => __('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.', 'bit-form'),
      3 => __('The uploaded file was only partially uploaded.', 'bit-form'),
      4 => __('No file was uploaded.', 'bit-form'),
      6 => __('Missing a temporary folder.', 'bit-form'),
      7 => __('Failed to write file to disk.', 'bit-form'),
      8 => __('A PHP extension stopped the file upload.', 'bit-form'),
    ];
    return $errors[$code];
  }

  public static function fileCopy($tmpdir, $destinationDir, $file)
  {
    $tmpFile = $tmpdir . DIRECTORY_SEPARATOR . $file;
    $newFile = $destinationDir . DIRECTORY_SEPARATOR . $file;
    if (file_exists($tmpFile)) {
      copy($tmpFile, $newFile);
    }
  }

  public static function tempDirToUploadDir($submitted_data, $fields, $formId, $entryID)
  {
    $upload_dir = wp_upload_dir();
    $tempDir = $upload_dir['basedir'] . '/bitforms/temp';
    $destinationDir = BITFORMS_UPLOAD_DIR . DIRECTORY_SEPARATOR . $formId . DIRECTORY_SEPARATOR . $entryID . DIRECTORY_SEPARATOR;
    if (!is_dir($destinationDir)) {
      mkdir($destinationDir);
    }

    foreach ($submitted_data as $key => $data) {
      if (isset($fields[$key]) && 'advanced-file-up' === $fields[$key]['type']) {
        $files = $data;
        $fldData = $submitted_data[$key];
        $files = explode(',', $fldData);
        if (is_array($files) && count($files) > 0) {
          foreach ($files as $file) {
            self::fileCopy($tempDir, $destinationDir, trim($file));
          }
        } else {
          self::fileCopy($tempDir, $destinationDir, trim($files));
        }
        if (!empty($files)) {
          $submitted_data[$key] = $files;
        }
      }
    }
    array_map('unlink', array_filter(
      (array) array_merge(glob("$tempDir/*"))
    ));

    return $submitted_data;
  }

  private function getByteSizeByUnit($sizeString)
  {
    // split 2MB into 2 and MB
    $size = preg_replace('/[^0-9\.]/', '', $sizeString);
    $unit = preg_replace('/[^a-zA-Z]/', '', $sizeString);
    $unit = strtolower($unit);
    if ('kb' === $unit) {
      return $size * 1024;
    } elseif ('mb' === $unit) {
      return $size * 1024 * 1024;
    } elseif ('gb' === $unit) {
      return $size * 1024 * 1024 * 1024;
    } else {
      return $size;
    }
  }

  public function validation($field_key, $file_details, $form_id)
  {
    if (!function_exists('wp_check_filetype_and_ext')) {
      require_once ABSPATH . 'wp-admin/includes/file.php';
    }

    $formManager = new FormManager($form_id);
    $form_contents = $formManager->getFormContent();
    $field_content_details = $form_contents->fields;
    $fieldDetail = $field_content_details->{$field_key};
    $fieldType = $fieldDetail->typ;
    $maxSizeDetails = [];
    $allowFileTypes = [];
    $maxSize = null;
    if ('file-up' === $fieldType) {
      $allowFileTypes = !empty($fieldDetail->config->allowedFileType) ? $fieldDetail->config->allowedFileType : [];
      if (!empty($fieldDetail->config->allowMaxSize)) {
        if (!empty($fieldDetail->config->maxSize)) {
          $maxSizeDetails['maxSize'] = $fieldDetail->config->maxSize . $fieldDetail->config->sizeUnit;
        }
        if (!empty($fieldDetail->config->isItTotalMax)) {
          $maxSizeDetails['maxTotalFileSize'] = $fieldDetail->config->maxSize . $fieldDetail->config->sizeUnit;
        }
      }
      if (!empty($allowFileTypes)) {
        $allowFileTypes = explode(',', $allowFileTypes);
      }
    } elseif ('advanced-file-up' === $fieldType) {
      $allowFileTypes = !empty($fieldDetail->config->allowFileTypeValidation) ? $fieldDetail->config->acceptedFileTypes : [];
      if (!empty($fieldDetail->config->allowFileSizeValidation)) {
        if (!empty($fieldDetail->config->maxFileSize)) {
          $maxSizeDetails['maxSize'] = $fieldDetail->config->maxFileSize;
        }
        if (!empty($fieldDetail->config->maxTotalFileSize)) {
          $maxSizeDetails['maxTotalFileSize'] = $fieldDetail->config->maxTotalFileSize;
        }
      }
    }
    if (!empty($maxSizeDetails['maxSize'])) {
      $maxSize = $this->getByteSizeByUnit($maxSizeDetails['maxSize']);
    }
    $maxTotalFileSize = null;
    if (!empty($maxSizeDetails['maxTotalFileSize'])) {
      $maxTotalFileSize = $this->getByteSizeByUnit($maxSizeDetails['maxTotalFileSize']);
    }

    if ($formManager->isRepeatedField($field_key)) {
      foreach ($file_details['name'] as $rowIndex => $file) {
        if (!empty($file)) {
          $fileDetails = [
            'name'     => $file,
            'type'     => $file_details['type'][$rowIndex],
            'tmp_name' => $file_details['tmp_name'][$rowIndex],
            'error'    => $file_details['error'][$rowIndex],
            'size'     => $file_details['size'][$rowIndex],
          ];
          $validateState = $this->validateFileInfo($fieldType, $fileDetails, $allowFileTypes, $maxSize, $maxTotalFileSize);
          if (!empty($validateState) && !empty($validateState['message'])) {
            return $validateState;
          }
        }
      }
    } else {
      return $this->validateFileInfo($fieldType, $file_details, $allowFileTypes, $maxSize, $maxTotalFileSize);
    }
    return [];
  }

  private function validateFileInfo($fieldType, $file_details, $allowFileTypes, $maxSize, $maxTotalFileSize)
  {
    $errorMessage = [
      'message'   => '',
      'error_type'=> '',
    ];
    if (is_array($file_details['name'])) {
      $totalSize = 0;
      foreach ($file_details['name']  as $key => $file) {
        if (!empty($file)) {
          $fileInfo = [
            'name'     => $file,
            'type'     => $file_details['type'][$key],
            'tmp_name' => $file_details['tmp_name'][$key],
            'error'    => $file_details['error'][$key],
            'size'     => $file_details['size'][$key],
          ];
          $totalSize += $fileInfo['size'];
          $validateState = $this->validateSingleFile($fieldType, $fileInfo, $allowFileTypes, $maxSize);
          if (!empty($validateState)) {
            return $validateState;
          }
        }
      }
      if (isset($maxTotalFileSize) && !is_null($maxTotalFileSize) && $totalSize > $maxTotalFileSize) {
        $errorMessage['message'] = __('Total File size is too large', 'bit-form');
        $errorMessage['error_type'] = 'file_size_error';
        return $errorMessage;
      }
    } else {
      $validateState = $this->validateSingleFile($fieldType, $file_details, $allowFileTypes, $maxSize);
      if (!empty($validateState)) {
        return $validateState;
      }
    }

    return $errorMessage;
  }

  private function validateSingleFile($fieldType, &$file, $allowTypes, $maxSize = null)
  {
    $fileName = sanitize_file_name($file['name']);
    if (!empty($fileName)) {
      $fileSize = $file['size'];
      if (!empty($maxSize) && $fileSize > $maxSize) {
        return [
          'message'   => __('File size is too large', 'bit-form'),
          'error_type'=> 'file_size_error',
        ];
      }

      $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
      $fileExtAllowedByWp = wp_check_filetype_and_ext($file['tmp_name'], $fileName);
      $isAllowedFileType = in_array('.' . $fileExtension, $allowTypes);
      if ('advanced-file-up' === $fieldType && !empty($allowTypes)) {
        if (function_exists('mime_content_type')) {
          $fileMimeType = mime_content_type($file['tmp_name']);
        } else {
          $fileMimeType = $fileExtAllowedByWp['type'];
        }
        $isAllowedFileType = in_array($fileMimeType, $allowTypes);
      }
      if ((!empty($allowTypes) && !$isAllowedFileType) || (empty($allowTypes) && empty($fileExtAllowedByWp['ext']))) {
        return [
          'message'   => __(($fileExtension ? ".{$fileExtension}" : 'empty') . ' file extension is not allowed', 'bit-form'),
          'error_type'=> 'file_type_error',
        ];
      }
      if ('svg' === $fileExtension) {
        $svg_sanitizer = new Sanitizer();
        $dirty_svg = file_get_contents($file['tmp_name']);
        $clean_svg = $svg_sanitizer->sanitize($dirty_svg);
        if (false === $clean_svg) {
          return [
            'message'   => __('SVG file is not valid', 'bit-form'),
            'error_type'=> 'file_type_error',
          ];
        }
        file_put_contents($file['tmp_name'], $clean_svg);
      }
    }
  }

  public static function deleteIsFileExists($path)
  {
    if (file_exists($path)) {
      wp_delete_file($path);
    }
  }
}
