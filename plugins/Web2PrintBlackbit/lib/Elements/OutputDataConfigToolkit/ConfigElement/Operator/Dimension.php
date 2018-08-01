<?php
/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @category   Pimcore
 * @package    EcommerceFramework
 * @copyright  Copyright (c) 2009-2016 pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */


namespace Elements\OutputDataConfigToolkit\ConfigElement\Operator;

use PhpUnitsOfMeasure\PhysicalQuantity;

class Dimension extends AbstractOperator {

    protected $config;
    protected $fields = ['item_length','item_width','item_height'];

    public function __construct($config, $context = null) {
        parent::__construct($config, $context);
        $this->config = $config;
    }

    public function getLabeledValue($object)
    {
        $result = new \stdClass();
        $result->label = $this->label;

        /**
         * @var \Elements\OutputDataConfigToolkit\ConfigElement\Value\Numeric $item
         * @var \Windhager\Product $object
         */
        $formattedValues = [];

        $config = (array)$this->config->data;

        $result->label = $config['label'];
        if($object->getId()){
            $sourceUnit = $object->getSize_unit();
            foreach($this->fields as $field){
                if($config['showItem_'.$field]){

                    $getter = "get" . ucfirst($field);
                    $value = $object->$getter();
                    if($value){
                        $targetUnit = $config['targetUnit_' . $field];
                        if($targetUnit && $sourceUnit && $sourceUnit->getTitle()){
                            $entry = new PhysicalQuantity\Length($value,$sourceUnit->getTitle());
                            $value = $entry->toUnit($targetUnit);
                        }
                        $value = str_replace('.',',',$value);

                        if($config['showUnit_'.$field]){


                            $value .= $config['concatenator_'.$field];

                            if($targetUnit){
                                $value .= $targetUnit;
                            }else{
                                if($sourceUnit){
                                    $value .= $sourceUnit->getTitle();
                                }
                            }
                        }
                    }
                    if($value){
                        $formattedValues[] = $value;
                    }
                }
            }
        }

        $formattedValue = implode($config['concatenator'] ?: '',$formattedValues);

        $result->value = $formattedValue;
        if(!$result->value){
            $result->empty = true;
        }
        return $result;
    }

}
