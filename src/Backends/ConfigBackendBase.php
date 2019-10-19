<?php

namespace CashExpress\ConfigSync\Backends;

use Illuminate\Console\Command;
use CashExpress\ConfigSync\Connections\ConnectionBase;
use CashExpress\ConfigSync\Connections\ConnectionInterface;
use CashExpress\ConfigSync\Environments\ConfigEnvironmentInterface;
use BeyondCode\Credentials\Credentials;

abstract class ConfigBackendBase implements ConfigBackendInterface
{
  abstract public function sync(ConfigEnvironmentInterface $configEnvironment, ConnectionInterface $connection);

  public function encryptAndStore(array $data, string $filePath) {
    resolve("BeyondCode\Credentials\Credentials")->store($data, $filePath);  
  }
}