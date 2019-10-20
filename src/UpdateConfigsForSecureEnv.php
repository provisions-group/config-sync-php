<?php

namespace CashExpress\ConfigSync;

use RecursiveIteratorIterator;
use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use CashExpress\ConfigSync\Backends\ConfigBackendBase;
use CashExpress\ConfigSync\Backends\ConfigBackendFactory;
use CashExpress\ConfigSync\Environments\ConfigEnvironmentFactory;

class UpdateConfigsForSecureEnv extends ConfigSyncCommandBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'config:secure-files 
                                {--revert : set the config/ files back to using env() }';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the config/ files substituting the default env() for env_secure()';

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

        //validate the input and if it comes back successfully do nothing, otherwise, exit
        !$this->validateInput() ? exit() : null;

        if($this->option("revert")) {
            $this->revertConfigFilesToEnv();
        }
        else {
            $this->updateConfigFilesToSecure();
        }

    }

    function displayWelcome() {
        $table = new Table($this->output);
        $table->setRows([[$this->stampdate(),$this->titleFormat("Update Configs to Secure")]]);
        $table->render();

        //spacer line
        $this->info("");
    }

    function validateInput() {
        $commentLineLength = 60;
        $successFlag = true;

        //revert parameter validation
        $revert = $this->option("revert");
        $revertString = $this->option("revert") == true ? "true" : "false";
        if(!is_bool($revert)) {
            $revertStatus = $this->failureFormat("failed");
            $revertComment = wordwrap("should not be set to anything - it's just a flag", $commentLineLength);
            $successFlag = false;
        }
        else {
            $revertStatus = $this->successFormat("success");
            $revertComment = wordwrap("successfully set revert status to {$revertString}", $commentLineLength);
        }

        $this->heading("Checking inputs...");

        $table = new Table($this->output);
        $separator = new TableSeparator();

        $table->setRows([
            ["Parameter", "Value", "Status", "Comment"],
            $separator,            
            ["--revert", $revertString, $revertStatus, $revertComment],
        ]);

        $table->render();

        if(!$successFlag) {
            $this->heading("...uh oh, inputs are not valid!");
        }
        else {
            $this->heading("...done - inputs are valid!");
        }
        //spacer line
        $this->info("");

        return $successFlag;
    }

    function updateConfigFilesToSecure() {
        $this->heading("Setting config files to use env_secure()...");

        $path = realpath(config_path()); 
        $fileList = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
        
        foreach ($fileList as $item) {
            if ($item->isFile() && stripos($item->getPathName(), 'php') !== false) {
                $file_contents = file_get_contents($item->getPathName());
                $file_contents = str_replace("env(","env_secure(",$file_contents);
                file_put_contents($item->getPathName(),$file_contents);

                $this->line($this->stampdate() . " - " . $item->getFileName() . " - updated to env_secure()");
            }
        }

        $this->heading("... successfully updated!");

        //spacer line
        $this->info("");
    }

    function revertConfigFilesToEnv() {
        $this->heading("Resetting config files to use env()...");

        $path = realpath(config_path());
        $fileList = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
        
        foreach ($fileList as $item) {
            if ($item->isFile() && stripos($item->getPathName(), 'php') !== false) {
                $file_contents = file_get_contents($item->getPathName());
                $file_contents = str_replace("env_secure(","env(",$file_contents);
                file_put_contents($item->getPathName(),$file_contents);
                $this->line($this->stampdate() . " - " . $item->getfileName() . " - reverted to env()");
            }
        }

        $this->heading("... successfully reverted!");

        //spacer line
        $this->info("");
    }
}
