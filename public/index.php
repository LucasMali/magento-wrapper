<?php
/**
 * Created by PhpStorm.
 * @author Lucas Maliszewski <lucascube@gmail.com>
 * @date 11/23/2015
 * For PHP 5.6 version or higher
 *
 * @todo    What is left:
 *          <ul>
 *              <li>Add in coupon logic</li>
 *              <li>Add in the validation logic for email and urls</li>
 *              <li>Add in REST APIs</li>
 *              <li>Add in Magento.php universal abstracts for both REST and SOAP</li>
 *              <li>Add unit tests</li>
 *          </ul>
 *
 * @link http://devdocs.magento.com/guides/m1x/
 */
?>
<html>
    <h1>Welcome</h1>
    <p>The subcategories below will show functionality provided</p>
    <ul>
        <li>
            <p>Using http://indaba.local/soapme/create URL will create an <b>object</b> into magento</p>
            <a href="http://indaba.local/soapme/create">SOAP Create</a>
        </li>
        <li>
            <p>Using http://indaba.local/soapme/create URL will create a <b>purchase</b> into magento</p>
            <a href="http://indaba.local/soapme/checkout">SOAP Checkout</a>
        </li>
    </ul>
</html>
