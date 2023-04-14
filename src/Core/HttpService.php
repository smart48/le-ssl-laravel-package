<?php

namespace Imagewize\SslManager\Core;

use Exception;
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
     * @throws Exception if unable to reload configuration
     */
    public function reloadConfiguration()
    {
        $output = '';
        $exitCode = 0;

        try {
            ob_start();
            system($this->httpReloadCommand, $exitCode);
            $output = ob_get_clean();

            if ($exitCode !== 0) {
                throw new Exception("Failed to reload configuration. Exit code: $exitCode. Output: $output");
            }

            sleep(5);

            return true;
        } catch (Exception $e) {
            // Clean output buffer before re-throwing exception
            ob_clean();
            throw $e;
        }
    }

}
