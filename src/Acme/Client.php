<?php
/**
 * Client class file
 *
 * @author Zhang Jinlong <466028373@qq.com>
 * @link https://github.com/stonemax/acme2
 * @copyright Copyright &copy; 2018 Zhang Jinlong
 * @license https://opensource.org/licenses/mit-license.php MIT License
 */

namespace Imagewize\SslManager\Acme;

/**
 * Class Client
 * @package Imagewize\SslManager\Acme
 */
class Client
{
    /**
     * Runtime instance
     * @var Runtime
     */
    public static $runtime;

    /**
     * Client constructor.
     * @param array $emailList
     * @param string $storagePath
     * @param bool $staging
     */
    public function __construct($emailList, $storagePath, $staging = FALSE)
    {
        self::$runtime = new Runtime($emailList, $storagePath, $staging);

        self::$runtime->init();
    }

    /**
     * Get account service instance
     * @return services\AccountService
     */
    public function getAccount()
    {
        return self::$runtime->account;
    }

    /**
     * Get order service instance
     * @param array $domainInfo
     * @param string $algorithm
     * @param bool $generateNewOder
     * @return services\OrderService
     * @throws exceptions\AccountException
     * @throws exceptions\NonceException
     * @throws exceptions\OrderException
     * @throws exceptions\RequestException
     */
    public function getOrder($domainInfo, $algorithm, $generateNewOder = TRUE)
    {
        return self::$runtime->getOrder($domainInfo, $algorithm, $generateNewOder);
    }
}
