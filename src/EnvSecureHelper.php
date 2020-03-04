<?php

use Illuminate\Encryption\Encrypter;
use BeyondCode\Credentials\Credentials;

function env_secure($key, $default = null) {
  if(env('APP_KEY') == null || env('APP_KEY') == "") {
    return env($key, $default);
  }

  $encryptionKey = env('APP_KEY');
  if (strpos($encryptionKey, 'base64:') === 0) {
     $encryptionKey = base64_decode(substr($encryptionKey, 7));
   }
  $encrypter = new Encrypter($encryptionKey, 'AES-256-CBC');

  $credentials = new Credentials($encrypter); 
  $credentials->load(env('APP_CONFIG_FILE','./config.safe.env'));

  $value = $credentials->get($key);
  if($value == null) {
    return env($key, $default);
  }
  else {
    return $credentials->get($key);
  }
}