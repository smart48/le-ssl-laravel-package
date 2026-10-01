<?php
/**
 * EndpointService class file
 *
 * @author Zhang Jinlong <466028373@qq.com>
 * @link https://github.com/stonemax/acme2
 * @copyright Copyright &copy; 2018 Zhang Jinlong
 * @license https://opensource.org/licenses/mit-license.php MIT License
 */

namespace Imagewize\SslManager\Acme\Services;

use Imagewize\SslManager\Acme\Client;
use Imagewize\SslManager\Acme\Exceptions\EndpointException;
use Imagewize\SslManager\Acme\Helpers\RequestHelper;

/**
 * Class EndpointService
 * @package Imagewize\SslManager\Acme\Services
 */
#[\AllowDynamicProperties]
class EndpointService
{
    /** Change account key url
     * @var string
     */
    public $keyChange;

    /** Create new account url
     * @var string
     */
    public $newAccount;

    /** Generate new nonce url
     * @var string
     */
    public $newNonce;

    /** Create new order url
     * @var string
     */
    public $newOrder;

    /** Revoke certificate url
     * @var string
     */
    public $revokeCert;

    /**
     * EndpointService constructor.
     * @throws EndpointException
     * @throws \Imagewize\SslManager\Acme\Exceptions\RequestException
     */
    public function __construct()
    {
        $this->populate();
    }

    /**
     * Populate endpoint info
     * @throws EndpointException
     * @throws \Imagewize\SslManager\Acme\Exceptions\RequestException
     */
    private function populate()
    {
        $endpointUrl = Client::$runtime->staging === FALSE
            ? Client::$runtime->params['endpointUrl']
            : Client::$runtime->params['endpointStagingUrl'];

        list($code, , $data) = RequestHelper::get($endpointUrl);

        if ($code != 200)
        {
            throw new EndpointException("Get endpoint info failed, the url is: {$endpointUrl}");
        }

        foreach ($data as $key => $value)
        {
            $this->{$key} = $value;
        }
    }
}
