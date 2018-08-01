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
         * @var \Windhager\Product $product
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
                if($this->printDate && !\Windhager\Helper\Catalog::inDateRage($this->printDate,$element->getRelevant_from_catalog(),$element->getRelevant_to_catalog(),'catalog')){
                    unset($elements[$index]);
                }

            }
        }

        $newElements = [];
        foreach($elements as $element){
            if(\Windhager\Helper\Catalog::inDateRage($this->printDate,$element->getNew_from(),$element->getNew_to())){
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

        <div class="product">

            <?
            if($ratio < 4 || count($additionalImages) == 0){?>
                <div class="product-header row--same-height">

                    <div class="product-header__img-wrapper product-header__img-wrapper--max-height" <?php  if ($leftImage) {  $src = (string)$leftImage->getThumbnail($getThumbnailName('catalog-main')); ?> style="background-image: url(<?= $src ?>);" <? } ?>></div>
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
                        $configArray = Windhager\OutputDataConfigToolkit\Service::buildOutputDataConfigForCatalog($this->windhageroutputchanneltable("tableconfig")->getOutputChannel());

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
