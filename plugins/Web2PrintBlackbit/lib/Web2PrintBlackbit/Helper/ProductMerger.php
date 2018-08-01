<?php
/**
 * Created by PhpStorm.
 * User: ckogler
 * Date: 09.01.2017
 * Time: 14:12
 */

namespace Web2PrintBlackbit\Helper;
use Pimcore\Model\Object;

class ProductMerger {

    public static function getEditableLanguages(){
        $allowedLanguages = \Pimcore\Model\Object\Service::getLanguagePermissions(Object\AbstractObject::getByPath('/products'), \Pimcore\Tool\Authentication::authenticateSession(), "lEdit");
        if($allowedLanguages){
            return array_keys($allowedLanguages);
        }
        if(!$allowedLanguages){
            $allowedLanguages = \Pimcore\Tool::getValidLanguages();
        }
        return $allowedLanguages;
    }

    public static function getConfigConsiderPermissions(){
        $config = include \Pimcore\Config::locateConfigFile("product-merger.php");
        $helper = new \Web2PrintBlackbit\Helper\ProductLayout();
        $editableFields = $helper->getEditableFields();

        foreach((array)$config['actions'] as $key => $data){
            $intersection = array_intersect($editableFields,$data['fields']);
            if(empty($intersection)){
                unset($config['actions'][$key]);
            }
        }

        return $config;
    }

}