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

        /**
         * If no order ID, create a new order
         * @Exception stonemax\acme2\exceptions\OrderException: Get order info failed, 
         * the order url is: https://acme-v02.api.letsencrypt.org/acme/order/x/y
         * No order for ID x
         */
        if (!$order->getOrderId()) {
            echo "+ Creating new order for " . $domain . "\r\n";
            $order = $client->createOrder(
                [
                    CommonConstant::CHALLENGE_TYPE_HTTP => [$domain],
                ],
                CommonConstant::KEY_PAIR_TYPE_RSA
            );
        }

        // perhaps challenge needs to be removed as well as nginx configuration
        /* End for no order for ID x patch */

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
                if (file_put_contents(
                    "{$domainChallengeDirectory}/{$credential['fileName']}",
                    $credential['fileContent']
                ) === false) {
                    throw new Exception("Failed to save challenge file for " . $domain);
                }
            }
            echo "+ Verifying challenge for " . $domain . "\r\n";
            try {
                $challenge->verify();
            } catch (Exception $e) {
                throw new Exception("Failed to verify challenge for " . $domain . ": " . $e->getMessage());
            }
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
