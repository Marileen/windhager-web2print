<style>
    .page {
        position: relative;
        height: 70mm;
    }

    .canvas {
        position: relative;
        width: 100mm;
    }

    .corner {
        position: absolute;
        width: 2.5mm;
        height: 2.5mm;
        margin: 0.1mm;
    }

    .corner.corner--tl {
        top: 0;
        left: 0;
        border-top: 0.2pt solid black;
        border-left: 0.2pt solid black;
    }

    .corner.corner--tr {
        top: 0;
        right: 0;
        border-top: 0.2pt solid black;
        border-right: 0.2pt solid black;
    }

    .corner.corner--br {
        bottom: 0;
        right: 0;
        border-bottom: 0.2pt solid black;
        border-right: 0.2pt solid black;
    }

    .corner.corner--bl {
        bottom: 0;
        left: 0;
        border-bottom: 0.2pt solid black;
        border-left: 0.2pt solid black;
    }

    .wrapper {
        padding: 1mm;
        line-height: 1;
    }

    .art-nr__title,
    .content-nr__title{
        font-size: 11pt;
        margin-top: 1mm;
        margin-bottom: 1mm;
    }

    .art-nr {
        width: 64%;
        float: left;
        text-align: right;
        padding-right: 5.5mm;
        border-right: 1.5pt solid black;
    }

    .art-nr__text {
        font-size: 20pt;
        font-weight: bold;
    }

    .content-nr__text {
        font-size: 20pt;
        font-weight: normal;
    }

    .content-nr {
        width: 35%;
        float: right;
        text-align: right;
        padding-right: 2mm;
    }

    .border-bottom {
        border-bottom: 1.5pt solid black;
        padding-bottom: 0.7mm;
    }

    .middle-row {
        padding-top: 2.5mm;
        padding-bottom: 2.5mm
    }

    .fs-title {
        font-size: 19pt;
        font-weight: bold;
        padding-left: 2.2mm;
    }

    .fs-art-nr__title {
        font-size: 11pt;
    }

    .fs-art-nr {
        font-size: 20pt;
    }

    .description {
        text-align: center;
        font-size: 9pt;
        margin-top: 3mm;
    }

    .barcode-wrapper {
        margin-top: 8mm;
        height: 24mm;
    }

</style>
<?
/**
 * @var \Web2PrintBlackbit\Product $product
 */
$product = $this->product;
$params = $this->params;

$this->t = new \Pimcore\Translate('de_DE');
?>
<div class="wrapper" id="fs">
    <div class="corners">
        <div class="corner corner--tl">
        </div>
        <div class="corner corner--tr">
        </div>
        <div class="corner corner--bl">
        </div>
        <div class="corner corner--br">
        </div>
    </div>
    <div class="top-row border-bottom clearfix">
        <div class="art-nr">
            <div class="art-nr__title">
                <?=$this->t('ldl_hornbach_article_number')?>
            </div>
            <div class="art-nr__text">
                <?= $product->getArt_no() ?: '-'?>
            </div>
        </div>

        <div class="content-nr">
            <div class="content-nr__title">
                <?=$this->t('ldl_hornbach_unit')?>

            </div>
            <div class="content-nr__text">
                <?php
                $value = '';
                if($params['type'] == 'vpe'){
                    $value = $product->getAmountArticlesVPE();
                }
                if($params['type'] == 'vke'){
                    $value = $product->getVke();
                }
                echo $value ?: '-';
                ?>
            </div>
        </div>
    </div>
    <div class="border-bottom middle-row">
        <span class="fs-title">FLORASELF</span>
        <span class="fs-art-nr__title"><?=$this->t('ldl_hornbach_art_nr_client')?></span>
        <span class="fs-art-nr">
            <?
            $query = 'SELECT * FROM windhager_exchange_prices WHERE client_art_no is not null and year=year(now()) AND mandant=21 and pricelist_no="H0" and art_no=? LIMIT 1';
            $result = \Pimcore\Db::get()->fetchRow($query,[$product->getArt_no()])
            ?>
            <?=$result['client_art_no']?>
        </span>
    </div>
    <div class="description">
        <div class="description__title">
            <?
                $texts = array_filter([$product->getEsbTitle1('de_DE'),$product->getEsbTitle1('en_GB')]);
                echo implode('/', $texts);
            ?>
        </div>
        <div class="barcode-wrapper">
            <?php
            $imgData = Web2PrintBlackbit\Barcode::getImageData(['text' => $product->getEan(),'factor' => 1.2]);
            if($imgData){ ?>
                <img src="data:image/png;base64,<?=base64_encode($imgData)?>" class="img-responsive img-responsive--vert center-block" >

            <?}?>
        </div>

    </div>

</div>