<?php

namespace ProvisionsGroup\ConfigSync\Environments;

use Illuminate\Console\Command;
use ProvisionsGroup\ConfigSync\Connections\ConnectionVault;

class ConfigEnvironmentKubernetesVault extends ConfigEnvironmentBase
{
  private $environment;
  private $config;

  public function __construct($environment) {
    $this->environment = $environment;
    $this->config = config('config-sync.environments.' . $environment);
  }

  public function getEnvironmentConnection($credentials) : ConnectionVault {
    $connectionVault = new ConnectionVault($this->config['base_uri']);
    $connectionVault->connectByK8sJwt($credentials['jwt'], $credentials['role']);
    return $connectionVault;
  }

  public function getConfig() {
    return $this->config;
  }

}