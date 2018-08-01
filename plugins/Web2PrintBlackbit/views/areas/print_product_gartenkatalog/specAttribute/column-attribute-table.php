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


$levels = \Web2Print\Tool::getMaxGroupDepth($this->configArray);

if($this->printDate){
    $printDate = new \Carbon\Carbon($this->printDate);
}

?>


<?php

foreach ($this->configArray as $configElement) {
    $classname = $this->classname;
    $icons = \Web2PrintBlackbit\Helper\Catalog::getIconList();
    $label = $configElement->getLabeledValue(new $classname())->label;
    if(!$label){
        $label = $configElement->getLabel();
    }
    $icon = $icons[strtolower($label)];
    ?>
    <div class="product-table__col">
        <div class="product-table__item product-table__item--head">
            <?php
            $src = $icon['src'] ?: "data:image/svg+xml;charset=utf8,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%3E%3C/svg%3E";
            if(!$icon['src'] ){
               /* var_dump($label);
                var_dump($configElement); Exit;*/
            }
            ?>
            <img class="product-table__header-icon" src="<?= $src ?>" alt="<?= $configElement->getLabel() ?>">
        </div>

        <?php
        /**
         * @var \Web2PrintBlackbit\Product $element
         */

        //var_dump(\Windhager\Exporter\Product\Exports\ProductDatasheet::getIncludeFields());

        foreach ($this->elements as $element) {
            $value = '&nbsp;';


            $outputElement = $configElement->getLabeledValue($element);
            if($configElement->getLabel() == 'Verkaufsgebiet'){ //hack um alt definitionen zu supporten - altes feld war "sales_regions" und wurde offenbar in  "salesRegions" umbenannt
                $configElement = new \Elements\OutputDataConfigToolkit\ConfigElement\Value\DefaultValue((object)['attribute' => 'salesRegions','label' => 'Verkaufsgebiet']);
                $outputElement = $configElement->getLabeledValue($element);
            }

            if ($outputElement->def instanceof \Pimcore\Model\Object\ClassDefinition\Data\Select) {
                $value = $this->t("class_select_value_".$outputElement->value);
            }elseif ($outputElement->def instanceof \Pimcore\Model\Object\ClassDefinition\Data\Multiselect) {
                $value = [];

                foreach($outputElement->def->getOptions() as $option){
                    if(in_array($option['value'],(array)$outputElement->value)){
                        $value[] = $this->t("class_select_value_".$outputElement->def->getName().'_'.$option['key']);
                    }
                }
                $value = implode(', ', $value);
            } elseif ($outputElement->def instanceof \Pimcore\Model\Object\ClassDefinition\Data\Objects) {

                $result = [];
                $objects = $outputElement->value;
                foreach ((array)$objects as $o) {

                    if ($o instanceof \Web2PrintBlackbit\Product) {
                        $result[] = $o->getArt_no();
                    } else {
                        $getter = 'getTitle';
                        if (method_exists($o, $getter)) {
                            $result[] = $o->$getter();
                        }
                    }
                }
                $value = implode(', ', array_filter($result));
            } else {
                $value = $outputElement->value;
            }
            if ($outputElement->def) {
                    $name = $outputElement->def->getName();

                   if ($value) {
                       if ($name == 'thickness') {

                           /**
                            * @var \Pimcore\Model\Object\Objectbrick\Data\PMTAttributeThickness $brick
                            */
                      $brick = $element->getPmtMarketingAttributes()->getPMTAttributeThickness();
                        if ($unit = $brick->getThickness_unit()) {
                            if ($tmp = $unit->getTitle()) {
                                $value .= ' ' . $tmp;
                            }
                        }
                    }

                    if ($name == 'grammage') {
                        $value .= ' g/m²';
                    }

                    if ($name == 'mesh') {
                        $value .= ' mm';
                    }
                    if ($name == 'opaque') {
                        $value .= ' %';
                    }
                }
            }

            if(is_array($value)){
                var_dump($value);
            }

             if ($outputElement->def && $outputElement->def->getName() == 'ean') {
                if(!$this->showNewLogoOnTop){
                    $badge = \Pimcore\Model\Object\Badge::getByPath('/badges/katalog/new-short');
                    if(\Web2PrintBlackbit\Helper\Catalog::inDateRage($this->printDate,$element->getNew_from(),$element->getNew_to())){
                        if($badge->getImage()){
                            $value = $badge->getImage()->getThumbnail()->getHTML([
                                    "class" => "product-table__row-icon"
                                ]) . ' ' . $value;
                        }else {
                            $value = '';
                        }
                    }
                }

            }

            ?>
            <div class="product-table__item">
                <?php

                if($value instanceof \Web2PrintBlackbit\AttributeColor){
                    $value = (string)$value;
                }
                if (is_null($value) || $value === '') {
                    $value = '&nbsp;';
                }

                echo $value;
                ?>
            </div>
            <?
        }
        ?>

    </div>
    <? if($label != 've'){ ?>
    <div class="product-table__spacer">
        <div class="product-table__item product-table__item--head">
            <div class="product-table__header-icon"></div>
        </div>
        <?
        foreach ($this->elements as $element) {
            ?>
            <div class="product-table__item">&nbsp;</div>
        <?
        }
        ?>
    </div>
    <?}?>
<? } ?>