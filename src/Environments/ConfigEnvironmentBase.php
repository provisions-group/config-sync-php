<?php

namespace ProvisionsGroup\ConfigSync\Environments;

use Illuminate\Console\Command;
use ProvisionsGroup\ConfigSync\Connections\ConnectionVault;

abstract class ConfigEnvironmentBase implements ConfigEnvironmentInterface
{
  abstract public function getEnvironmentConnection($credentials);
}