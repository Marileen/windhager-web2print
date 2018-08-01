<?php
/**
 * Created by PhpStorm.
 * User: ckogler
 * Date: 03.01.2017
 * Time: 15:44
 */

namespace Web2PrintBlackbit\Formatter;

use Pimcore\Model\Object;
use Pimcore\Model\Element\ElementInterface;

class Product {
    /**
     * @param $result array containing the nice path info. Modify it or leave it as it is. Pass it out afterwards!
     * @param ElementInterface $source the source object
     * @param $targets list of nodes describing the target elements
     * @param $params optional parameters. may contain additional context information in the future. to be defined.
     * @return mixed list of display names.
     */
    public static function formatPath($result, ElementInterface $source, $targets, $params)
    {
        /** @var  $fd Data */
        $fd = $params["fd"];
        $context = $params["context"];
        $result = [];
        if($context['fieldname'] == 'main_color'){
            foreach($targets as $key => $e){

                $color = \Pimcore\Model\Object\AttributeColor::getById($e['id']);
                if($color instanceof Object\AttributeColor){
                    $result[$key] = ($color->getTitle() ?: $color->getFullPath()) .' - Code: ' . ($color->getCode() ?: 'n/a');
                }
            }
        }

        return $result;
    }

}