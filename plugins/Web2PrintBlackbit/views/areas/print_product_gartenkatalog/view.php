<?php
if(!class_exists('Barcode')) {
    include(__DIR__.'/../../../vendor/php-barcode/barcode.php');
}
if ($this->editmode) { ?>
    Produkte:

    <?

    echo $this->windhageroutputchanneltable("tableconfig", ['title' => 'Produkte']);


    ?>

    <div>
        <?= $this->checkbox("showAllVariants"); ?> Alle Varianten anzeigen
    </div>
<? } else {



    $getThumbnailName = function ($thumbnailName){
        if(!$this->printermarks){
            return $thumbnailName.'-web';
        }else{
            return $thumbnailName;
        }
    };

    foreach ($this->windhageroutputchanneltable("tableconfig")->getElements() as $product) {
        /**
         * @var \Web2PrintBlackbit\Product $product
         */

        $displayedAssetIds = [];

        $leftImage = $product->getImagesByField('packing')[0];

        if ($leftImage) {
            $displayedAssetIds[] = $leftImage->getImage()->getId();
        }

        /** @var Pimcore\Model\Object\Data\Hotspotimage[] $catalogImages */
        $catalogImages = [];
        $block = $product->getImages_in_use_catalog()?:[];
        foreach($block as $blockElement) {
            $catalogImages[] = $blockElement['image']->getData();
        }
        $rightImage = $catalogImages[0];

        if ($rightImage && $rightImage->getImage()) {
            $displayedAssetIds[] = $rightImage->getImage()->getId();
        }

        $additionalImages = [];

        foreach ($product->getImagesByField('material') as $hotspotImage) {

            if (!in_array($hotspotImage->getImage()->getId(), $displayedAssetIds)) {
                $additionalImages[] = $hotspotImage->getThumbnail($getThumbnailName('catalog-small'));
                $displayedAssetIds[] = $hotspotImage->getImage()->getId();
            }
        }

        $list = new \Pimcore\Model\Object\Product\Listing();
        $list->setCondition("o_parentId = ?", $product->getId());
        $list->setOrderKey("IF(sortCatalog,sortCatalog,CAST(art_no AS SIGNED))",false);
        $list->setOrder("ASC");
        $elements = $list->load();
        #  $elements = $product->getChildren();

        /**
         * @var \Pimcore\Model\Object\Product $element
         */
        $showAll = $this->checkbox("showAllVariants")->getValue();

        if (!$showAll) {

            foreach ($elements as $index => $element) {
                if($this->printDate && !\Web2PrintBlackbit\Helper\Catalog::inDateRage($this->printDate,$element->getRelevant_from_catalog(),$element->getRelevant_to_catalog(),'catalog')){
                    unset($elements[$index]);
                }

            }
        }

        $newElements = [];
        foreach($elements as $element){
            if(\Web2PrintBlackbit\Helper\Catalog::inDateRage($this->printDate,$element->getNew_from(),$element->getNew_to())){
                $newElements[] = $element;
            }
        }

        $showNewLogoOnTop = false;

        if(count($elements) == count($newElements)){
            $showNewLogoOnTop = true;
        }

        $ratio = 1;
        if($leftImage){
            $dimensions = $leftImage->getThumbnail($getThumbnailName('catalog-main'))->getDimensions();
            $ratio = round($dimensions['height']/$dimensions['width'],1);
        }

        #$leftImage = null; $rightImage = null;
        ?>


    <div class="article-item">
        <h2>
            <?= $product->getName(); ?>
        </h2>

        <!-- CONTENT -->
        <div class="content-container">

            <!--                        Anwenderbild -->
            <?php
            if ($rightImage) {
                //todo: anderes Thumbnail nehmen/erstellen
                $src = (string)$rightImage->getThumbnail($getThumbnailName('catalog-right'));
                ?>

                <div class="content-image" style="background-image: url('<?= $src ?>')">
                <!--<img src="assets/white-corners-top-left-bottom-right.svg">-->
                <svg version="1.1" id="Ebene_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                     viewBox="0 0 311.8 161.6" style="enable-background:new 0 0 311.8 161.6;" xml:space="preserve">
                        <style type="text/css">
                            .st0 {
                                fill: #FFFFFF;
                                stroke: none
                            }
                        </style>
                    <g>
                        <path class="st0" d="M296.1,161.6h15.7v-15.7C311.8,161.6,296.1,161.6,296.1,161.6z"/>
                        <path class="st0" d="M15.7,0H0v15.7C0,0,15.7,0,15.7,0z"/>
                    </g>
                        </svg>
                </div>
                <?
            }
            ?>

            <?php

                $configArray = array();
                if ($this->windhageroutputchanneltable("tableconfig")->getOutputChannel()) {
                    $configArray = \Web2PrintBlackbit\OutputDataConfigToolkit\Service::buildOutputDataConfigForCatalog($this->windhageroutputchanneltable("tableconfig")->getOutputChannel());
                }

//                foreach ($configArray as $configElement) {
//
//                    $classname = "Object_" . $this->windhageroutputchanneltable("tableconfig")->getSelectedClass();
//                    $icons = \Web2PrintBlackbit\Helper\Catalog::getIconList();
//                    $label = $configElement->getLabeledValue(new $classname())->label;
//                    if (!$label) {
//                        $label = $configElement->getLabel();
//                    }
//
//                    var_dump($label);
//                }
                ?>

                    <div class="content-2col-container-110">

                <?


                // Elements / Produktvariationen
                $elementIndex = 0;


                foreach ($elements as $element) {

                    $artNumber = '<br>'; //Artikelnummer
                    $artEAN = '<br>'; //EAN
                    $artDimensions = '';    //Länge Breite Höhe, Einheit
                    $artDiameter = '';    //Durchmesser
                    $artShape = '';
                    $artVE = ''; //Verpackungseinheiten


                    //foreach configArray -> Daten die als Output Channel Data angegeben sind + immer am Anfang die EAN und am Ende die VE
                    foreach ($configArray as $configElement) {

                        $classname = "Object_" . $this->windhageroutputchanneltable("tableconfig")->getSelectedClass();
                        $icons = \Web2PrintBlackbit\Helper\Catalog::getIconList();
                        $label = $configElement->getLabeledValue(new $classname())->label;
                        if (!$label) {
                            $label = $configElement->getLabel();
                        }
                        $icon = $icons[strtolower($label)];

                        $value = '&nbsp;';

                        $outputElement = $configElement->getLabeledValue($element);

                        switch ($label) {
                            case 'ean' :
                                $artEAN = $outputElement->value;
                                break;
                            case 'Länge (Tiefe)':
                                $artDimensions .= $outputElement->value . ' ';
                                break;
                            case 'Breite':
                                $artDimensions .= 'x ' . $outputElement->value . ' ';
                                break;
                            case 'Höhe':
                                $artDimensions .= 'x ' .$outputElement->value . ' ';
                                break;
                            case 'Größe Einheit':
                                $artDimensions .= $outputElement->value;
                                break;
                            case 'Durchmesser':
                                $artDiameter .= $outputElement->value;
                                break;
                            case 'Form' :
                                $artShape = $outputElement->value;
                                break;
                            case 've' :
                                $artVE = $outputElement->value;
                                break;
                        }

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

                                if ($o instanceof \Windhager\Product) {
                                    $result[] = $o->getArt_no();
                                } else {
                                    $getter = 'getTitle';
                                    if (method_exists($o, $getter)) {
                                        $result[] = $o->$getter();
                                    }
                                }
                            }
                            $value = implode(', ', array_filter($result));
                            //$artNo = implode(', ', array_filter($result));
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
                                if(\Windhager\Helper\Catalog::inDateRage($this->printDate,$element->getNew_from(),$element->getNew_to())){
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



                    } //foreach configArray

                    ?>

                    <table class="info-table-item">
                        <thead>
                        <tr>
                            <td class="col-item-number">Nr./Dim</td>
                            <td class="col-color-form"><? if ($artShape != '') { echo 'Form'; } else { echo 'Farbe';} ?></td>
                            <td class="col-ean">EAN</td>
                            <td class="col-packaging">
                                <img src="/gartenkatalog-assets-blackbit-juli-2018/Icons/VE_Karton.svg"/>
                            </td>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="col-item">

                                <div class="item-text-container">
                                    <div class="item-number">
                                        <?= $element->getId() ?>
                                    </div>
                                    <div class="item-dimension">
                                        <?=$artDimensions?>
                                    </div>
                                    <div class="item-diameter">
                                        <?=$artDiameter?>
                                    </div>
                                </div>
                            </td>
                            <td class="col-color-form">
                                <div class="color-form-sample-square">
                                    <? if ($artShape != '') {
                                        echo 'Form';

                                        } elseif (count($additionalImages) > 0) { ?>

                                        <img src="<?= $additionalImages[$elementIndex] ?>" alt=""/>

                                    <? } ?>
                                </div>
                            </td>
                            <td class="col-ean">
                                <div class="ean"><?php
                                    $artEAN = preg_replace('/[^0-9]/', '', $artEAN);
                                    $barcode = new Barcode($artEAN, 2);
                                    ob_start ();

                                    imagepng ($barcode->image());
                                    $image_data = ob_get_clean();

                                    $image_data_base64 = base64_encode ($image_data);
                                    ?><img src="data:image/png;base64, <?= $image_data_base64 ?>" /> </div>
                            </td>
                            <td class="col-packaging">
                                <div class="number-of-pieces">
                                    <div class="item-text-container"><?=$artVE?></div>
                                </div>
                                <div class="packaging-unit"></div>
                                <div class="packaging">
                                    <?php if($showNewLogoOnTop){    //todo: anderes neu-Icon setzen
                                        $badge = \Pimcore\Model\Object\Badge::getByPath('/badges/katalog/new-long');
                                        ?>
                                        <img class="" src="<?=$badge->getImage()?>"  alt="">
                                    <?}?>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>


                    <?
                    $elementIndex++;
                } //END foreach ($elements as $element)
                ?>

            </div>


            <div class="content-sidebar-image">
                <!--<img src="http://www.windhager.eu/typo3temp/thumbs/produkt_freisteller_292309_1b41a7509906846-mit-Banderole.png">-->
                <!--<img src="http://www.windhager.eu/fileadmin/user_upload/images/products/detailimages/06851-06852-Anzucht-Quelltabs-.jpg">-->

                <?php  if ($leftImage) {
                    //todo: evtl anderes thumbnail setzen
                    $src = (string)$leftImage->getThumbnail($getThumbnailName('catalog-main'));
                    ?>
                    <img src="<?= $src ?>">
                <? } ?>
            </div>
        </div>


        <!-- SIDEBAR -->
        <div class="sidebar-container">

        <?php
        $values = array_filter(explode("\n",$product->getHighlights()));

        if($values){?>
            <ul class="sidebar-bulletpoint-list">
                <? foreach($values as $v){?>
                    <li><?=$v?></li>
                <? } ?>
            </ul>
        <?}?>
        </div>
    </div>


    <?php } ?>
<? } ?>
