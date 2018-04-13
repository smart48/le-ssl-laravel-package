<?php

namespace Imagewize\SslManager\Commands;

use Illuminate\Console\Command;

class SslControllerServe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ssl-controller:serve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start SSL controller serving.';

    /**
     * Create a new command instance.
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
        $this->alert(
            config("ssl-manager.sites_directory")
        );
    }
}
