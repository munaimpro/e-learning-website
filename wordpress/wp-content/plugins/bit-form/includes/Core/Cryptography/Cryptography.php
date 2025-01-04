<?php

namespace BitCode\BitForm\Core\Cryptography;

class Cryptography
{
  public static $sodiumCompat;

  public static function getSodiumCompat()
  {
    if (!self::$sodiumCompat) {
      self::$sodiumCompat = new SodiumCompat();
    }
    return self::$sodiumCompat;
  }

  public static function encrypt($message, $key)
  {
    // check $key length and slice if key is longer than 32 bytes
    if (strlen($key) > 32) {
      $key = substr($key, 0, 32);
    }
    return base64_encode(self::getSodiumCompat()->compatEncrypt($message, $key));
  }

  public static function decrypt($message, $key)
  {
    if (strlen($key) > 32) {
      $key = substr($key, 0, 32);
    }
    return self::getSodiumCompat()->compatDecrypt(base64_decode($message), $key);
  }
}
