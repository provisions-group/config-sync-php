<?php

namespace CashExpress\ConfigSync;

use Illuminate\Console\Command;

class ConfigSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'config:sync';

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
        echo "hello!!\n";
        echo config("config-sync.key");
    }
}
