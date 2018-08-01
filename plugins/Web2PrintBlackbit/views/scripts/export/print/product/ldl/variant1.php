<style>
    .wrapper--big {
        page: ldl-big;
        padding-top: 25mm;
        padding-right: 10mm;
        padding-bottom: 5mm;
        padding-left: 15mm;

        line-height: 1;
    }

    .art-nr {
        float: left;
        width: 59%;
    }

    .content-nr {
        float: right;
        width: 41%;
    }

    .art-nr__title,
    .content-nr__title {
        margin-bottom: 2mm;
    }

    .art-nr__text,
    .content-nr__text {
        font-size: 25pt;
        font-weight: bold;
    }


    .description {
        margin-top: 7mm;
        line-height: 1.2;
    }
    .description__text {
        font-size: 12pt;
        font-weight: bold;
        margin-bottom: 3pt;
    }

    .description_subline1,
    .description_subline2 {
        font-size: 6pt;
        font-weight: bold;
        margin-bottom: 1pt;
    }

    .bottom-row {
        margin-top: 3.5mm;
    }

    .barcode-title {
        margin-bottom: 4mm;
    }

    .barcode-wrapper {
        float: left;
        min-width: 33mm;
        width: 45%;
        height: 24mm;
    }

    .bottom-row__middle {
        width: 35%;
        float: left;
    }

    .sales-unit {
        font-size: 10pt;
        line-height: 1.1em;
        font-weight: bold;
        height: 5.5em;
    }

    .icon {
        float: right;
        width: 20%;
        max-height: 24mm;
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
<div class="wrapper wrapper--big" id="standard">

    <div class="clearfix top-row">
        <div class="art-nr">
            <div class="art-nr__title">
                <?=$this->t('ldl_label_artno')?>
            </div>
            <div class="art-nr__text">
                <?= $product->getArt_no() ?: '-'?>
            </div>
        </div>

        <div class="content-nr">
            <div class="content-nr__title">
                <?=$this->t('ldl_label_content')?>
            </div>
            <div class="content-nr__text">

                <?php
                    $value = '';
                    if($this->getParam('type') == 'vke'){
                        $value = $product->getVke();
                    }
                    if($this->getParam('type') == 'vpe'){
                        $value = $product->getAmountVKEinVPE();
                    }
                    echo $value ?: '-';
                ?>
            </div>
        </div>
    </div>

    <div class="description">
        <div class="description__title">
            <?=$this->t('ldl_label_title')?>
        </div>
        <div class="description__text">
            <?=$product->getEsbTitle1('de_DE')?>
        </div>
        <?php
            foreach(['en_GB','fr_FR'] as $i => $locale){?>
                <div class="description_subline<?=$i+1?>">
                    <?=$product->getEsbTitle1($locale)?>
                </div>
        <?php } ?>
    </div>

    <div class="bottom-row">
        <div class="barcode-title">
            <?= $this->t('ldl_ean') ?>
        </div>
        <div class="clearfix bottom-row__bottom">
            <div class="barcode-wrapper">
                <?php
                $imgData = Web2PrintBlackbit\Barcode::getImageData(['text' => $product->getEan(),'factor' => 1.5]);
                if($imgData){ ?>
                    <img src="data:image/png;base64,<?=base64_encode($imgData)?>" class="img-responsive img-responsive--vert" >

                <?}?>
            </div>
            <div class="bottom-row__middle">
                <div class="sales-unit">
                    <?= $this->t('ldl_' . $this->getParam('type')) ?>
                    <?= $this->t('ldl_'.$this->getParam('type').'_suffix_1')?>
                </div>
                <div class="charge">
                    <?= $this->t('ldl_charge') ?> <?=$params['callbackSettings']['charge']?>
                </div>
            </div>
            <div class="icon">
                <img src="/plugins/Windhager/static6/img/06202-icon.png" class="img-responsive img-responsive--vert center-block">
            </div>
        </div>
    </div>

</div>