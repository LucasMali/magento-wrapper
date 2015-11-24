<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 11/24/2015
 * Time: 9:16 AM
 */

namespace Lib\Magento\Rest;

use Lib\Magento\MagentoInterface, OAuth;

/**
 * Class MRest
 * @package Lib\Magento\Rest
 *
 * @link http://devdocs.magento.com/guides/m1x/api/rest/introduction.html
 */
class MRest extends MagentoInterface
{
    const REST_URL = 'http://magento.local/api/rest/';
    const KEY = '3cd0823a5332363d63960013bd27fce0';
    const SECRET = '35b914f742d1b33f40a64265800e4901';

    /**
     * Contains the state of the Handshake
     * @var int
     */
    private $state;

    /**
     * Contains the token given for validation
     * @var string
     */
    private $token;

    /**
     * MRest constructor.
     * @param OAuth $oa
     * @param $state
     * @param $token
     */
    public function __construct(OAuth $oa, $state, $token)
    {
        $this->state = $state;
        $this->token = $token;
        $this->oa = $oa; // TODO Finish Implementing.
    }

    public function logIn($u = self::KEY, $p = self::SECRET)
    {
        $auth = $this->state === 2 ? OAUTH_AUTH_TYPE_AUTHORIZATION : OAUTH_AUTH_TYPE_URI;
        // TODO finish implementing.
    }

    public function createCart()
    {
        // TODO: Implement createCart() method.
    }

    public function setApiUrl($apiUrl)
    {
        // TODO: Implement setApiUrl() method.
    }
}