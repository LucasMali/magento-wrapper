<?php
/**
 * Created by PhpStorm.
 * @author Lucas Maliszewski <lucascube@gmail.com>
 * @date 11/23/2015
 */
namespace Lib\Magento;

use SoapClient, SoapFault;

/**
 * Class Magento
 *
 * This class is a basic interface to interact with magento's SOAP API Ver 2
 * @link http://devdocs.magento.com/guides/m1x/
 */
abstract class MagentoInterface
{
    const MAGENTO_V2_BASE_URL = 'http://magento.local/api/v2_soap/?wsdl';
    const MAGENTO_V1_BASE_URL = 'http://magento.local/api/soap/?wsdl';
    const DEFAULT_USER = 'lucasmali';
    const DEFAULT_PASS = 'passcode1';

    /**
     * Contains the SoapClient object
     * @var SoapClient
     */
    protected $client;

    /**
     * Contains the sessionId for the SoapClient
     * @see $client
     * @var int
     */
    protected $session;

    /**
     * Contains the cartID
     * @var int
     */
    protected $cart;

    /**
     * MagentoInterface constructor.
     * @param SoapClient $client
     */
    public function __construct(SoapClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $u
     * @param string $p
     * @throws LogicException
     */
    public function logIn($u = self::DEFAULT_USER, $p = self::DEFAULT_PASS)
    {
        if (
            !is_string($u)
            || !is_string($p)
        ) {
            throw new \LogicException('Please use a valid string type');
        }

        $this->session = $this->client->login($u, $p);
    }

    /**
     * Create a cart for buying stuff!
     */
    public function createCart()
    {
        $this->cart = $this->client->shoppingCartCreate($this->session, 1);
    }

    /**
     * @param string $apiUrl
     */
    public function setApiUrl($apiUrl)
    {
        // TODO add url validation.
        $this->client->setLocation($apiUrl);
    }
}