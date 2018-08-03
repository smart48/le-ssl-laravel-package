<?php

namespace Imagewize\SslManager\Commands;

use Illuminate\Console\Command;
use Imagewize\SslManager\Core\DnsService;
use Imagewize\SslManager\Jobs\UpdateCertificate;
use Imagewize\SslManager\Core\SslService;

class SslControllerUpdateCertificate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ssl-controller:update-certificate {domain} {renew=false} {now=false}';

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
    public function handle(SslService $sslService)
    {
        // https://goo.gl/JRx2aY Matt Stauffer $this->argument('argumentName')
        $domain = $this->argument('domain');
        $renew = $this->argument('renew');
        $now = $this->argument('now');

        if( $now ){
            $this->info("Certificate updating now.");
            $sslService->updateCertificate($domain, $renew);
        } else {
            UpdateCertificate::dispatch($domain, $renew)->onQueue($this->controllerQueue);
            $this->info("Certificate updating requested.");
        }
    }
}
