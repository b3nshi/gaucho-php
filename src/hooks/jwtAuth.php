<?php
# JWT Library to generate token
use \Firebase\JWT\JWT;

class JwtAuth {
  private static $jwtKey = 'e07cf913fc8d9464dda03555b690ed38';
  private static $jwtEncrypt = ['HS256'];

  public static function jwtGetToken($id, $email) {
    $time = time();
    $token = array(
        # 'iss' => 'http://cert.fluentiq.com', # issuer
        'aud' => self::jwtAud(), # audience
        'sub' => '', # subject
        'iat' => $time, # start token time
        'exp' => time() + (60 * 60), # expiration time: 1 hour after
        'data' => [
          'id' => $id,
          'email' => $email,
        ]
    );

    return JWT::encode($token, self::$jwtKey);
  }

  public static function jwtCheck($token) {
    if (empty($token)) {
      # throw new Exception('Invalid token supplied');
      return false;
    }

    $decode = JWT::decode(
      $token,
      self::$jwtKey,
      self::$jwtEncrypt
    );

    return ($decode->aud === self::jwtAud());
  }

  public static function jwtGetData($token) {
    return (array) JWT::decode($token, self::$jwtKey, self::$jwtEncrypt)->data;
  }

  private static function jwtAud() {
    $aud = '';

    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $aud = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
      $aud = $_SERVER['REMOTE_ADDR'];
    }

    $aud .= @$_SERVER['HTTP_USER_AGENT'];
    $aud .= gethostname();

    return sha1($aud);
  }
}
