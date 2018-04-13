<?php

namespace Imagewize\SslManager\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Imagewize\SslManager\Core\SslService;

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

    /**
     * Create a new job instance.
     *
     * @param string $domain
     */
    public function __construct($domain)
    {
        $this->domain = $domain;
    }

    /**
     * Execute the job.
     *
     * @param SslService $sslService
     *
     * @return void
     */
    public function handle(SslService $sslService)
    {
        $sslService->updateCertificate($this->domain);
    }
}
