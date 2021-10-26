<?php

namespace Smart48\SslManager\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Smart48\SslManager\Core\DnsService;
use Smart48\SslManager\Core\SslService;
use LogicException;

class UpdateCertificate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    public $tries = 3;

    /**
     * @var string
     */
    private $domain;
    private $renew;

    /**
     * Create a new job instance.
     *
     * @param string $domain
     */
    public function __construct( $domain, $renew )
    {
        $this->domain = $domain;
        $this->renew = $renew;
    }

    /**
     * Execute the job.
     *
     * @param SslService $sslService
     *
     * @param DnsService $dnsService
     * @return void
     */
    public function handle(SslService $sslService, DnsService $dnsService)
    {
        // temporary disable this check
        // if (!$dnsService->hasProperRecord($this->domain)) {
        //     $this->fail(
        //         new LogicException(sprintf(
        //             'Domain "%s" must have proper A NAME record."',
        //             $this->domain
        //         ))
        //     );

        //     return;
        // }

        $sslService->updateCertificate($this->domain, $this->renew);
    }
}
