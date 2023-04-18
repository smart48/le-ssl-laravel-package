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
    protected $signature = 'ssl-controller:update-certificate {domain} {now=false} {renew=false}';

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
        /**
         * See https://laravel.com/docs/6.x/artisan#command-structure
         * 
         * NB Renewing causes deletion of the current certificate 
         * so only use `true` for 3rd parameter when need be
         * 
         **/
        
        $domain = $this->argument('domain');
        $now = $this->argument('now');
        $renew = $this->argument('renew') == 'true';

        if ($now == 'true') {
            $this->info("Certificate updating now.");
            $sslService->updateCertificate($domain, $renew);
        } else {
            UpdateCertificate::dispatch($domain, $renew)->onQueue($this->controllerQueue);
            $this->info("Certificate updating requested.");
        }
    }
}
