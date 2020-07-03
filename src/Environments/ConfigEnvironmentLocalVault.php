<?php

namespace ProvisionsGroup\ConfigSync\Environments;

use Illuminate\Console\Command;
use ProvisionsGroup\ConfigSync\Connections\ConnectionVault;

class ConfigEnvironmentLocalVault extends ConfigEnvironmentBase
{
  private $environment;
  private $config;

  public function __construct($environment) {
    $this->environment = $environment;
    $this->config = config('config-sync.environments.' . $environment);
  }

  public function getEnvironmentConnection($credentials) : ConnectionVault {
    $connectionVault = new ConnectionVault($this->config['base_uri']);
    $connectionVault->connectByToken($credentials['token'], $this->config['auth_path']);
    return $connectionVault;
  }

  public function getConfig() {
    return $this->config;
  }

}