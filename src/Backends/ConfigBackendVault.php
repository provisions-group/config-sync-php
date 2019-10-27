<?php

namespace CashExpress\ConfigSync\Backends;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use BeyondCode\Credentials\Credentials;
use CashExpress\ConfigSync\Connections\ConnectionBase;
use CashExpress\ConfigSync\Connections\ConnectionVault;
use CashExpress\ConfigSync\Connections\ConnectionInterface;
use CashExpress\ConfigSync\Environments\ConfigEnvironmentBase;
use CashExpress\ConfigSync\Environments\ConfigEnvironmentInterface;

class ConfigBackendVault extends ConfigBackendBase
{
  public function sync(ConfigEnvironmentInterface $configEnvironment, ConnectionInterface $vaultConnection) {
    $vaultMountPath = $configEnvironment->getConfig()['mount_to_sync'];
    $vaultSecretPath = $configEnvironment->getConfig()['secret_to_sync'];

    try {
      $data = json_decode($vaultConnection->getClient()->get($vaultMountPath."/data/".$vaultSecretPath)->getBody());
    }
    catch(\Throwable $e) {
      //TODO: add throwable errors
      Log::channel("stderr")->error("Could not retrieve the data at {$vaultMountPath}/data/{$vaultSecretPath}!");
      Log::channel("stderr")->error($e->getMessage());
      return false;
    }
    
    $dataToStore = json_decode(json_encode($data->data->data), true);

    $filePath = $configEnvironment->getConfig()['config_file_path'];

    try {
      $this->encryptAndStore($dataToStore, $filePath);
    }
    catch(\Throwable $e) {
      //TODO: add throwable errors
      Log::channel("stderr")->error("Could not write the data to the safe at {$filePath}!");
      Log::channel("stderr")->error($e->getMessage());
      return false;
    }
    return true;
  }
}