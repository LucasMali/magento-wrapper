<?php
/**
 * Created by PhpStorm.
 * @author Lucas Maliszewski <lucascube@gmail.com>
 * @date 11/24/2015
 */

namespace Controller\Magento\Api;

use Lib\Magento\Api\Soap\V2\Soap;

/**
 * Class SoapMeController
 *
 * This is a hybrid [MV]C controller, taking out the Model View.
 * It interacts with the Library[Model] SOAP Object based off of the public
 * trigger[view].
 *
 * @package Controller\Magento\Api
 */
class SoapMeController
{
    const CREATE = 'create';
    const ORDER = 'order';

    /**
     * Contains the action to be ran
     * @var string
     */
    private $action;

    /**
     * Contains the values to be processed
     * @var array
     */
    private $request;

    /**
     * The Soap Object
     * @var Soap
     */
    private $ms;

    /**
     * SoapMeController constructor.
     * @param $action
     * @param array $request
     * @param Soap $ms
     */
    public function __construct($action, Array $request, Soap $ms)
    {
        if (!is_string($action)) {
            throw new \LogicException(sprintf('Invalid type given: Expected string %s given', gettype($action)));
        }

        $this->action = $action;
        $this->request = $request;
        $this->ms = $ms;
    }

    /**
     * Run
     *
     * Simple action command to process the events based on the action given.
     *
     * @return mixed
     * @throws \Exception
     */
    public function run()
    {
        try {

            $this->ms->logIn();

            switch ($this->action) {
                case self::CREATE:
                    $res = $this->ms->addProduct($this->request['product']);
                    break;
                case self::ORDER:
                    $this->ms->addOrder($this->request['order']);
                    $res = $this->ms->finalizeOrder();
                    break;
            }

            return $res;

        } catch (\Exception $e) {
            throw $e;
        }
    }

}