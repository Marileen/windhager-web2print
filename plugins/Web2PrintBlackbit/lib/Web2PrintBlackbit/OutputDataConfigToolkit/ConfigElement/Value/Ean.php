<?php

namespace Web2PrintBlackbit\OutputDataConfigToolkit\ConfigElement\Value;

use Elements\OutputDataConfigToolkit\ConfigElement\Value;

class Ean extends Value\DefaultValue{

    protected $attribute = 'ean';

    public function getLabeledValue($object) {
        $result = parent::getLabeledValue($object);


        if($result->value){
            $value = substr($result->value,0,7).' ';
            $value .= '<span class="strong">' . substr($result->value,7,5).'</span>';
            $value .= ' ' . substr($result->value,12,strlen($value));
            $result->value = $value;
        }
        return $result;
    }

}