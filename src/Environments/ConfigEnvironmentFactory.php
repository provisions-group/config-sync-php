<?php

namespace ProvisionsGroup\ConfigSync\Environments;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ConfigEnvironmentFactory
{
  public function getConfigEnvironment(String $environment) {
    if(!is_null($environment) && trim($environment) != "") {
      $class = config('config-sync.environments.' . $environment . '.class');
      if(!is_null($class) && trim($class) != "") {
      $this->log("info", "{$environment} <-> {$class}");
 
       try {
         $newClass = new $class($environment);
         return $newClass;
       } catch(\Throwable $e) {
         $this->log("error","{$class} - couldn't create that class");
       }
    }
      else {
       $this->log("error","{$environment} - no matching configuration");
      }
    }
    else {
       $this->log("error", "you have not chosen a environment");
     }
     return null;
   }  
 
   private function log($level, $message) {
     //this forces the log output to the console since we're in an artisan command
     Log::channel("stderr")->$level($message);
   }
}