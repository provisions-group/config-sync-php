<?php

namespace CashExpress\ConfigSync;

use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use CashExpress\ConfigSync\Backends\ConfigBackendBase;
use CashExpress\ConfigSync\Backends\ConfigBackendFactory;
use CashExpress\ConfigSync\Environments\ConfigEnvironmentFactory;

class ConfigSync extends ConfigSyncCommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'config:sync 
                                {--backend=vault : The backend to use for config values} 
                                {--environment=developer : The backend environment to use} 
                                {--watch : Flag that keeps the process running rather than running once}
                                {--refresh=10 : Only used if the --watch flag is set; frequency of checking with backend and refreshing file}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronizes a config backend with the Laravel configs';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //Display welcome message
        $this->displayWelcome();

        //Step 1. validate the input and if it comes back successfully do nothing, otherwise, exit
        !$this->validateInput() ? exit() : null;

        //Step 2. Synch with backend in a matching envirvonment
        $this->syncWithBackendInEnvironment($this->option("backend"), $this->option("environment")); 
        
        //Enter Step 2 watch loop and sleep on wait if flag is true
        while($this->option("watch")) {
            //sleep for the duration specified
            sleep($this->option("refresh"));

            //Step 2. synch with backend in a matching envirvonment
            $this->syncWithBackendInEnvironment($this->option("backend"), $this->option("environment")); 
        }
    }

    function displayWelcome() {
        $table = new Table($this->output);
        $table->setRows([[$this->stampdate(),$this->titleFormat("Config Sync - A Tool to Securely Integrate Secrets Backends with App Configurations")]]);
        $table->render();

        //spacer line
        $this->info("");
    }

    function validateInput() {
        $commentLineLength = 60;
        $successFlag = true;

        //backend parameter validation
        $backend = $this->option("backend");
        if(!is_string($backend)) {
            $backendStatus = $this->failureFormat("failed");
            $backendComment = wordwrap("needs to be a string and set to backend in config-sync.php", $commentLineLength);
            $successFlag = false;
        }
        else {
            $class = config('config-sync.backends.' . $backend . '.class');
            if(!is_null($class) && trim($class) != "") {
                $backendStatus = $this->successFormat("success");
                $backendComment = wordwrap("successfully mapped chosen backend ({$backend}) to configured class ({$class})", $commentLineLength);
            }
            else {
                $backendStatus = $this->failureFormat("failed");
                $backendComment = wordwrap("chosen a backend ({$backend}) that has no matching configuration", $commentLineLength);
                $successFlag = false;
            }
        }

        //environment parameter validation
        $environment = $this->option("environment");
        if(!is_string($environment)) {
            $environmentStatus = $this->failureFormat("failed");
            $environmentComment = wordwrap("needs to be a string and set to environment in config-sync.php", $commentLineLength);
            $successFlag = false;
        }
        else {
            $class = config('config-sync.environments.' . $environment . '.class');
            if(!is_null($class) && trim($class) != "") {
                $environmentStatus = $this->successFormat("success");
                $environmentComment = wordwrap("successfully mapped chosen environment ({$environment}) to class ({$class})", $commentLineLength);
            }
            else {
                $environmentStatus = $this->failureFormat("failure");
                $environmentComment = wordwrap("chosen environment ({$environment}) that has no matching configuration", $commentLineLength);
                $successFlag = false;
            }
        }

        //watch parameter validation
        $watch = $this->option("watch");
        $watchString = $this->option("watch") == true ? "true" : "false";
        if(!is_bool($watch)) {
            $watchStatus = $this->failureFormat("failed");
            $watchComment = wordwrap("should not be set to anything - it's just a flag", $commentLineLength);
            $successFlag = false;
        }
        else {
            $watchStatus = $this->successFormat("success");
            $watchComment = wordwrap("successfully set watch status to {$watchString}", $commentLineLength);
        }

        $refresh = $this->option("refresh");
        if(intval($refresh) == 0) {
            $refreshStatus = $this->failureFormat("failed");
            $refreshComment = wordwrap("integer of seconds between watch polls", $commentLineLength);
            $successFlag = false;
        }
        else {
            $refreshStatus = $this->successFormat("success");
            $refreshComment = wordwrap("successfully set refresh interval to {$refresh} seconds", $commentLineLength);
        }

        $this->heading("Step 1. Checking inputs...");

        $table = new Table($this->output);
        $separator = new TableSeparator();
        $table->setRows([
            ["Parameter", "Value", "Status", "Comment"],
            $separator,            
            ["--backend", $backend, $backendStatus, $backendComment],
            $separator,
            ["--environment", $environment, $environmentStatus, $environmentComment],
            $separator,
            ["--watch", $watchString, $watchStatus, $watchComment],
            $separator,
            ["--refresh", $refresh, $refreshStatus, $refreshComment]
        ]);

        $table->render();
        if(!$successFlag) {
            $this->heading("Step 1. ...uh oh, inputs are not valid!");
        }
        else {
            $this->heading("Step 1. ...done - inputs are valid!");
        }
        //spacer line
        $this->info("");

        return $successFlag;
    }

    function syncWithBackendInEnvironment(string $backend, string $environment) {
        $this->heading("Step 2. Starting to sychronize with backend [{$backend}] for environment [{$environment}]");

        $configBackend = (new ConfigBackendFactory())->getConfigBackend($backend);
        if($configBackend == null) {
            $this->error($this->failureFormat("The config backend had an error - see error in above logs"));
            exit();
        }

        $configEnvrionment = (new ConfigEnvironmentFactory())->getConfigEnvironment($environment);

        //TODO: need to move these three into their matching ConfigEnvironmentXYZ
        if($configEnvrionment->getConfig()['auth'] == "token") {
            $credentials['token'] = $configEnvrionment->getConfig()['token'];
          }
          
        if($configEnvrionment->getConfig()['auth'] == "ldap") {
            $credentials['username'] = $this->ask('LDAP Username');
            $credentials['password'] = $this->secret('LDAP Password');
        }

        if($configEnvrionment->getConfig()['auth'] == "kubernetes") {
            $credentials['jwt'] = file_get_contents('/var/run/secrets/kubernetes.io/serviceaccount/token');
            $credentials['role'] = $configEnvrionment->getConfig()['role'];
        }
          
        $configEnvrionmentConnection = $configEnvrionment->getEnvironmentConnection($credentials);
        $successFlag = $configBackend->sync($configEnvrionment, $configEnvrionmentConnection);

        if(!$successFlag) {
            $this->heading("Step 2. ...uh oh, data did not synchronize!");
        }
        else {
            $this->heading("Step 2. ...done - data sychronized to ecrypted file!");
        }

        //spacer line
        $this->info("");
    }
}
