<?php
if(!class_exists('ean13')) {
    include_once(__DIR__.'/../../../vendor/ean-13/ean13class.php');
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
                ?>

                    <div class="content-2col-container-110">

                <?


                // Elements / Produktvariationen
                $elementIndex = 0;


                foreach ($elements as $element) {

                    $artNumber = '<br>'; //Artikelnummer
                    $artEAN = '<br>'; //EAN
                    $artShape = '';
                    $artVE = ''; //Verpackungseinheiten
                    $customFields = [];


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
                            case 'art_no':
                                $artNumber = $outputElement->value;
                                break;
                            case 'ean':
                                $artEAN = $outputElement->value;
                                break;
                            case 've' :
                                $artVE = $outputElement->value;
                                break;
                            default:
                                if(count($customFields) < 2) {
                                    $customFields[] = ['label' => $label, 'value' => $outputElement->value];
                                }
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
                                <img src="/pim-icons/gartenkatalog-2019-svg/VE_Karton.svg"/>
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
                                    <div class="item-first-line">
                                        <?php if(isset($customFields[0])) {
                                            echo $customFields[0]['value'];
                                        }?>
                                    </div>
                                    <div class="item-second-line">
                                        <?php if(isset($customFields[1])) {
                                            echo $customFields[1]['value'];
                                        }?>
                                    </div>
                                </div>
                            </td>
                            <td class="col-color-form">
                                <div class="color-form-sample-square">
                                    <? if ($artShape != '') {
                                        echo 'Form';

                                        } elseif (count($additionalImages) > 0) {

                                        if (isset($additionalImages[$elementIndex])) {
                                        ?>

                                        <img src="<?= $additionalImages[$elementIndex] ?>" alt=""/>

                                    <? }
                                    } ?>
                                </div>
                            </td>
                            <td class="col-ean">
                                <div class="ean"><?php
                                    $artEAN = preg_replace('/[^0-9]/', '', $artEAN);
                                    $ean13 = new ean13;
                                    $ean13->article = $artEAN;   // initial article code
                                    $ean13->article .= $ean13->generate_checksum();   // add the proper checksum value

                                    ?><?= $ean13->codestring() ?></div>
                            </td>
                            <td class="col-packaging">
                                <div class="number-of-pieces">
                                    <div class="item-text-container"><?=$artVE?></div>
                                </div>
                                <div class="packaging-unit"></div>
                                <div class="packaging new-logo">
                                    <?php if(\Web2PrintBlackbit\Helper\Catalog::inDateRage($this->printDate,$element->getNew_from(),$element->getNew_to())){
                                        ?>
                                        <img class="" src="/plugins/Web2PrintBlackbit/static/img/N_Neu.svg"  alt="">
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
