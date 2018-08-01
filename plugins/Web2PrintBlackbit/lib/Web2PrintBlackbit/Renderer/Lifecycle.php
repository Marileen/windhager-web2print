<?php
/**
 * Created by PhpStorm.
 * User: ckogler
 * Date: 26.09.2017
 * Time: 13:24
 */
namespace Web2PrintBlackbit\Renderer;

use Pimcore\Model\Object\Concrete;

class Lifecycle
{
    /**
     * @param $data string as provided in the class definition
     * @param $object Concrete
     * @param $params mixed
     * @return string
     */
    public static function renderLayoutText($data, $object, $params) {

        $s = '<b>Aktueller Lebenszyklus: </b>';

        if($item = $object->getLifecycleCalculated()){
          $s .= ' ' . ($item->getTitle() ?: $item->getKey());
        }else{
            $s .= ' - ';
        }

        return $s;
    }
}