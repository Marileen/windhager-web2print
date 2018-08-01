<?php
/**
 * Created by PhpStorm.
 * User: ckogler
 * Date: 18.05.2016
 * Time: 13:04
 */
namespace Web2PrintBlackbit\Events;

use Pimcore\Model\Object;


class Objects {


    public static function preSendData(\Zend_EventManager_Event $event){

        $object = $event->getParam('object');

        $df = $object->getClass()->getLayoutDefinitions();
        $childs = $df->getChildren();
        foreach($childs as $c){
            self::modifyValue($c);
        }
    }

    protected static function modifyValue($c){

        if(method_exists($c,'setAssetUploadPath')){
            if(!$c->getAssetUploadPath()){
                $c->setAssetUploadPath('/uploads');
            }
        }

        if(method_exists($c,'getChildren')){
            if($c2 = $c->getChildren()){
                foreach($c2 as $x){
                    self::modifyValue($x);
                }
            }
        }

    }
}