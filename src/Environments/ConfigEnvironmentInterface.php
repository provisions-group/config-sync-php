<?php

namespace ProvisionsGroup\ConfigSync\Environments;

use Illuminate\Console\Command;

interface ConfigEnvironmentInterface 
{
  public function getEnvironmentConnection(Array $credentials);
}