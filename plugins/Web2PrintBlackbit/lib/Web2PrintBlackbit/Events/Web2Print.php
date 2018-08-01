<?php
/**
 * Created by PhpStorm.
 * User: ckogler
 * Date: 18.05.2016
 * Time: 13:04
 */
namespace Web2PrintBlackbit\Events;


class Web2Print {


    public static function modifyProcessingOptions(\Zend_EventManager_Event $event){

        $container = $event->getParam('returnValueContainer');
        $options = $container->getData();
        $container->setData($options);
        return $container;
    }

    public static function modifyConfig(\Zend_EventManager_Event $event){
        $container = $event->getParam('returnValueContainer');
        $config = $container->getData();

        $config["conformance"] = \Conformance::PDFX4;
        $config["outputIntent"] = ['identifier' => "ISO Coated v2 300% (ECI)",'data' => base64_encode(file_get_contents(\PIMCORE_DOCUMENT_ROOT.'/plugins/Windhager/iic-profiles/ISOcoated_v2_300_eci.icc'))];

        if(!$config["title"]){
            $config["title"] = $event->getParam('document')->getKey();
        }

        $container->setData($config);
    }

}