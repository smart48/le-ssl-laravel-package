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
    protected $description = 'Command description';
    /**
     * @var DnsService
     */
    private $dnsService;

    /**
     * @var string
     */
    private $targetCname;

    /**
     * @var string
     */
    private $controllerQueue;

    /**
     * Create a new command instance.
     *
     * @param DnsService $dnsService
     * @param string $targetCname
     * @param string $controllerQueue
     */
    public function __construct(DnsService $dnsService, $targetCname, $controllerQueue)
    {
        parent::__construct();
        $this->dnsService = $dnsService;
        $this->targetCname = $targetCname;
        $this->controllerQueue = $controllerQueue;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $domain = $this->argument('domain');

        if ($this->dnsService->getDomainCNAME($domain) !== $this->targetCname) {
            $this->error(
                sprintf(
                    'Domain "%s" must be pointed to "%s" via CNAME record."',
                    $domain,
                    $this->targetCname
                )
            );
            return;
        }

        UpdateCertificate::dispatch($domain)->onQueue($this->controllerQueue);

        $this->info("Certificate updating requested.");
    }
}
