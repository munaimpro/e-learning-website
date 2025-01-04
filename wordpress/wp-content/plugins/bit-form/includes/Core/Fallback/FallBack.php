<?php

namespace BitCode\BitForm\Core\Fallback;

use ReflectionMethod;

final class FallBack
{
  public static function getOlderVersion()
  {
    $installed = get_option('bitforms_installed');
    $oldversion = null;
    if ($installed) {
      $oldversion = get_option('bitforms_version');
    }
    return $oldversion;
  }

  public static function add($version, $invokeable, $check = '>=')
  {
    $oldversion = self::getOlderVersion();
    if ($oldversion && version_compare($oldversion, BITFORMS_VERSION, '!=')) {
      return static::action($version, $invokeable, $oldversion, $check);
    }
  }

  public static function action($version, $invokeable, $oldversion, $check)
  {
    if (!version_compare($version, $oldversion, $check)) {
      return false;
    }
    $namespace = 'BitCode\BitForm\Core\Fallback';
    $invoke = explode('@', $invokeable);
    if (is_array($invoke) && 2 === count($invoke)) {
      $fileName = $invoke[0];
      $method = $invoke[1];
      $class = $namespace . '\\' . $fileName;
      if (class_exists($class) && method_exists($class, $method)) {
        $invokeMethod = new ReflectionMethod($class, $method);
        $invokeMethod->invoke($invokeMethod->isStatic() ? null : new $class());
      } else {
        return 'Method or class not exist';
      }
    } else {
      return 'Invalid invokeable';
    }
  }
}
