<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 11/24/2015
 * Time: 8:00 AM
 */

namespace Lib\Magento\Api\Soap\V2;

use Lib\Magento\Api\MagentoInterface;
use SoapClient;
use SoapFault;

/**
 * Class Soap
 * @package Lib\Magento\Soap\V2
 *
 * @todo add coupon logic
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
     * Contains the session for the SoapClient
     * @see $client
     * @var Soap::Session
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
     * @param array $request
     * @throws SoapFault
     * @throws \Exception
     */
    public function addOrder(Array $request)
    {
        try {
            $this->createShoppingCart();
            // TODO add coupon logic here
            $this->setShoppingCustomer($request['customer']);
            $this->setShoppingAddress($request['address']);
            $this->setShoppingCartPayment($request['payment']);
            $this->addShoppingProduct($request['products']);
            $this->setShoppingShipping($request['shipping']);
        } catch (SoapFault $sf) {
            throw $sf;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @throws SoapFault
     * @throws \Exception
     */
    public function createShoppingCart()
    {
        try {
            if (!$this->cartId = $this->client->shoppingCartCreate($this->session)) {
                throw new \LogicException('Something went wrong while creating a customer');
            }
        } catch (\Exception $e) {
            throw $e;
        } catch (SoapFault $sf) {
            throw $sf;
        }
    }

    /**
     * @param array $customer
     * @throws SoapFault
     * @throws \Exception
     */
    public function setShoppingCustomer(Array $customer)
    {
        try {
            if (!$this->client->shoppingCartCustomerSet($this->session, $this->cartId, $customer)) {
                throw new \LogicException('Something went wrong while creating a customer');
            }
        } catch (\Exception $e) {
            throw $e;
        } catch (SoapFault $sf) {
            throw $sf;
        }
    }

    /**
     * @param array $address
     * @throws SoapFault
     * @throws \Exception
     */
    public function setShoppingAddress(Array $address)
    {
        try {
            if (!$this->client->shoppingCartCustomerAddresses($this->session, $this->cartId, $address)) {
                throw new \LogicException('Something went wrong while creating a customer');
            }
        } catch (\Exception $e) {
            throw $e;
        } catch (SoapFault $sf) {
            throw $sf;
        }
    }

    /**
     * @param array $paymentData
     * @throws SoapFault
     * @throws \Exception
     */
    public function setShoppingCartPayment(Array $paymentData)
    {
        try {
            if (!$this->client->shoppingCartPaymentMethod($this->session, $this->cartId, $paymentData)) {
                throw new \LogicException('Something went wrong while creating a payment method');
            }
        } catch (\Exception $e) {
            throw $e;
        } catch (SoapFault $sf) {
            throw $sf;
        }
    }

    /**
     * @param array $products
     * @throws SoapFault
     * @throws \Exception
     */
    public function addShoppingProduct(Array $products)
    {
        try {
            if (!$this->client->shoppingCartProductAdd($this->session, $this->cartId, $products)) {
                throw new \LogicException('Something went wrong while adding the products');
            }
        } catch (\Exception $e) {
            throw $e;
        } catch (SoapFault $sf) {
            throw $sf;
        }
    }

    /**
     * @param $shipping
     * @throws SoapFault
     * @throws \Exception
     */
    public function setShoppingShipping($shipping)
    {
        if (!is_string($shipping)) {
            throw new \LogicException('The shipping method is invalid type');
        }

        try {
            if (!$this->client->shoppingCartProductAdd($this->session, $this->cartId, $shipping)) {
                throw new \LogicException('Something went wrong while adding the shipping method');
            }
        } catch (\Exception $e) {
            throw $e;
        } catch (SoapFault $sf) {
            throw $sf;
        }
    }

    /**
     * Finalize the Order and return the receipt
     *
     * @return mixed
     * @throws SoapFault
     * @throws \Exception
     */
    public function finalizeOrder()
    {
        // TODO consider adding checks for a cart that is order ready compliant.
        try {
            $receipt = $this->client->shoppingCartOrder($this->session, $this->cartId);
        } catch (SoapFault $sf) {
            throw $sf;
        } catch (\Exception $e) {
            throw $e;
        }

        return $receipt;
    }

    /**
     * @param $apiUrl
     */
    public function setApiUrl($apiUrl)
    {
        $this->client->setLocation($apiUrl);
    }
}