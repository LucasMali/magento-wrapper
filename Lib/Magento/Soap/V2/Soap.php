<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 11/24/2015
 * Time: 8:00 AM
 */

namespace Lib\Magento\Soap\V2;


use Lib\Magento\MagentoInterface;

class Soap extends MagentoInterface
{
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
}