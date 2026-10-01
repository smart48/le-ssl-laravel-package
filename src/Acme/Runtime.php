<?php
/**
 * Runtime class file
 *
 * @author Zhang Jinlong <466028373@qq.com>
 * @link https://github.com/stonemax/acme2
 * @copyright Copyright &copy; 2018 Zhang Jinlong
 * @license https://opensource.org/licenses/mit-license.php MIT License
 */

namespace Imagewize\SslManager\Acme;

use Imagewize\SslManager\Acme\Services\AccountService;
use Imagewize\SslManager\Acme\Services\EndpointService;
use Imagewize\SslManager\Acme\Services\NonceService;
use Imagewize\SslManager\Acme\Services\OrderService;

/**
 * Class Runtime
 * @package Imagewize\SslManager\Acme
 */
class Runtime
{
    /**
     * Email list
     * @var array
     */
    public $emailList;

    /**
     * Storage path for certificate keys, public/private key pair and so on
     * @var string
     */
    public $storagePath;

    /**
     * If staging status
     * @var bool
     */
    public $staging;

    /**
     * Config params
     * @var array
     */
    public $params;

    /**
     * Account service instance
     * @var \Imagewize\SslManager\Acme\Services\AccountService
     */
    public $account;

    /**
     * Order service instance
     * @var \Imagewize\SslManager\Acme\Services\OrderService
     */
    public $order;

    /**
     * Endpoint service instance
     * @var \Imagewize\SslManager\Acme\Services\EndpointService
     */
    public $endpoint;

    /**
     * Nonce service instance
     * @var \Imagewize\SslManager\Acme\Services\NonceService
     */
    public $nonce;

    /**
     * Runtime constructor.
     * @param array $emailList
     * @param string $storagePath
     * @param bool $staging
     */
    public function __construct($emailList, $storagePath, $staging = FALSE)
    {
        $this->emailList = array_filter(array_unique($emailList));
        $this->storagePath = rtrim(trim($storagePath), '/\\');
        $this->staging = boolval($staging);

        sort($this->emailList);
    }

    /**
     * Init
     */
    public function init()
    {
        $this->params = require(__DIR__.'/config.php');

        $this->endpoint = new EndpointService();
        $this->nonce = new NonceService();
        $this->account = new AccountService($this->storagePath.'/account');

        $this->account->init();
    }

    /**
     * Get order service instance
     * @param array $domainInfo
     * @param string $algorithm
     * @param bool $generateNewOder
     * @return OrderService
     * @throws exceptions\AccountException
     * @throws exceptions\NonceException
     * @throws exceptions\OrderException
     * @throws exceptions\RequestException
     */
    public function getOrder($domainInfo, $algorithm, $generateNewOder)
    {
        if (!$this->order)
        {
            $this->order = new OrderService($domainInfo, $algorithm, $generateNewOder);
        }

        return $this->order;
    }
}
