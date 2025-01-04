<?php

namespace BitCode\BitForm\Core\Util;

final class Log
{
  public static function get_backtrace_info()
  {
    $trace = debug_backtrace();

    $file = $trace[1]['file'];
    $class = $trace[2]['class'];
    $func = $trace[2]['function'];
    $type = $trace[2]['type'];
    $line = $trace[1]['line'];

    $output = sprintf(
      '%s%s%s() at %s on line %s',
      $class,
      $type,
      $func,
      $file,
      $line
    );

    return $output;
  }

  public static function debug_log($log)
  {
    $trace_info = self::get_backtrace_info();
    if (WP_DEBUG === true) {
      if (is_array($log) || is_object($log)) {
        if (is_array($log)) {
          $log['trace'] = $trace_info;
        } else {
          $log->trace = $trace_info;
        }
        $log_msg = print_r($log, true);
      } else {
        $log_msg = $log . ' in ' . $trace_info;
      }

      error_log($log_msg);
    }
  }

  public static function print($log, $path = '', $fileName = 'print.log', $fileOpenMode = 'a')
  {
    $rootDir = BITFORMS_CONTENT_DIR;
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
    fwrite($file, $log);
    fclose($file);
    return true;
  }
}
