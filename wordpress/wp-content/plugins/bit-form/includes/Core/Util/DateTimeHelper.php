<?php

namespace BitCode\BitForm\Core\Util;

use DateTime;
use DateTimeZone;

final class DateTimeHelper
{
  private $_dateFormat;
  private $_timeFormat;
  private $_timezone;
  private $_currentTime;
  private $_currentFormat;

  public function __construct()
  {
    $this->_dateFormat = get_option('date_format');
    $this->_timeFormat = get_option('time_format');
    $this->_timezone = self::wp_timezone();
    $this->_currentTime = current_time('mysql');
    $this->_currentFormat = 'Y-m-d H:i:s';
  }

  public function getDate($date = null, $currentFormat = null, $currentTZ = null, $expectedFormat = null, $expectedTZ = null)
  {
    if (is_null($date)) {
      $date = $this->_currentTime;
      $currentFormat = $this->_currentFormat;
      $currentTZ = $this->_timezone;
    }

    $currentFormat = is_null($currentFormat) ? $this->_currentFormat : $currentFormat;
    $currentTZ = is_null($currentTZ) ? $this->_timezone : $currentTZ;
    $expectedFormat = is_null($expectedFormat) ? $this->_dateFormat : $expectedFormat;
    $expectedTZ = is_null($expectedTZ) ? $this->_timezone : $expectedTZ;

    return $this->getFormated($date, $currentFormat, $currentTZ, $expectedFormat, $expectedTZ);
  }

  public function getTime($date = null, $currentFormat = null, $currentTZ = null, $expectedFormat = null, $expectedTZ = null)
  {
    if (is_null($date)) {
      $date = $this->_currentTime;
      $currentFormat = $this->_currentFormat;
      $currentTZ = $this->_timezone;
    }

    $currentFormat = is_null($currentFormat) ? $this->_currentFormat : $currentFormat;
    $currentTZ = is_null($currentTZ) ? $this->_timezone : $currentTZ;
    $expectedFormat = is_null($expectedFormat) ? $this->_timeFormat : $expectedFormat;
    $expectedTZ = is_null($expectedTZ) ? $this->_timezone : $expectedTZ;

    return $this->getFormated($date, $currentFormat, $currentTZ, $expectedFormat, $expectedTZ);
  }

  public function getDay($nameType, $date = null, $currentFormat = null, $currentTZ = null, $expectedTZ = null)
  {
    if (is_null($date)) {
      $date = $this->_currentTime;
      $currentFormat = $this->_currentFormat;
      $currentTZ = $this->_timezone;
    }

    $currentFormat = is_null($currentFormat) ? $this->_currentFormat : $currentFormat;
    $currentTZ = is_null($currentTZ) ? $this->_timezone : $currentTZ;
    $expectedTZ = is_null($expectedTZ) ? $this->_timezone : $expectedTZ;

    switch ($nameType) {
      case 'numeric-with-leading':
        $expectedFormat = 'd';
        break;

      case 'numeric-without-leading':
        $expectedFormat = 'j';
        break;

      case 'short-name':
        $expectedFormat = 'D';
        break;

      case 'full-name':
        $expectedFormat = 'l';
        break;

      default:
        $expectedFormat = 'd';
        break;
    }

    return $this->getFormated($date, $currentFormat, $currentTZ, $expectedFormat, $expectedTZ);
  }

  public function getMonth($nameType, $date = null, $currentFormat = null, $currentTZ = null, $expectedTZ = null)
  {
    if (is_null($date)) {
      $date = $this->_currentTime;
      $currentFormat = $this->_currentFormat;
      $currentTZ = $this->_timezone;
    }

    $currentFormat = is_null($currentFormat) ? $this->_currentFormat : $currentFormat;
    $currentTZ = is_null($currentTZ) ? $this->_timezone : $currentTZ;
    $expectedTZ = is_null($expectedTZ) ? $this->_timezone : $expectedTZ;

    switch ($nameType) {
      case 'numeric-with-leading':
        $expectedFormat = 'm';
        break;

      case 'numeric-without-leading':
        $expectedFormat = 'n';
        break;

      case 'short-name':
        $expectedFormat = 'M';
        break;

      case 'full-name':
        $expectedFormat = 'F';
        break;

      default:
        $expectedFormat = 'd';
        break;
    }

    return $this->getFormated($date, $currentFormat, $currentTZ, $expectedFormat, $expectedTZ);
  }

  public function getFormated($dateString, $currentFormat, $currentTZ, $expectedFormat, $expectedTZ)
  {
    if (false === $currentFormat) {
      $dateObject = new DateTime($dateString, $currentTZ);
    } else {
      $dateObject = DateTime::createFromFormat($currentFormat, $dateString, $currentTZ);
    }

    if (!is_null($expectedTZ)) {
      $dateObject->setTimezone($expectedTZ);
    }

    if ($dateObject) {
      return $dateObject->format($expectedFormat);
    }
    return false;
  }

  public function getUnicodeLikeFormat($type, $format = null)
  {
    $type = strtolower($type);
    switch ($type) {
      case 'date':
        $format = is_null($format) ? $this->_dateFormat : $format;
        break;

      case 'time':
        $format = is_null($format) ? $this->_timeFormat : $format;
        break;

      case 'timestamp':
        $format = is_null($format) ? $this->_currentFormat : $format;
        break;

      default:
        break;
    }

    if (false !== strpos($format, 'd')) {
      $format = str_replace('d', 'dd', $format);
    }
    if (false !== strpos($format, 'j')) {
      $format = str_replace('j', 'd', $format);
    }
    if (false !== strpos($format, 'D')) {
      $format = str_replace('D', 'eee', $format);
    }
    if (false !== strpos($format, 'I')) {
      $format = str_replace('I', 'eeee', $format);
    }
    if (false !== strpos($format, 'S')) {
      $format = str_replace('S', 'F', $format);
    }
    if (false !== strpos($format, 'M')) {
      $format = str_replace('M', 'MMM', $format);
    }
    if (false !== strpos($format, 'F')) {
      $format = str_replace('F', 'MMMM', $format);
    }
    if (false !== strpos($format, 'm')) {
      $format = str_replace('m', 'MM', $format);
    }
    if (false !== strpos($format, 'n')) {
      $format = str_replace('n', 'M', $format);
    }
    if (false !== strpos($format, 'y')) {
      $format = str_replace('y', 'yy', $format);
    }
    if (false !== strpos($format, 'Y')) {
      $format = str_replace('Y', 'yyyy', $format);
    }
    if (false !== strpos($format, 'a')) {
      $format = str_replace('a', 'aaaa', $format);
    }
    if (false !== strpos($format, 'A')) {
      $format = str_replace('A', 'aaaa', $format);
    }
    if (false !== strpos($format, 'g')) {
      $format = str_replace('g', 'h', $format);
    }
    if (false !== strpos($format, 'G')) {
      $format = str_replace('G', 'H', $format);
    }
    if (false !== strpos($format, 'h')) {
      $format = str_replace('h', 'hh', $format);
    }
    if (false !== strpos($format, 'H')) {
      $format = str_replace('H', 'HH', $format);
    }
    if (false !== strpos($format, 'i')) {
      $format = str_replace('i', 'mm', $format);
    }
    if (false !== strpos($format, 's')) {
      $format = str_replace('s', 'ss', $format);
    }
    return $format;
  }

  public function getUnicodeToPhpFormat($type, $format = null)
  {
    $type = strtolower($type);
    switch ($type) {
      case 'date':
        $format = is_null($format) ? $this->_dateFormat : $format;
        break;

      case 'time':
        $format = is_null($format) ? $this->_timeFormat : $format;
        break;

      case 'timestamp':
        $format = is_null($format) ? $this->_currentFormat : $format;
        break;
      default:
        break;
    }

    if (false !== strpos($format, 'd')) {
      $format = str_replace('dd', 'd', $format);
    }
    if (false !== strpos($format, 'E')) {
      $format = str_replace('E', 'D', $format);
    }
    if (false !== strpos($format, 'MMMM')) {
      $format = str_replace('MMMM', 'F', $format);
    } elseif (false !== strpos($format, 'MMM')) {
      $format = str_replace('MMM', 'M', $format);
    } elseif (false !== strpos($format, 'MM')) {
      $format = str_replace('MM', 'm', $format);
    }
    if (false !== strpos($format, 'yyyy')) {
      $format = str_replace('yyyy', 'Y', $format);
    } elseif (false !== strpos($format, 'yy')) {
      $format = str_replace('yy', 'y', $format);
    }
    return $format;
  }

  public static function wp_timezone_string()
  {
    if (\function_exists('wp_timezone_string')) {
      return wp_timezone_string();
    }

    $timezone_string = get_option('timezone_string');

    if ($timezone_string) {
      return $timezone_string;
    }

    $offset = (float) get_option('gmt_offset');
    $hours = (int) $offset;
    $minutes = ($offset - $hours);

    $sign = ($offset < 0) ? '-' : '+';
    $abs_hour = abs($hours);
    $abs_mins = abs($minutes * 60);
    $tz_offset = sprintf('%s%02d:%02d', $sign, $abs_hour, $abs_mins);

    return $tz_offset;
  }

  public static function wp_timezone()
  {
    if (\function_exists('wp_timezone')) {
      return wp_timezone();
    }
    return new DateTimeZone(self::wp_timezone_string());
  }
}
