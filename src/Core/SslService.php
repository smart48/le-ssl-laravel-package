<?php

namespace Imagewize\SslManager\Core;

use Exception;
use Imagewize\SslManager\Acme\Client;
use Imagewize\SslManager\Acme\Constants\CommonConstant;

class SslService
{
    /**
     * @var string
     */
    private $accountEmail;

    /**
     * @var string
     */
    private $storagePath;

    /**
     * @var string
     */
    private $challengeDirectory;

    /**
     * @var HttpService
     */
    private $httpServer;

    /**
     * @var bool
     */
    private $staging;

    public function __construct(
        $accountEmail,
        $storagePath,
        $challengeDirectory,
        HttpService $httpService,
        $staging = false
    ) {
        $this->accountEmail = $accountEmail;
        $this->storagePath = $storagePath;
        $this->challengeDirectory = $challengeDirectory;
        $this->httpServer = $httpService;
        $this->staging = filter_var($staging, FILTER_VALIDATE_BOOLEAN);
    }

    public function updateCertificate($domain, $renew = true)
    {
        $sslDomains = [$domain];
        if (count(explode('.', $domain)) === 2) {
            $sslDomains[] = "www.{$domain}";
        }

        echo "+ Starting ...\r\n";
        // staging letsencrypt service issues untrusted test certificates
        $client = new Client([$this->accountEmail], $this->storagePath, $this->staging);
        $renew = filter_var($renew, FILTER_VALIDATE_BOOLEAN);
        $order = $client->getOrder(
            [
                CommonConstant::CHALLENGE_TYPE_HTTP => [$domain, "www.{$domain}"],
            ],
            CommonConstant::KEY_PAIR_TYPE_RSA,
            $renew
        );

        echo "+ Order expires " . $order->expires . "\r\n";

        $pendingChallenges = $order->getPendingChallengeList();


        echo "+ Adding web server configuration for " . $domain . "\r\n";
        $certificateInfo = null;
        $this->httpServer->updateSite($domain, $certificateInfo);
        $this->httpServer->reloadConfiguration();

        echo "+ Starting challenges\r\n";
        foreach ($pendingChallenges as $challenge) {
            $challengeType = $challenge->getType();
            $credential = $challenge->getCredential();

            if ($challengeType == CommonConstant::CHALLENGE_TYPE_HTTP) {
                $domainChallengeDirectory = "{$this->challengeDirectory}/{$domain}";

                if (!file_exists($domainChallengeDirectory)) {
                    mkdir($domainChallengeDirectory, 0755, true);
                }

                echo "+ Saving challenge file for " . $domain . "\r\n";
                file_put_contents(
                    "{$domainChallengeDirectory}/{$credential['fileName']}",
                    $credential['fileContent']
                );
            }
            echo "+ Verifying challenge for " . $domain . "\r\n";
            $challenge->verify();
        }
        
        echo "+ Getting certificate info (this can take a while)\r\n";
        $certificateInfo = $order->getCertificateFile();
        echo "+ Writing certificate to nginx config\r\n";
        $this->httpServer->updateSite($domain, $certificateInfo);
        echo "+ Reloading web server configuration\r\n";
        $this->httpServer->reloadConfiguration();

        echo "Done!\r\n";
    }
}