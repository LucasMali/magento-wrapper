<?php
/**
 * Created by PhpStorm.
 * @author Lucas Maliszewski <lucascube@gmail.com>
 * @date 11/24/2015
 */
session_start();

// Control logging events
defined('LOGGING') or define('LOGGING', false);

// Checking we have the correct parameters needed
if (
    empty($_REQUEST)
    || !isset($_REQUEST['action'])
) {
    echo 'We need an action!';
    exit;
}

// Checking to insure we load all required files
if (
    (require_once $_SERVER['DOCUMENT_ROOT'] . '/Lib/Magento/Api/Magento.php') == false
    || (require_once $_SERVER['DOCUMENT_ROOT'] . '/Lib/Magento/Api/Soap/V2/Soap.php') == false
    || (require_once $_SERVER['DOCUMENT_ROOT'] . '/Controller/Magento/Api/SoapMeController.php') == false
) {
    echo 'Oh-nos! Something went wrong. Please contact [PERSON] for further help';
    // Send an email
    error_log('[error] ' . date("Y-m-d H:i:s") . ' Unable to load the required files', 1, 'email@here.com');
    exit;
}

// setting our action
$action = $_REQUEST['action'];

// Dummy data
$request = getDummyData();

try {
    if (LOGGING === true) {
        $logType = 'log';
        $fileLoc = '/var/tmp/magento.log';
    }

    /*
     * SOAP
     */
    $client = new SoapClient(\Lib\Magento\Api\Soap\V2\Soap::SOAP_V2_URL);
    $magentoSoap = new \Lib\Magento\Api\Soap\V2\Soap($client);
    $smc = new Controller\Magento\Api\SoapMeController($action, $request, $magentoSoap);
    $res = $smc->run();

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

} catch (Exception $e) {
    echo $message = sprintf(
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

/**
 * Moved to a function to keep the main code from getting convoluted.
 * @return array
 */
function getDummyData()
{
    return [
        'product' => [
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
        ], // end add product data
        'order' => [
            'customer' => [
                'firstname' => 'testFirstname',
                'lastname' => 'testLastName',
                'email' => 'testEmail@mail.com',
                'mode' => 'guest',
                'website_id' => '0'
            ],
            'address' => [
                [
                    'mode' => 'billing',
                    'firstname' => 'first name',
                    'lastname' => 'last name',
                    'street' => 'street address',
                    'city' => 'city',
                    'region' => 'region',
                    'postcode' => 'postcode',
                    'country_id' => 'US',
                    'telephone' => '123456789',
                    'is_default_billing' => 1
                ],
            ],
            'payment' => [
                'po_number' => null,
                'method' => 'checkmo',
                'cc_cid' => null,
                'cc_owner' => null,
                'cc_number' => null,
                'cc_type' => null,
                'cc_exp_year' => null,
                'cc_exp_month' => null
            ],
            'products' => [
                [
                    'product_id' => '4',
                    'sku' => 'simple_product',
                    'qty' => '5',
                    'options' => null,
                    'bundle_option' => null,
                    'bundle_option_qty' => null,
                    'links' => null
                ]
            ],
            'shipping' => 'freeshipping_freeshipping',
        ] // end order data
    ];
}