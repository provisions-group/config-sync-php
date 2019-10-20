<?php

namespace CashExpress\ConfigSync\Environments;

use Illuminate\Console\Command;

interface ConfigEnvironmentInterface 
{
  public function getEnvironmentConnection(Array $credentials);
}