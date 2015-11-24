<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 11/24/2015
 * Time: 8:00 AM
 */

namespace Lib\Magento\Soap\V2;

use Lib\Magento\MagentoInterface;

/**
 * Class Soap
 * @package Lib\Magento\Soap\V2
 *
 * This is a basic wrapper to interact with Magento's SOAP API
 */
class Soap extends MagentoInterface
{
    const SOAP_V2_URL = 'http://magento.local/api/v2_soap/?wsdl';
    const SOAP_V1_URL = 'http://magento.local/api/soap/?wsdl';
    const DEFAULT_USER = 'lucasmali';
    const DEFAULT_PASS = 'passcode1';

    /**
     * Contains the SoapClient object
     * @var SoapClient
     */
    private $client;

    /**
     * Contains the sessionId for the SoapClient
     * @see $client
     * @var int
     */
    private $session;

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
     * @param array $products
     * @return array
     * @throws \Exception
     */
    public function addProducts(Array $products)
    {
        $logs = [];
        foreach ($products as $product) {
            try {
                $logs[] = $this->soapCall($product);
            } catch (SoapFault $sf) {
                $logs['errors'][$product['sku']] = $sf->getMessage();
            } catch (\Exception $e) {
                throw $e;
            } finally {
                return $logs;
            }
        }
    }

    /**
     * @param array $product
     * @return mixed
     * @throws SoapFault
     * @throws \Exception
     */
    public function addProduct(Array $product)
    {
        try {
            $res = $this->client->catalogProductCreate(
                $this->session,
                $product['type'],
                $this->client->catalogProductAttributeSetList($this->session)[0]->set_id,
                $product['sku'],
                $product['info']
            );
        } catch (SoapFault $sf) {
            throw $sf;
        } catch (\Exception $e) {
            throw $e;
        }

        return $res;
    }

    /**
     * @param array $purchase
     * @return mixed
     * @throws SoapFault
     * @throws \Exception
     *
     * @TODO Find out if Matt wants Shopping Cart Ordres or a Sales Order wrapper.
     */
    public function addOrder(Array $purchase)
    {

        try {
            // check for an existence of a cart
            if (empty($this->cart)) {
                $this->createCart();
            }

            // TODO Continue here...

            $res = $this->client->salesOrderInvoiceCreate(
                $this->session, '1', $purchase['invoice']
            );
        } catch (SoapFault $sf) {
            throw $sf;
        } catch (\Exception $e) {
            throw $e;
        }

        return $res;
    }

    /**
     * Create a cart for buying stuff!
     */
    public function createCart()
    {
        $this->cart = $this->client->shoppingCartCreate($this->session, 1);
    }

    /**
     * @param $apiUrl
     */
    public function setApiUrl($apiUrl)
    {
        $this->client->setLocation($apiUrl);
    }
}