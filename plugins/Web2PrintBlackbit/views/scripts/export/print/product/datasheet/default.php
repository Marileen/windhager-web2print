<?php
/**
 * Created by PhpStorm.
 * User: Julian Raab
 * Date: 16.02.2017
 * Time: 16:22
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?= $this->title ?: "Export" ?></title>

    <link rel="stylesheet" type="text/css" href="/plugins/Windhager/static6/css/print-style.css" media="all"/>
    <link rel="stylesheet" type="text/css" href="/plugins/Windhager/static6/css/print-edit.css" media="screen"/>
    <link rel="stylesheet" type="text/css" href="/plugins/Windhager/static6/css/datasheet.css" media="all"/>


    <?= $this->headLink() ?>
    <!-- see https://github.com/lindell/JsBarcode -->
    <script type="text/javascript" src="/plugins/Windhager/static6/vendor/barcode/JsBarcode.ean.min.js"></script>

</head>

<body>
<?
if (!$this->pdf){ ?>

<div class="canvas">
    <div class="page">
        <? } ?>

<?php

/* @var $templateConfig \Pimcore\Model\Object\PdfTemplate */
$templateConfig = $this->templateConfig;


/* @var $articles array */
extract($this->displayData);

$validProductIds = (array)$this->validProductIds;



$pricelist = $this->getParam("pricelist");
$year = $this->getParam("year");
$mandant = $this->getParam("mandant");
$region = $this->getParam("region");

if($pricelist && $year && $mandant){
    $row = \Pimcore\Db::get()->fetchRow('select * from windhager_exchange_prices where pricelist_no= ? AND mandant= ? AND year = ?',[$pricelist,$mandant,$year]);
    $pricelistName = $this->translate($row['pricelist_title']);
}




/* @var $product \Web2PrintBlackbit\Product */
$product = $this->product;
$esbType = $product->getEsbType();
?>
    <div id="datasheet" class="">
        <header class="page-header"></header>
        <footer class="footer page-footer">
            <?php if($pricelistName){ ?>
                <?=sprintf($this->t('datasheet_footer_text'),$pricelistName,date('d.m.Y'))?><br/>
            <?php }?>
            <?=$this->t('datasheet_footer_text_2')?>
        </footer>

        <div class="sidebar-wrapper">
            <div class="side-bar">
                <style>
                    .test-wrapper {
                        /*position: absolute;*/
                        /*top: 0;*/
                        /*right: 0;*/
                        width: 100%;
                        /*height: 100%;*/
                        /*border: 1px solid pink;*/

                    }

                    .test {
                        height: auto;
                        width: 48.2mm;
                        /*border: 1px solid blue;*/
                    }

                    .test:before {
                        content: "";
                        top: 2.87cm;
                        position: absolute;
                        left: 0;
                        bottom: 0;
                        border-left: 0.7mm solid #bac63f;
                    }
                </style>
                <div>
                    <div class="page-number">
                        <!--  page numbers are set by css/pdfReactor -->
                        <span class="page-num"></span>/<span class="page-num-total"></span>
                    </div>
                </div>
                <div class="test-wrapper">

                    <?php if ($templateConfig) { ?>
                        <?php if ($image = $templateConfig->getBanner()) { ?>
                            <?= $image->getThumbnail("export-product-datasheet-banner")->getHtml(["class" => "test"]) ?>
                        <?php } ?>
                    <?php } else { ?>
                        <img src="/plugins/Windhager/static6/img/datasheet-bg-green.png" class="test">
                    <?php } ?>

                </div>
                <div class="logo">
                    <img src="/plugins/Windhager/static6/img/windhager-logo.jpg" class="img-responsive img-responsive--vert">
                </div>
                <div class="months-wrapper">
                    <div class="months">
                        <?php
                        $locale = \Zend_Registry::get('Zend_Locale');
                        $months = $locale->getTranslationList('months');
                        $monthlist = $months["format"]["abbreviated"];

                        $from = $product->getSalesDateFrom();
                        $to = $product->getSalesDateTo();

                        if(!$from && !$to){
                            if($articles[0]){
                                $article = \Web2PrintBlackbit\Product::getByArt_no($articles[0]['art_nr'],['limit' => 1,'unpublished' => true]);
                                $from = $article->getSalesDateFrom();
                                $to = $article->getSalesDateTo();
                            }
                        }

                        $isInRange = function ($month) use ($from, $to) {
                            if (!$from || !$to) {
                                return false;
                            }

                            if($from > $to){

                                if($month <= $to){
                                    return true;
                                }
                                if($from <= 12 && $month >= $from){
                                    return true;
                                }

                            }else{
                                return $month >= $from && $month <= $to;
                            }
                        }

                        ?>

                        <div class="months-inner">
                            <?php foreach ($monthlist as $i => $month) { ?>
                                <div class="month-group">
                                    <div class="month-group__month"><?= trim(strtoupper($month), "."); ?></div>
                                    <?
                                    if ($isInRange($i)) { ?>
                                        <div class="month-group__field"></div>
                                    <? } ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="clearfix">
            <div class="product-image-big">
                <?php

                    if($esbType == 'D'){
                        $mainImage = $product->getImagesByField('packing')[0];
                    }else{
                        $mainImage = $product->getImagesByField('in_use')[0];
                    }
                if ($mainImage): ?>
                    <?= $mainImage->getThumbnail("export-product-datasheet-main")->getHtml(["class" => "img-responsive img-responsive--vert"]) ?>
                <?php endif; ?>
            </div>

            <div class="product-info">
                <div class="product-category">
                    <?=$product->getProductrange_datasheet()?>
                </div>
                <div class="product-title">
                    <?= $product->getName(); ?><? if($esbType == 'D'){?> (<?=$product->getArt_no()?>)<?php } ?>
                </div>
                <? if($v = $product->getText()){?>
                    <div class="product-description">
                        <?=$v?>
                    </div>
                <?}?>
                <? if($v = $product->getHighlights()){
                    $v = explode('<br />',$v);
                    ?>
                    <div class="product-description">
                        <ul>
                            <? foreach($v as $e){?>
                                <li><?=$e?></li>
                            <?}?>
                        </ul>
                    </div>
                <?}?>
                <? if($v = $product->getUsp()){?>
                    <div class="product-description">
                        <?=$v?>
                    </div>
                <?}?>

            </div>
        </div>
        <br>

        <div class="small-images clearfix">

            <?
                $showedImageIds = [];
            ?>
            <?php

            if($esbType == 'D'){
                $imageTypes = ['crop','material'];
            }else{
                $imageTypes = ['packing','detail','material'];
            }
            foreach($imageTypes as $type){
            if ($images = $product->getAllImages($type, $product,false)) {
                foreach($images as $image){
                    if($asset = $image->getImage()){
                        if(in_array($asset->getId(),$showedImageIds)){
                            continue;
                        }
                        $showedImageIds[] = $asset->getId();
                    }
                    ?>
                <div class="small-image__item">
                    <?= $image->getThumbnail("export-product-datasheet-default")->getHtml(["class" => "img-responsive img-responsive--vert center-block"]) ?>
                </div>

            <?php }}
            }


            ?>


        </div>


        <?php if ($articles): ?>
            <?php

            $head = array_keys($articles[0]);


            $sums = [];

            /**
             * f.	Bitte auf die Teilung achten, wann eine 2. Seite benötigt wird
            bis vier Zeilen auf einer Seite
            Wenns fünf Tabellenzeilen werden, drei vorne stehen lassen – den Rest auf der nächsten Seite abbilden

             */
            $d = [$articles];

            /*if($_GET['test']){
            }else{

                if(count($articles) <=4){
                    $d = [$articles];
                }else{
                    $first = [];
                    $first[] = array_shift($articles);
                    $first[] = array_shift($articles);
                    $first[] = array_shift($articles);

                    $d = [$first,$articles];
                }
            }*/


            foreach($d as $i => $articles){
            ?>

            <table class="product-table table--full-width <?php if($product->getEsbType() != 'D'){?> regular-article<?}else{?> display-article<?}?>" <? if($i > 0){?> style="page-break-before: always;" <?}?>>
                <thead>
                <tr>
                    <?php foreach ($head as $th) { ?>
                        <th><?= $this->translate("datasheet." . $th) ?></th>
                    <?php } ?>
                </tr>
                </thead>
                <tbody>

                <?php
                foreach ($articles as $x => $articleRow) {

                    ?>
                    <tr>
                        <?php foreach ($articleRow as $param => $value) {

                            if($param == 'amount' && $value){
                                $value = (int)$value;
                            }
                            if(is_numeric($value)){
                                $sums[$param] += $value;
                            }
                            ?>

                            <?php if ($param == "ean") { ?>
                                <!-- for options see https://github.com/lindell/JsBarcode -->
                                <td class="col-ean column-<?=$param?>">
                                    <?php if ($value) { ?>
                                        <canvas class="barcode"
                                                data-background="#fff"
                                                data-height="20"
                                                data-margin="2"
                                                data-padding="0"
                                                data-value="<?= $value ?>"
                                                data-format="EAN13"
                                                data-displayValue="true"
                                                data-width="1.1">
                                        </canvas>
                                    <?php } ?>
                                </td>
                            <?php } else { ?>
                                <? if(in_array($param,['ek','ek_sum','uvp','uvp_sum'])){
                                    if($value){
                                        $value = '<span style="white-space:nowrap;">'.number_format($value,2,'.','').' €</span>';
                                    }
                                    ?>

                                <?} ?>
                                <td  class="column-<?=$param?>">

                                    <?= $value ?></td>
                            <?php } ?>
                        <?php } ?>
                    </tr>
                <?php } ?>

                <?
                if($esbType == 'D' && !$d[$i+1]){?>
                    <tr class="last-row">
                    <?
                    foreach (array_keys($articleRow) as $k) {
                        $v = '';

                        if($k == 'art_nr'){
                            $v = $product->getArt_no();
                        }
                        if($k == 'title'){
                            $v = $product->getName();
                        }
                        if($k == 'amount'){
                            $v = $sums['amount'];
                        }
                        if($k == 'ek_sum' || $k == 'uvp_sum'){
                            if($sums[$k]){
                                $v = number_format($sums[$k],2,'.','').' €';
                            }

                        }
                        ?>
                        <td  class="column-<?=$k?>"><?=$v?></td>
                    <?} ?>
                    </tr>


                <? } ?>
                </tbody>
            </table>
            <?}?>



            <div class="info-text" >
                <?=$this->t('datasheet_uvp_text')?>
            </div>
        <?php endif; ?>
    </div>



        <? if (!$this->pdf){ ?>
    </div>
</div>
<? } ?>
<script type="text/javascript">
    JsBarcode(".barcode").init();
</script>

</body>

</html>
