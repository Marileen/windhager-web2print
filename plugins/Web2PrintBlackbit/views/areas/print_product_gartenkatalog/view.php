<?php if ($this->editmode) { ?>
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

            <div class="content-2col-container-110">


            <?php

                $configArray = array();
                if ($this->windhageroutputchanneltable("tableconfig")->getOutputChannel()) {
                    $configArray = \Web2PrintBlackbit\OutputDataConfigToolkit\Service::buildOutputDataConfigForCatalog($this->windhageroutputchanneltable("tableconfig")->getOutputChannel());
                }

                foreach ($configArray as $configElement) {
                    $classname = "Object_" . $this->windhageroutputchanneltable("tableconfig")->getSelectedClass();
                    $icons = \Web2PrintBlackbit\Helper\Catalog::getIconList();
                    $label = $configElement->getLabeledValue(new $classname())->label;
                    if (!$label) {
                        $label = $configElement->getLabel();
                    }
                    $icon = $icons[strtolower($label)];

                    //... todo
                    //$src = $icon['src'] ?: "data:image/svg+xml;charset=utf8,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%3E%3C/svg%3E";
                    ?>

<!--                    <img class="product-table__header-icon" src="--><?//= $src ?><!--" alt="--><?//= $configElement->getLabel() ?><!--">-->

                    <?
                }
            ?>

                <? if(count($additionalImages) > 0){?>

                        <? foreach($additionalImages as $img){?>
                            <div class="product-header__material__item" >
                                <img src="<?= $img ?>" alt=""/>
                            </div>
                            <?
                        }?>
                    <? foreach($additionalImages as $img){?>
                        <table class="info-table-item">
                        <thead>
                        <tr>
                            <td class="col-item-number">Nr./Dim</td>
                            <td class="col-color-form">Farbe/Form</td>
                            <td class="col-ean">EAN</td>
                            <td class="col-packaging">
                                <img src="assets/icons/VE_Karton.svg"/>
                            </td>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="col-item">

                                <div class="item-text-container">
                                    <div class="item-number">05509</div>
                                    <div class="item-dimension">Abmessung</div>
                                    <div class="item-diameter">Durchmesser</div>
                                </div>
                            </td>
                            <td class="col-color-form">
                                <div class="color-form-sample-square" style="background-color: #0000cc">
                                    <?= $img ?>
                                </div>
                            </td>
                            <td class="col-ean">
                                <div class="ean">abcdef</div>
                            </td>
                            <td class="col-packaging">
                                <div class="number-of-pieces">
                                    <div class="item-text-container">2</div>
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
                    <? } ?>

                <? } ?>

                <!-- thead is only displayed for the first two items not matter if they are separate or followed by a tbody-->

                <table class="info-table-item">
                    <thead>
                    <tr>
                        <td class="col-item-number">Nr./Dim</td>
                        <td class="col-color-form">Farbe/Form</td>
                        <td class="col-ean">EAN</td>
                        <td class="col-packaging">
                            <img src="assets/icons/VE_Karton.svg"/>
                        </td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td class="col-item">

                            <div class="item-text-container">
                                <div class="item-number">05509</div>
                                <div class="item-dimension">Abmessung</div>
                                <div class="item-diameter">Durchmesser</div>
                            </div>
                        </td>
                        <td class="col-color-form">
                            <div class="color-form-sample-square" style="background-color: #0000cc">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                     viewBox="0 0 26.01 26.01"><title>Element 1</title>
                                    <g id="Ebene_2" data-name="Ebene 2">
                                        <g id="Ebene_1-2" data-name="Ebene 1">
                                            <image width="26" height="26"
                                                   xlink:href="data:image/png;base64,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"/>
                                        </g>
                                    </g>
                                </svg>
                            </div>
                        </td>
                        <td class="col-ean">
                            <div class="ean">abcdef</div>
                        </td>
                        <td class="col-packaging">
                            <div class="number-of-pieces">
                                <div class="item-text-container">2</div>
                            </div>
                            <div class="packaging-unit"></div>
                            <div class="packaging"><img src="assets/icons/N_Neu.svg"/></div>
                        </td>
                    </tr>
                    </tbody>
                </table>

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

        <div class="product">

            <?
            if($ratio < 4 || count($additionalImages) == 0){?>
                <div class="product-header row--same-height">

                    <div class="product-header__img-wrapper product-header__img-wrapper--max-height"
                        <?php  if ($leftImage) {  $src = (string)$leftImage->getThumbnail($getThumbnailName('catalog-main')); ?> style="background-image: url(<?= $src ?>);" <? } ?>>

                    </div>
                    <div class="product-header__info-wrapper" style="">
                        <table class="product-header__title" style="   ">
                            <tbody>
                            <tr>
                                <td><h3><?= $product->getName(); ?></h3></td>
                                <td class="product-header__icon-wrapper">
                                    <?php if($showNewLogoOnTop){
                                        $badge = \Pimcore\Model\Object\Badge::getByPath('/badges/katalog/new-long');
                                        ?>

                                        <div class="product-header__icon">
                                            <img class="product-header__icon-img" src="<?=$badge->getImage()?>"  alt="">
                                        </div>
                                    <?}?>
                                </td>
                            </tr>

                            </tbody>
                        </table>

                        <div class="product-header__description clearfix">
                            <!--                        Anwenderbild -->
                            <?php
                            if ($rightImage) {
                                $src = (string)$rightImage->getThumbnail($getThumbnailName('catalog-right'));
                                ?>
                                <div class="anwenderbild">
                                    <img src="<?= $src ?>" class="embed-responsive-item big">

                                    <? if(count($additionalImages) > 0 && count($additionalImages) <=3){?>

                                        <div class="image-row-material">
                                            <?
                                            $i = 0;
                                            foreach($additionalImages as $img){?>
                                                <div class="product-header__material__item" >
                                                    <img src="<?= $img ?>" alt=""/>
                                                </div>
                                                <?
                                                $i++;
                                            }?>
                                        </div>
                                    <? } ?>
                                </div>
                                <?
                            }
                            ?>
                            <?

                            $values = array_filter(explode("\n",$product->getHighlights()));

                            if($values){?>
                                <ul>
                                    <? foreach($values as $v){?>
                                        <li><?=$v?></li>
                                    <? } ?>
                                </ul>
                            <?}?>

                        </div>


                    </div>

                </div>

                <?php

                if(count($additionalImages) > 3 || !$rightImage) { ?>

                    <? $rows = array_chunk($additionalImages, 9);



                    for ($i = 0; $i <= 1; $i++) {
                        $imgs = $rows[$i];
                        if ($imgs) { ?>
                            <div class="product-header row--same-height">

                                <div class="product-header__img-placehoder" style="">
                                </div>
                                <div class="product-header__info-wrapper" style="">
                                    <div
                                        class="product-header__material clearfix" >
                                        <? foreach ($imgs as $img) {
                                            ?>
                                            <div class="product-header__material__item">
                                                <img src="<?= $img ?>" alt=""/>
                                            </div>
                                            <?
                                        } ?>
                                    </div>
                                </div>
                            </div>
                            <?
                        }

                    }

                    ?>


                    <?
                }
                ?>
            <?}else{?>

                <div class="product-header row--same-height">
                    <div class="product-header__img-wrapper" <?php  if ($leftImage) {  $src = (string)$leftImage->getThumbnail($getThumbnailName('catalog-main')); ?> style="background-image: url(<?= $src ?>);" <? } ?>></div>

                    <div class="product-header__info-wrapper" style="">

                        <table class="product-header__title">
                            <tr>
                                <td>
                                    <h3><?= $product->getName(); ?></h3>

                                </td>
                                <td class="product-header__icon-wrapper">
                                    <?php if($showNewLogoOnTop){
                                        $badge = \Pimcore\Model\Object\Badge::getByPath('/badges/katalog/new-long');
                                        ?>

                                        <div class="product-header__icon">
                                            <img class="product-header__icon-img" src="<?=$badge->getImage()?>"  alt="">
                                        </div>
                                    <?}?>

                                </td>
                            </tr>
                        </table>
                        <div class="product-header__description clearfix">
                            <?php
                            if ($rightImage) {
                                $src = (string)$rightImage->getThumbnail($getThumbnailName('catalog-right'));
                                ?>
                                <div class="anwenderbild">
                                    <img src="<?= $src ?>" class="embed-responsive-item big">

                                    <? if(count($additionalImages) > 0 && count($additionalImages) <=3){?>

                                        <div class="image-row-material">
                                            <?
                                            $i = 0;
                                            foreach($additionalImages as $img){?>
                                                <div class="product-header__material__item" >
                                                    <img src="<?= $img ?>" alt=""/>
                                                </div>
                                                <?
                                                $i++;
                                            }?>
                                        </div>
                                    <? } ?>
                                </div>
                                <?
                            }
                            ?>
                            <?

                            $values = array_filter(explode("\n",$product->getHighlights()));

                            if($values){?>
                                <ul>
                                    <? foreach($values as $v){?>
                                        <li><?=$v?></li>
                                    <? } ?>
                                </ul>
                            <?}?>

                        </div>

                        <?php

                        if(count($additionalImages) > 3 || !$rightImage) {
                            $rows = array_chunk($additionalImages, 9);



                            for ($i = 0; $i <= 1; $i++) {
                                $imgs = $rows[$i];
                                if ($imgs) { ?>
                                    <div
                                        class="product-header__material clearfix" >
                                        <? foreach ($imgs as $img) {
                                            ?>
                                            <div class="product-header__material__item">
                                                <img src="<?= $img ?>" alt=""/>
                                            </div>
                                            <?
                                        } ?>
                                    </div>
                                    <?
                                }

                            }
                        }
                        ?>
                    </div>
                </div>
            <?}?>




            <div class="product-table">
                <?php


                if ($elements) {
                    ?>

                    <?php
                    $configArray = array();
                    if ($this->windhageroutputchanneltable("tableconfig")->getOutputChannel()) {
                        $configArray = Web2PrintBlackbit\OutputDataConfigToolkit\Service::buildOutputDataConfigForCatalog($this->windhageroutputchanneltable("tableconfig")->getOutputChannel());

                    }
                    ?>
                    <?= $this->partial("/specAttribute/column-attribute-table.php",
                        array("configArray" => $configArray,
                            "classname" => "Object_" . $this->windhageroutputchanneltable("tableconfig")->getSelectedClass(),
                            "elements" => $elements,
                            "printDate" => $this->printDate,
                            "showNewLogoOnTop" => $showNewLogoOnTop
                        )
                    ); ?>

                    <?
                }
                ?>
            </div>
        </div>
    <?php } ?>
<? } ?>