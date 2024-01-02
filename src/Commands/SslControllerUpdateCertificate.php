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
     * Handle the SSL certificate update command.
     * 
     * This command is designed to update SSL certificates for a specified domain using Laravel's Artisan CLI.
     * The command structure follows Laravel's conventions (https://laravel.com/docs/9.x/artisan#command-structure).
     * 
     * @param SslService $sslService
     * The service responsible for SSL certificate operations.
     * 
     * Renewing causes deletion of the current certificate so only use `true` for 3rd parameter when need be
     * 
     * @return void
     */
    public function handle(SslService $sslService)
    {

        // Extracting command arguments
        $domain = $this->argument('domain');
        $now = $this->argument('now');
        $renew = $this->argument('renew') == 'true';

        // Check if the certificate update should be done immediately or queued for later
        if ($now == 'true') {
            $this->info("Certificate updating now.");
            $sslService->updateCertificate($domain, $renew);
        } else {
            // Queue the certificate update for background processing
            UpdateCertificate::dispatch($domain, $renew)->onQueue($this->controllerQueue);
            $this->info("Certificate updating requested.");
        }
    }
}
