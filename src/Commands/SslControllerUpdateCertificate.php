<?php

namespace Imagewize\SslManager\Commands;

use Illuminate\Console\Command;
use Imagewize\SslManager\Core\DnsService;
use Imagewize\SslManager\Jobs\UpdateCertificate;

class SslControllerUpdateCertificate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ssl-controller:update-certificate {domain}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Request SSL certificate updating.';

    /**
     * @var string
     */
    private $controllerQueue;

    /**
     * Create a new command instance.
     *
     * @param string $controllerQueue
     */
    public function __construct($controllerQueue)
    {
        parent::__construct();
        $this->controllerQueue = $controllerQueue;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // https://goo.gl/JRx2aY Matt Stauffer $this->argument('argumentName')
        $domain = $this->argument('domain');

        UpdateCertificate::dispatch($domain)->onQueue($this->controllerQueue);

        $this->info("Certificate updating requested.");
    }
}
