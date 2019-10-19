<?php

namespace CashExpress\ConfigSync\Environments;

use Illuminate\Console\Command;
use CashExpress\ConfigSync\Connections\ConnectionVault;

abstract class ConfigEnvironmentBase implements ConfigEnvironmentInterface
{
  abstract public function getEnvironmentConnection();
}