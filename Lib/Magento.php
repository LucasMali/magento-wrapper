<?php
/**
 * Created by PhpStorm.
 * @author Lucas Maliszewski <lucascube@gmail.com>
 * @date 11/23/2015
 */
namespace Lib;

use SoapClient, SoapFault;

/**
 * Class Magento
 *
 * This class is a basic interface to interact with magento's SOAP API Ver 2
 * @link http://devdocs.magento.com/guides/m1x/
 */
class MagentoInterface
{
    const MAGENTO_V2_BASE_URL = 'http://magento.local/api/v2_soap/?wsdl';
    const MAGENTO_V1_BASE_URL = 'http://magento.local/api/soap/?wsdl';
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
     * Contains the cartID
     * @var int
     */
    private $cart;

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
     * Create a cart for buying stuff!
     */
    public function createCart()
    {
        $this->cart = $this->client->shoppingCartCreate($this->session, 1);
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
     * @param string $apiUrl
     */
    public function setApiUrl($apiUrl)
    {
        // TODO add url validation.
        $this->client->setLocation($apiUrl);
    }
}