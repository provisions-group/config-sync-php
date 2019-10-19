<?php

namespace CashExpress\ConfigSync\Backends;

use Illuminate\Console\Command;
use CashExpress\ConfigSync\Connections\ConnectionBase;
use CashExpress\ConfigSync\Connections\ConnectionInterface;
use CashExpress\ConfigSync\Environments\ConfigEnvironmentInterface;

interface ConfigBackendInterface 
{
  function sync(ConfigEnvironmentInterface $configEnvironment, ConnectionInterface $connection);
}