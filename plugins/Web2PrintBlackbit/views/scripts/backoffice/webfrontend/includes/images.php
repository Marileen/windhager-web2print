<?php

/**
 * @var \Pimcore\Model\Object\ClassDefinition\Data $element
 * @var \Web2PrintBlackbit\Product $product
 */
$product = $this->product;
$tabArray = $this->tabArray;
$key = $this->key;
$result = [];
foreach($tabArray->getChildren() as $child){
    $name = $child->getName();

    foreach($child->getChildren() as $c){

        foreach($c->getChildren() as $entry){

            $getter = 'get' . ucfirst($entry->getName());
            $items = $product->$getter();
            foreach((array)$items as $block){
                $image = $block['image']->getData();
                if($image && $image->getImage()){
                    $result[$name][$c->getName()][$entry->getTitle()][]  = $block;
                }
            }

        }
    }
}

if($result){
?>

<div class="panel panel-default">
    <div class="panel-heading detail-expand" role="tab" id="headingFirst">
        <h4 class="panel-title">
            <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse<?=$key?>" aria-controls="collapse<?=$key?>">
                <?=$this->t('backoffice_label_'.$key)?>
            </a>
        </h4>
    </div>
    <div id="collapse<?=$key?>" class="panel-collapse collapse " role="tabpanel" aria-labelledby="heading<?=$key?>">
        <div class="panel-body">

            <?

            foreach ($result as $cat => $x){

                ?>
                <h4><?=$cat?></h4>

                <? foreach($x as $subCat => $types){
                    foreach($types as $type => $blocks) {?>

                        <?
                        foreach ($blocks as $block) {


                        }
                    }
                    ?>


                    <h5><?=$subCat?></h5>

                    <div class="row image-row">
                    <? foreach($types as $type => $blocks) {
                        foreach ($blocks as $block) {
                            ?>
                            <div class="col-xs-2">
                                <div class="image-image">
                                    <?
                                    $image = $block['image']->getData();
                                    ?>
                                    <img src="<?=$image->getThumbnail('shop-cart-list')?>" alt="" />
                                </div>
                                <div class="image-type">
                                    <?=$this->t('backoffice_label_col_type')?>: <?=$type ?: '-'?>
                                </div>
                                <? if($block['format']){?>
                                    <div class="image-format">
                                        <?=$this->t('backoffice_label_col_format')?>: <?=$block['format']->getData() ?: '-'?>
                                    </div>
                                <?}?>
                                <div class="image-download">
                                    <a href="<?=$this->document->getProperty('downloadPage')?>?id=<?=$image->getImage()->getId()?>" class="btn btn-default"><span class="glyphicon glyphicon-download"></span> <?=$this->t('backoffice_label_download')?></a>
                                </div>
                            </div>
                            <?
                        }
                    }
                    ?>
                    </div>

                    <?
                    if(false){?>
                    <table class="table table-striped">
                        <tr>
                            <th><?=$this->t('backoffice_label_col_image')?></th>
                            <th><?=$this->t('backoffice_label_col_type')?></th>
                            <th><?=$this->t('backoffice_label_col_format')?></th>
                            <th><?=$this->t('backoffice_label_col_download')?></th>
                        </tr>
                        <? foreach($types as $type => $blocks){
                            foreach($blocks as $block){
                            ?>

                            <tr>
                                <td width="170">
                                    <?
                                    $image = $block['image']->getData();
                                    ?>
                                    <img src="<?=$image->getThumbnail('shop-cart-list')?>" alt="" />
                                </td>
                                <td><?=$type?></td>
                                <td><?=$block['format']->getData()?></td>
                                <td width="120">
                                    <a href="<?=$this->document->getProperty('downloadPage')?>?id=<?=$image->getImage()->getId()?>" class="btn btn-default"><span class="glyphicon glyphicon-download"></span> <?=$this->t('backoffice_label_download')?></a>

                                </td>
                            </tr>
                        <? }
                        }?>
                    </table>
                        <?}?>
                    <?}?>
                <?php
            }
            ?>
        </div>
    </div>
</div>

<? } ?>