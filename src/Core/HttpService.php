<?php

namespace Smart48\SslManager\Core;

use Illuminate\Contracts\View\Factory as ViewFactory;

class HttpService
{
    /**
     * @var string
     */
    private $challengeDirectory;

    /**
     * @var string
     */
    private $sitesConfigDirectory;

    /**
     * @var string
     */
    private $httpReloadCommand;

    /**
     * @var ViewFactory
     */
    private $viewFactory;

    /**
     * HttpServerController constructor.
     *
     * @param $challengeDirectory
     * @param $sitesConfigDirectory
     * @param $httpReloadCommand
     * @param ViewFactory $viewFactory
     */
    public function __construct(
        $challengeDirectory,
        $sitesConfigDirectory,
        $httpReloadCommand,
        ViewFactory $viewFactory
    ) {
        $this->challengeDirectory = $challengeDirectory;
        $this->sitesConfigDirectory = $sitesConfigDirectory;
        $this->httpReloadCommand = $httpReloadCommand;
        $this->viewFactory = $viewFactory;
    }

    /**
     * @param string $domain
     * @param array|null $certificateInfo
     */
    public function updateSite($domain, array $certificateInfo = null)
    {
        $config = $this->viewFactory
            ->make('ssl-manager::site', [
                'domain' => $domain,
                'challengeDirectory' => $this->challengeDirectory,
                'certificateInfo' => $certificateInfo,
            ])
            ->render();
        if (!file_exists($this->sitesConfigDirectory) ) {
             mkdir($this->sitesConfigDirectory, 0755, true);
        }
        file_put_contents("{$this->sitesConfigDirectory}/{$domain}.conf", $config);
    }

    /**
     * @return boolean
     */
    public function reloadConfiguration()
    {
        ob_start();
        system($this->httpReloadCommand, $exitCode);
        ob_end_clean();

        sleep(5);

        return ! $exitCode;
    }
}
