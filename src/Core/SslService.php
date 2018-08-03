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

    public function updateCertificate($domain, $renew = false)
    {
        $client = new Client([$this->accountEmail], $this->storagePath, false);
        $order = $client->getOrder(
            [
                CommonConstant::CHALLENGE_TYPE_HTTP => [$domain],
            ],
            CommonConstant::KEY_PAIR_TYPE_RSA,
            $renew
        );

        // temporary disable this check
        // try {
        //     $certificateInfo = $order->getCertificateFile();
        // } catch (Exception $e) {
        //     $certificateInfo = null;
        // }
        $certificateInfo = null;

        $pendingChallenges = $order->getPendingChallengeList();

        foreach ($pendingChallenges as $challenge) {
            $challengeType = $challenge->getType();
            $credential = $challenge->getCredential();

            if ($challengeType == CommonConstant::CHALLENGE_TYPE_HTTP) {
                $domainChallengeDirectory = "{$this->challengeDirectory}/{$credential['identifier']}";

                if (!file_exists($domainChallengeDirectory)) {
                    mkdir($domainChallengeDirectory, 0777, true);
                }

                file_put_contents(
                    "{$domainChallengeDirectory}/{$credential['fileName']}",
                    $credential['fileContent']
                );

                $this->httpServer->updateSite($domain, $certificateInfo);
                $this->httpServer->reloadConfiguration();
            }

            $challenge->verify();
        }

        $certificateInfo = $order->getCertificateFile();

        $this->httpServer->updateSite($domain, $certificateInfo);
        $this->httpServer->reloadConfiguration();
    }
}