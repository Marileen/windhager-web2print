<style>
    .wrapper--big {
        page: ldl-big;
        padding-top: 6.5mm;
        padding-right: 4mm;
        padding-bottom: 5mm;
        padding-left: 5mm;

        line-height: 1;
    }

    .logo-wrapper,
    .address1,
    .address2 {
        float: left;
        width: 33.33%;
        height: 31mm;
        font-size: 10pt;
    }

    .logo-wrapper {
        padding-right: 8mm;
    }

    .address1 {
        padding-right: 4mm;
    }

    .address1,
    .address2 {
        line-height: 1.2;
    }

    .art-nr {
        margin-bottom: 6mm;
    }

    .description {
        width: 40%;
        float: left;
        height: 30mm;
    }

    .description__title {
        margin-top: 5.5mm;
        margin-bottom: 4mm;
    }

    .barcode-wrapper,
    .icon {
        float: left;
        width: 30%;
    }

    .barcode-wrapper {
        height: 30mm;
    }

    .icon {
        height: 22mm;
    }

    .art-nr__title,
    .content-nr__title,
    .description__title {
        font-size: 7pt;
    }

    .art-nr__title,
    .content-nr__title {
        margin-bottom: 2pt;
    }

    .art-nr__text,
    .description__text,
    .content-nr__text {
        font-size: 12pt;
        font-weight: bold;
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
<div class="wrapper wrapper--big" id="as">
    <div class="top-row clearfix">
        <div class="logo-wrapper">
            <img src="/plugins/Windhager/static6/img/logo-schellenberg.png" class="logo img-responsive img-responsive--vert">
        </div>
        <div class="address1">
            <?=$this->t('ldl_schellenberg_address1')?>
        </div>
        <div class="address2">
            <?=$this->t('ldl_schellenberg_address2')?>

        </div>
    </div>
    <div class="art-nr">
        <div class="art-nr__title">
            <?=$this->t('ldl_schellenberg_matnr')?>
        </div>
        <div class="art-nr__text">
            <?
            $query = 'SELECT * FROM windhager_exchange_prices WHERE client_art_no is not null and year=year(now()) and mandant=10 and pricelist_no in ("DC","DD") and art_no=? LIMIT 1';
            $result = \Pimcore\Db::get()->fetchRow($query,[$product->getArt_no()])

            ?>
            <?=$result['client_art_no']?>
        </div>
    </div>

    <div class="description-row clearfix">
        <div class="description">
            <div class="description__title">
                <?=$this->t('ldl_schellenberg_desc')?>
            </div>
            <div class="description__text">
                <?=$product->getEsbTitle1('de_DE')?>
            </div>
        </div>
        <div class="barcode-wrapper">
            <?php
            $imgData = Web2PrintBlackbit\Barcode::getImageData(['text' => $product->getEan(),'factor' => 1.5]);
            if($imgData){ ?>
            <img src="data:image/png;base64,<?=base64_encode($imgData)?>" class="img-responsive img-responsive--vert center-block">

            <?}?>
        </div>
        <div class="icon">
            <img src="/plugins/Windhager/static6/img/50713-icon.png" class="img-responsive img-responsive--vert center-block">
        </div>
    </div>

    <div class="content-nr">
        <div class="content-nr__title">
            <?=$this->t('ldl_schellenberg_amount')?>
        </div>
        <?php
        $value = '';
        if($params['type'] == 'vpe'){
            $value = $product->getAmountArticlesVPE();
            $lblUnit = 'ldl_dehner_unit_vpe';
        }
        if($params['type'] == 'vke'){
            $value = $product->getVke();
            $lblUnit = 'ldl_dehner_unit_vke';
        }
        if($value){
        ?>
        <div class="content-nr__text">
            <span class="content-nr__amount"><?=$value?></span>
            <span class="content-nr__unit"><?=$this->t('ldl_schellenberg_amount_unit')?></span>
        </div>
        <? }?>

    </div>

</div>