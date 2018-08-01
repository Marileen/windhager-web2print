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
?>
    <div id="datasheet" class="display-set">
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
                        };

                        ?>

                        <div class="months-inner">
                            <?php foreach ($monthlist as $i => $month) { ?>
                                <div class="month-group">
                                    <div class="month-group__month"><?= trim(strtoupper($month), "."); ?></div>
                                    <? if ($isInRange($i)) { ?>
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
                    $mainImage = $product->getImagesByField('packing')[0];
                    if ($mainImage): ?>
                    <?= $mainImage->getThumbnail("export-product-datasheet-main")->getHtml(["class" => "img-responsive img-responsive--vert"]) ?>
                <?php endif; ?>
            </div>

            <div class="product-info">
                <div class="product-category">
                    <?=$product->getProductrange_datasheet()?>
                </div>
                <div class="product-title">
                    <?= $product->getName(); ?>
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

            $imageTypes = ['material','detail'];
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

        <?php

            $setArticles = $product->getDisplaySet();
            $includeFields = explode(",", $this->getParam("includeFields",''));

            $result = [];
            $sumCols = [];

            if(in_array('EK',$includeFields)){
                $sumCols[] = 'ek_sum';
            }
            if(in_array('UVP',$includeFields)){
                $sumCols[] = 'uvp_sum';
            }

            foreach($setArticles as $setArticle){

                $displayData = \Web2PrintBlackbit\Webservice\ProductDisplay::getDisplayContents(
                    $setArticle,
                    $this->getParam("pricelist"),
                    $this->getParam("year"),
                    $this->getParam("mandant"),
                    $this->getParam("region"),
                    $includeFields
                );

                $setArticleData = [];

                foreach($displayData['articles'] as $e){
                    foreach($sumCols as $field){
                        if($e[$field]){
                            $setArticleData[$field] += $e[$field];
                        }
                    }
                }
                $result[] = ['setArticle' => $setArticle,'setArticleData' => $setArticleData , 'articles' => $displayData['articles']];
            }


        ?>

        <?php if($result){
            ?>
        <table class="product-table table--full-width maintable">
            <thead>
            <tr>
                <th><?= $this->translate("datasheet.art_nr") ?></th>
                <th><?= $this->translate("datasheet.title") ?></th>
                <?
                foreach($sumCols as $c){
                    ?>
                        <th><?=$this->t('datasheet.'.$c)?></th>
                    <?
                }
                ?>
                <th><?= $this->translate("datasheet.ean") ?></th>
            </tr>
            </thead>
            <tbody>
            <?php

            $sums = [];
            foreach ($result as $e){
                /**
                 * @var \Web2PrintBlackbit\Product $setArticle
                 */
                $setArticle = $e['setArticle'];
                ?>
                <tr>
                    <td><?=$setArticle->getArt_no()?></td>
                    <td><?=$setArticle->getTitle()?></td>
                    <?
                    foreach($sumCols as $c){
                       # if(array_key_exists($c,$e['setArticleData'])){
                            $sums[$c] += $e['setArticleData'][$c];
                        $l = str_replace('_sum','',$c);
                            ?>
                            <td style="text-align: right"><?=number_format($e['setArticleData'][$c],2,'.','')?> €</td>
                        <?
                        #}
                    }
                    ?>
                    <td class="col-ean column-ean">
                        <?php if ($value = $setArticle->getEan()) { ?>
                            <canvas class="barcode"
                                    data-background="#fff"
                                    data-height="20"
                                    data-margin="2"
                                    data-padding="0"
                                    data-value="<?= $value?>"
                                    data-format="EAN13"
                                    data-displayValue="true"
                                    data-width="1.1">
                            </canvas>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
            <tr class="last-row">
                <td></td>
                <td><?=$product->getName()?></td>
                <?
                foreach($sumCols as $c){
                    if(array_key_exists($c,$sums)){
                        ?>
                        <td style="text-align: right"><?=number_format($sums[$c],2,'.','')?> €</td>
                    <?}
                }
                ?>
                <td></td>
            </tr>
            <?php

            ?>
            </tbody>
        </table>

        <?php } ?>

        <?php if ($result): ?>
            <?php

            foreach($result as $setArticleData){

                $setArticle = $setArticleData['setArticle'];

            $articles = $setArticleData['articles'];
            if(!$articles){
                continue;
            }
            $head = array_keys($articles[0]);




            /**
             * f.	Bitte auf die Teilung achten, wann eine 2. Seite benötigt wird
            bis vier Zeilen auf einer Seite
            Wenns fünf Tabellenzeilen werden, drei vorne stehen lassen – den Rest auf der nächsten Seite abbilden

             */
           # if(count($articles) <=4){
                $d = [$articles];
            /*}else{
                $first = [];
                $first[] = array_shift($articles);
                $first[] = array_shift($articles);
                $first[] = array_shift($articles);

                $d = [$first,$articles];
            }*/

            foreach($d as $i => $articles){
                $sums = [];
            ?>

            <table class="product-table set-article-table table--full-width" <? if($i > 0){?> style="page-break-before: always;" <?}?>>
                <thead>
                <tr>
                    <?php foreach ($head as $th) { ?>
                        <th><?= $this->translate("datasheet." . $th) ?></th>
                    <?php } ?>
                </tr>
                </thead>
                <tbody>

                <?php foreach ($articles as $i => $articleRow) {
                    $setArticleData['setArticleData']['amount'] += $articleRow['amount'];
                    ?>
                    <tr>
                        <?php foreach ($articleRow as $param => $value) {

                            if($param == 'amount' && $value){
                                $value = (int)$value;
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
                                        $value = number_format($value,2,'.','');
                                        $value = '<span style="white-space:nowrap;">'.$value.' €</span>';
                                    }
                                    ?>
                                <?} ?>
                                <td  class="column-<?=$param?>">

                                    <?= $value ?></td>
                            <?php } ?>
                        <?php } ?>
                    </tr>
                <?php } ?>
                <tr class="last-row">
                    <?

                        foreach ($articleRow as $param => $value) {?>

                               <? if($param == 'art_nr'){?>
                                    <td><?=$setArticle->getArt_no()?></td>
                                <?}elseif($param == 'title'){?>
                                    <td><?=$setArticle->getName()?></td>
                                <?}elseif ($param == 'amount'){?>
                                  <td class="column-amount"><?=$setArticleData['setArticleData']['amount']?></td>
                                <?}elseif(in_array($param,$sumCols)){?>
                                    <td style="text-align: right; white-space: nowrap;"><?=number_format($setArticleData['setArticleData'][$param],2,'.','')?> €</td>
                                <?}elseif($param == 'ean'){?>
                                    <td class="col-ean column-<?=$param?>">
                                        <?php if ($setArticle->getEan()) { ?>
                                            <canvas class="barcode"
                                                    data-background="#fff"
                                                    data-height="20"
                                                    data-margin="2"
                                                    data-padding="0"
                                                    data-value="<?= $setArticle->getEan() ?>"
                                                    data-format="EAN13"
                                                    data-displayValue="true"
                                                    data-width="1.1">
                                            </canvas>
                                        <?php } ?>
                                    </td>
                                <? }else{?>
                                    <td>&nbsp;</td>
                                <?}?>

                        <?}

                    ?>

                </tr>

                </tbody>
            </table>
                <div class="info-text" style="page-break-before: avoid;">
                    <div class="left">
                        <?=$this->t('datasheet_uvp_text')?>
                    </div>
                    <div class="right">
                        <?=$this->t('datasheet_set_display_table_note')?> <?=$setArticle->getArt_no()?>
                    </div>
                    <br style="clear: both;" />
                </div>
                <?}
            }

            ?>







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
