<?php

namespace ProvisionsGroup\ConfigSync;

use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use ProvisionsGroup\ConfigSync\Backends\ConfigBackendBase;
use ProvisionsGroup\ConfigSync\Backends\ConfigBackendFactory;
use ProvisionsGroup\ConfigSync\Environments\ConfigEnvironmentFactory;

class ConfigSyncCommandBase extends Command
{
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function stampdate() {
        return $this->timestampFormat(date("m-d-Y H:i:s T"));
    }

    protected function heading($text) {
        $table = new Table($this->output);
        $table->setRows([[$this->stampdate(), $this->headingFormat($text)]]);
        $table->render(); 
    }

    protected function successFormat($text) {
        return "<fg=green>{$text}</>";
    }

    protected function failureFormat($text) {
        return "<fg=red>{$text}</>";
    }

    protected function timestampFormat($text) {
        return "<fg=yellow>{$text}</>";
    }

    protected function headingFormat($text) {
        return "<fg=blue>{$text}</>";
    }

    protected function titleFormat($text) {
        return "<fg=red;options=bold>{$text}</>";
    }
}
