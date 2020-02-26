<?php

namespace ProvisionsGroup\ConfigSync\Backends;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use BeyondCode\Credentials\Credentials;
use ProvisionsGroup\ConfigSync\Connections\ConnectionBase;
use ProvisionsGroup\ConfigSync\Connections\ConnectionInterface;
use ProvisionsGroup\ConfigSync\Environments\ConfigEnvironmentInterface;

abstract class ConfigBackendBase implements ConfigBackendInterface
{
  abstract public function sync(ConfigEnvironmentInterface $configEnvironment, ConnectionInterface $connection);

  public function encryptAndStore(array $data, string $filePath) {
    resolve("BeyondCode\Credentials\Credentials")->store($data, $filePath); 
    
    Log::channel("stderr")->info("Stored the following environment variables in {$filePath}...");
    foreach ($data as $key => $value) {
      Log::channel("stderr")->info($key);
    }
  }
}