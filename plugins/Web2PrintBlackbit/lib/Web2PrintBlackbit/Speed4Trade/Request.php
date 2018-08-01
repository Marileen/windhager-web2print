<?php
/**
 * Created by PhpStorm.
 * User: ckogler
 * Date: 04.12.2017
 * Time: 14:42
 */

namespace Web2PrintBlackbit\Speed4Trade;
class Request {

    public $requestData;

    public function __construct($requestData)
    {
        $this->requestData = $requestData;
    }
}