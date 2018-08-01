<?php
/**
 * Created by PhpStorm.
 * User: ckogler
 * Date: 03.01.2017
 * Time: 15:44
 */

namespace Web2PrintBlackbit\Helper;

use Pimcore\Model\Object;

class Ldl {

    public static function getLocales(){
        $currentLocale = (string)\Zend_Registry::get('Zend_Locale');
        $locales = array_flip(\Pimcore\Tool::getValidLanguages());
        unset($locales[$currentLocale]);
        if($currentLocale == 'de_DE'){
            unset($locales['de_AT']);
        }
        if($currentLocale == 'de_AT'){
            unset($locales['de_DE']);
        }
        return array_keys($locales);
    }
}