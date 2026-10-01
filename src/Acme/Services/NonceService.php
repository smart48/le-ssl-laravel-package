<?php
/**
 * NonceService class file
 *
 * @author Zhang Jinlong <466028373@qq.com>
 * @link https://github.com/stonemax/acme2
 * @copyright Copyright &copy; 2018 Zhang Jinlong
 * @license https://opensource.org/licenses/mit-license.php MIT License
 */

namespace Imagewize\SslManager\Acme\Services;

use Imagewize\SslManager\Acme\Client;
use Imagewize\SslManager\Acme\Exceptions\NonceException;
use Imagewize\SslManager\Acme\Helpers\CommonHelper;
use Imagewize\SslManager\Acme\Helpers\RequestHelper;

/**
 * Class NonceService
 * @package Imagewize\SslManager\Acme\Services
 */
class NonceService
{
    /**
     * Request nonce anti replay attack
     * @var string
     */
    private $_nonce;

    /**
     * NonceService constructor.
     */
    public function __construct()
    {
        // do nothing
    }

    /**
     * Get nonce
     * @return string
     * @throws NonceException
     * @throws \Imagewize\SslManager\Acme\Exceptions\RequestException
     */
    public function get()
    {
        if (!$this->_nonce)
        {
            $this->_nonce = $this->getNew();
        }

        $nonce = $this->_nonce;

        $this->destroy();

        return $nonce;
    }

    /**
     * Set nonce
     * @param string $nonce
     */
    public function set($nonce)
    {
        $this->_nonce = $nonce;
    }

    /**
     * Destroy nonce
     */
    public function destroy()
    {
        $this->_nonce = NULL;
    }

    /**
     * Get new nonce for next request
     * @return string
     * @throws NonceException
     * @throws \Imagewize\SslManager\Acme\Exceptions\RequestException
     */
    private function getNew()
    {
        $newNonceUrl = Client::$runtime->endpoint->newNonce;

        list($code, $header, ) = RequestHelper::head($newNonceUrl);

        if ($code != 200)
        {
            throw new NonceException("Get new nonce failed, the url is: {$newNonceUrl}");
        }

        $nonce = CommonHelper::getNonceFromResponseHeader($header);

        if (!$nonce)
        {
            throw new NonceException("Get new nonce failed, the header doesn't contain `Replay-Nonce` filed, the url is: {$newNonceUrl}");
        }

        return $nonce;
    }
}
