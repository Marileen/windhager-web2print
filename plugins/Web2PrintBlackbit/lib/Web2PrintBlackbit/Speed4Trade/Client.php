<?php
/**
 * Created by PhpStorm.
 * User: ckogler
 * Date: 04.12.2017
 * Time: 14:42
 */

namespace Web2PrintBlackbit\Speed4Trade;

class Client {

    protected static $soapClient;

    protected static $speed4TradeConfig = null;
    public function __construct()
    {
        if(!self::$soapClient){
            $config = include \Pimcore\Config::locateConfigFile("speed4Trade.php");
            self::$speed4TradeConfig = $config;
            $options = [];
            $options['login'] = $config['username'];
            $options['password'] = $config['password'];
            $options['soap_version'] = \SOAP_1_2;
            $options['trace'] = \Pimcore::inDebugMode();
            $options['classmap']['fetchMandators'] = 'Request';

            $context = stream_context_create([
                'ssl' => [
                    // set some SSL/TLS specific options
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ]);
            $options['stream_context'] = $context;
            self::$soapClient = new \SoapClient($config['wsdl'], $options);
            self::$soapClient->__setLocation($config['soapEndpoint']);
        }
        return $this;
    }

    /**
     * @return mixed|null
     */
    public static function getSpeed4TradeConfig()
    {
        return self::$speed4TradeConfig;
    }


    /**
     * @param string $method
     * @param string $xmlString
     * @return \SimpleXMLElement
     */
    public function call($method,$xmlString){
        $result = self::$soapClient->$method(new Request($xmlString));
        $xml = simplexml_load_string($result->return);
        return $xml;
    }
}