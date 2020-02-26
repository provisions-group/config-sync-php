<?php

namespace ProvisionsGroup\ConfigSync\Backends;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Exception;

class ConfigBackendFactory
{
  public function getConfigBackend(string $backend) {
    if(!is_null($backend) && trim($backend) != "") {
     $class = config('config-sync.backends.' . $backend . '.class');
     if(!is_null($class) && trim($class) != "") {
     $this->log("info", "{$backend} <-> {$class}");

      try {
        $newClass = new $class();
        return $newClass;
      } catch(\Throwable $e) {
        $this->log("error","{$class} - couldn't create that class");
        throw $e;
      }
   }
     else {
      $this->log("error","{$backend} - no matching configuration");
     }
   }
   else {
      $this->log("error", "you have not chosen a backend");
    }
    return null;
  }  

  private function log($level, $message) {
    //this forces the log output to the console since we're in an artisan command
    Log::channel("stderr")->$level($message);
  }
}