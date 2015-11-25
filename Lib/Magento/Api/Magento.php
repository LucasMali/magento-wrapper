<?php
/**
 * Created by PhpStorm.
 * @author Lucas Maliszewski <lucascube@gmail.com>
 * @date 11/23/2015
 */
namespace Lib\Magento\Api;

/**
 * Class Magento
 *
 * This class is a basic interface to interact with magento's SOAP API Ver 2
 * @link http://devdocs.magento.com/guides/m1x/
 */
abstract class MagentoInterface
{
    /**
     * Contains the cartID
     * @var int
     */
    protected $cartId;

    /**
     * @param $user
     * @param $passcode
     */
    abstract public function logIn($user, $passcode);

    /**
     * Creates a shopping cart
     * @return mixed
     */
    abstract public function createShoppingCart();

    /**
     * Changes the default api url
     * @param $apiUrl
     */
    abstract public function setApiUrl($apiUrl);
}