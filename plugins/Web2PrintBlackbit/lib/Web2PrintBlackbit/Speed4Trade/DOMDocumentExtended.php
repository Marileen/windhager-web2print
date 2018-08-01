<?php
/**
 * Created by PhpStorm.
 * User: ckogler
 * Date: 05.12.2017
 * Time: 14:18
 */

namespace Web2PrintBlackbit\Speed4Trade;

class DOMDocumentExtended extends \DOMDocument
{

    public function createElement($name, $value = null)
    {
        if ($value && is_string($value)) {
            $value = $this->createTextNode($value);
            $result = parent::createElement($name);
            $result->appendChild($value);
        } else {
            $result = parent::createElement($name, $value);
        }

        return $result;
    }

}