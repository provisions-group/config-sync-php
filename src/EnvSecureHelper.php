<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;
use Illuminate\Encryption\Encrypter;
use BeyondCode\Credentials\Credentials;

function env_secure($key, $default = null) {
  if (Str::startsWith($encryptionKey = env('APP_KEY'), 'base64:')) {
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