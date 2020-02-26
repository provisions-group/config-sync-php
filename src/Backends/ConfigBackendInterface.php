<?php

namespace ProvisionsGroup\ConfigSync\Backends;

use Illuminate\Console\Command;
use ProvisionsGroup\ConfigSync\Connections\ConnectionBase;
use ProvisionsGroup\ConfigSync\Connections\ConnectionInterface;
use ProvisionsGroup\ConfigSync\Environments\ConfigEnvironmentInterface;

interface ConfigBackendInterface 
{
  function sync(ConfigEnvironmentInterface $configEnvironment, ConnectionInterface $connection);
}