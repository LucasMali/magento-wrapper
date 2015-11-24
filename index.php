<?php
/**
 * Created by PhpStorm.
 * @author Lucas Maliszewski <lucascube@gmail.com>
 * @date 11/23/2015
 * For PHP 5.6 verson or higher
 *
 * @link http://devdocs.magento.com/guides/m1x/
 */

// TODO add in autoloader
if (
    (require_once 'Lib/Magento/Magento.php') === false
    || (require_once 'Lib/Magento/Soap/V2/Soap.php') === false
) {
    echo 'Oh-nos! Something went wrong. Please contact [PERSON] for further help';
    // Send an email
    error_log('[error] ' . date("Y-m-d H:i:s") . ' Unable to load the file Magento.php', 1, 'email@here.com');
    exit;
}

// Control logging events
defined('LOGGING') or define('LOGGING', false);

// THIS WOULD BE SENT OVER THE WIRE
$requests = [
    'action' => 'addproduct',
    'type' => 'simple',
    'sku' => 1234567890,
    'info' =>
        [
            'categories' => [2],
            'websites' => [1],
            'name' => 'Product name',
            'description' => 'Product description',
            'short_description' => 'Product short description',
            'weight' => '10',
            'status' => '1',
            'url_key' => 'product-url-key',
            'url_path' => 'product-url-path',
            'visibility' => '4',
            'price' => '100',
            'tax_class_id' => 1,
            'meta_title' => 'Product meta title',
            'meta_keyword' => 'Product meta keyword',
            'meta_description' => 'Product meta description'
        ]
];
// END SENT OVER THE WIRE
//
//if (!empty($_REQUEST)) {
//    $requests = file_get_contents('php://input');
//}

try {
    if (LOGGING === true) {
        $logType = 'log';
        $fileLoc = '/var/tmp/magento.log';
    }

    // Begin the API call(s)
    $client = new SoapClient(\Lib\Magento\MagentoInterface::MAGENTO_V2_BASE_URL);
    $magentoSoap = new \Lib\Magento\Soap\V2\Soap($client);
    $magentoSoap->logIn();

    /*
     * Branch logic based on action
     *
     * NOTE:    This will be replaced with the uri action sets
     *          e.g.    http://magento.local/addproduct/
     *                  http://magento.local/order
     */
    switch ($requests['action']) {
        case 'addproduct':
            $res = $magentoSoap->addProduct($requests);
            break;
        case 'order':
            $res = $magentoSoap->addOrder($requests);
            break;
    }

    /*
     * Capture the results and display them. This might be handy for branching
     * logic and displaying custom messages.
     *
     * NOTE:    The res in most test cases have been the productID if successful
     *          otherwise it returns a message e.g. Unable to insert the product
     */
    if (
        gettype($res) == 'object'
        || gettype($res) == 'array'
    ) {
        echo $message = print_r($res, true);
    } else {
        echo $message = $res;
    }
} catch (LogicException $le) {
    echo $errMessage = sprintf(
        'You did it to yourself %s %s %d',
        $le->getMessage(), $le->getFile(), $le->getLine()
    );
    $logType = 'error';
} catch (SoapFault $sf) {
    echo $errMessage = sprintf(
        'Unable to insert the product %s %s %d',
        $sf->getMessage(), $sf->getFile(), $sf->getLine()
    );
    $logType = 'error';
} catch (Exception $e) {
    echo $errMessage = sprintf(
        'Oh-Nos! Something went wrong--oops! %s %s %d',
        $e->getMessage(), $e->getFile(), $e->getLine()
    );
    $logType = 'error';
} finally {
    if (LOGGING === true) { // LOG the transaction
        if (!file_exists($fileLoc)) {
            if (!touch($fileLoc)) {
                // TODO consider adding addition catch logic for failure
            }
        }
        error_log(sprintf('[%s] %s %s', $errorType, date("Y-m-d H:i:s"), $message), 3, $fileLoc);
    }
}