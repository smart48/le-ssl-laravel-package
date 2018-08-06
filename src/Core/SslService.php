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

        echo "+ Starting ...\r\n";
        // staging letsencrypt service
        $staging = false;
        $client = new Client([$this->accountEmail], $this->storagePath, $staging);
        $renew = filter_var($renew, FILTER_VALIDATE_BOOLEAN);
        $order = $client->getOrder(
            [
                CommonConstant::CHALLENGE_TYPE_HTTP => [$domain],
            ],
            CommonConstant::KEY_PAIR_TYPE_RSA,
            $renew
        );

        echo "+ Order expires " . $order->expires . "\r\n";

        $pendingChallenges = $order->getPendingChallengeList();
        // $pendingChallenges = [];

        if (!empty($pendingChallenges)) {

            echo "+ Adding web server configuration for " . $domain . "\r\n";
            $certificateInfo = null;
            $this->httpServer->updateSite($domain, $certificateInfo);
            $this->httpServer->reloadConfiguration();

            echo "+ Starting challenges\r\n";
            foreach ($pendingChallenges as $challenge) {
                $challengeType = $challenge->getType();
                $credential = $challenge->getCredential();

                if ($challengeType == CommonConstant::CHALLENGE_TYPE_HTTP) {
                    $domainChallengeDirectory = "{$this->challengeDirectory}/{$credential['identifier']}";

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
        } else {
            echo "+ There are no pending challenges (No need for renew)\r\n";
        }

        echo "Done!\r\n";
    }
}