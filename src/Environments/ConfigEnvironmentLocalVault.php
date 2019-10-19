<?php

namespace CashExpress\ConfigSync\Environments;

use Illuminate\Console\Command;
use CashExpress\ConfigSync\Connections\ConnectionVault;

class ConfigEnvironmentLocalVault extends ConfigEnvironmentBase
{
  private $environment;
  private $config;

  public function __construct($environment) {
    $this->environment = $environment;
    $this->config = config('config-sync.environments.' . $environment);
  }

  public function getEnvironmentConnection() : ConnectionVault {
    $connectionVault = new ConnectionVault($this->config['base_uri']);
    $connectionVault->connectByToken($this->config['token']);
    return $connectionVault;
  }

  public function getConfig() {
    return $this->config;
  }

}