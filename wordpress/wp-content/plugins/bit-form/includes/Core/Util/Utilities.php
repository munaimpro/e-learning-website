<?php

namespace BitCode\BitForm\Core\Util;

use BitCode\BitForm\Admin\Form\Helpers;

final class Utilities
{
  public static function isPro()
  {
    $integrateData = get_option('bitformpro_integrate_key_data');
    $isProLicenseActivated = !empty($integrateData) && is_array($integrateData) && 'success' === $integrateData['status'];
    return class_exists('BitCode\\BitFormPro\\Plugin') && $isProLicenseActivated;
  }

  public static function getRealPath($dir, $name)
  {
    if (!isset($dir)) {
      return false;
    }
    $directories = array_diff(scandir($dir), ['..', '.']);
    $name = strtolower($name);

    foreach ($directories as $directory) {
      $dirLower = strtolower($directory);
      if ($dirLower === $name) {
        return $directory;
      }
    }

    return false;
  }

  public static function getFileNameExceptExtension($filename)
  {
    $path_parts = pathinfo($filename);
    return $path_parts['filename'];
  }

  public static function styleGenerator($styleObj, $important = false)
  {
    if (is_array($styleObj)) {
      $styleObj = json_decode(wp_json_encode($styleObj));
    }
    $important = $important ? '!important' : '';
    $classes = array_keys((array) $styleObj);
    $css = '';
    foreach ($classes as $class) {
      $css .= $class;
      $props = (array) $styleObj->{$class};
      $propsKeys = array_keys($props);
      $styleObject = '{';
      foreach ($propsKeys as $property) {
        $value = $props[$property];
        if (empty($value)) {
          continue;
        }
        if (is_object($value)) {
          continue;
        }
        $styleObject .= "{$property}:{$value}{$important};";
      }
      $styleObject = rtrim($styleObject, ';');
      $styleObject .= '}';
      $css .= $styleObject;
    }

    return $css;
  }

  public static function convertToCSS(array $styles): string
  {
    $css = '';

    foreach ($styles as $selector => $properties) {
      $css .= "$selector{";

      // Check if the properties are an array, meaning it's a nested rule (e.g., media queries, keyframes)
      if (is_array($properties)) {
        // If it's a nested array (media query or keyframe), recursively handle it
        foreach ($properties as $property => $value) {
          if (is_array($value)) {
            // Recursively process nested arrays (media queries, keyframes, etc.)
            $css .= self::convertToCSS([$property => $value]);
          } else {
            // Regular CSS property
            $css .= "$property:$value;";
          }
        }
      }

      $css .= '}';
    }

    return $css;
  }

  public static function appendCSS($formId, $css, $mode = 'a')
  {
    $fileName = '';
    $path = '';
    $path = 'form-styles';
    $fileName = "bitform-{$formId}.css";
    Helpers::saveFile($path, $fileName, $css, $mode);
    $fileName = "bitform-{$formId}-formid.css";
    Helpers::saveFile($path, $fileName, $css, $mode);
  }

  public static function duplicateDbTable($oldTableName, $newTableName)
  {
    global $wpdb;
    $oldTable = "{$wpdb->prefix}bitforms_{$oldTableName}";
    $newTable = "{$wpdb->prefix}bitforms_{$newTableName}";
    $wpdb->query(
      $wpdb->prepare(
        "CREATE TABLE IF NOT EXISTS $newTable LIKE $oldTable"
      )
    );
    $wpdb->query(
      $wpdb->prepare(
        "INSERT $newTable SELECT * FROM $oldTable"
      )
    );
  }
}
