<?php

namespace Imagewize\SslManager\Core;

use Exception;
use stonemax\acme2\Client;
use stonemax\acme2\constants\CommonConstant;

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

    public function __construct(
        $accountEmail,
        $storagePath,
        $challengeDirectory,
        HttpService $httpService
    ) {
        $this->accountEmail = $accountEmail;
        $this->storagePath = $storagePath;
        $this->challengeDirectory = $challengeDirectory;
        $this->httpServer = $httpService;
    }

    public function updateCertificate($domain, $renew = true)
{
    $wwwDomain = "www." . $domain;
    
    echo "+ Starting ...\r\n";
    // staging letsencrypt service
    $staging = false;
    $client = new Client([$this->accountEmail], $this->storagePath, $staging);
    $renew = filter_var($renew, FILTER_VALIDATE_BOOLEAN);
    $order = $client->getOrder(
        [
            CommonConstant::CHALLENGE_TYPE_HTTP => [$domain, $wwwDomain], // Including both www and non-www domains
        ],
        CommonConstant::KEY_PAIR_TYPE_RSA,
        $renew
    );

    echo "+ Order expires " . $order->expires . "\r\n";

    $pendingChallenges = $order->getPendingChallengeList();

    // Update for non-www domain
    echo "+ Adding web server configuration for " . $domain . "\r\n";
    $certificateInfo = null;
    $this->httpServer->updateSite($domain, $certificateInfo);
    $this->httpServer->reloadConfiguration();

    // Update for www domain
    echo "+ Adding web server configuration for " . $wwwDomain . "\r\n";
    $this->httpServer->updateSite($wwwDomain, $certificateInfo);
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
    
    // Update for non-www domain
    echo "+ Writing certificate to nginx config for " . $domain . "\r\n";
    $this->httpServer->updateSite($domain, $certificateInfo);
    // Update for www domain
    echo "+ Writing certificate to nginx config for " . $wwwDomain . "\r\n";
    $this->httpServer->updateSite($wwwDomain, $certificateInfo);
    
    echo "+ Reloading web server configuration\r\n";
    $this->httpServer->reloadConfiguration();

    echo "Done!\r\n";
}

}