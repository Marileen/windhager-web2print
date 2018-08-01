<style>
    .page {
        height: 70mm;
        position: relative;
    }

    .canvas {
        position: relative;
        width: 100mm;
        line-height: 1;
    }

    .col-left {
        height: 70mm;
        width: 29.2mm;
        float: left;
        border-right: 0.2pt solid black;
        padding: 5mm 1mm;
        text-align: center;
    }

    .col-right {
        position: relative;
        height: 70mm;
        float: right;
        width: calc(100% - 29.2mm);
        padding-left: 1mm;
        padding-top: 1.5mm;
        padding-right: 2mm;
    }

    .wh-art-nr {
        font-size: 6pt;
        margin-bottom: 5mm;
    }

    .border-bottom {
        border-bottom: 0.2pt solid black;
    }
    .art-nr {
        float: left;
        padding-left: 2mm;
    }

    .art-nr__title {
        font-size: 4pt;
        margin-bottom: 0.5mm;
    }

    .art-nr__text {
        font-size: 20pt;
        height: 17pt;
    }

    .logo-wrapper {
        float: right;
        max-width: 30mm;
        height: 8.5mm;
        margin: 0;
        padding: 0;
        margin-bottom: 0.1mm;
    }
    .logo {
        margin-left: auto;
    }

    .description {
        margin: 5mm 1mm;
    }

    .description td {
        padding-bottom: 2mm;
    }

    .description__lang {
        padding-right: 4.5mm;
        font-weight: bold;
        vertical-align: top;
    }

    .content__bottom {
        position: absolute;
        bottom: 0;
        right: 0;
        left: 1mm;
        border-top: 0.2pt solid black;
        padding-top: 1mm;
        padding-left: 1mm;
        padding-bottom: 1mm;
    }

    .trade-unit__title {
        font-size: 5pt;
        margin-bottom: 2pt;
    }

    .trade-unit__text {
        font-size: 15pt;
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
<div class="wrapper" id="de">
    <div class="col-left barcode-wrapper">
        <div class="wh-art-nr">
            <?=$this->t('ldl_dehner_art_no_wh')?>
            <?= $product->getArt_no() ?: '-'?>


        </div>
        <?php
        $imgData = Web2PrintBlackbit\Barcode::getImageData(['text' => $product->getEan()]);
        if($imgData){ ?>
            <img src="data:image/png;base64,<?=base64_encode($imgData)?>" class="img-responsive img-responsive--vert center-block" >

        <?}?>
    </div>

    <div class="col-right">
        <div class="top-row border-bottom clearfix ">
            <div class="art-nr">
                <div class="art-nr__title">
                    <?=$this->t('ldl_dehner_art_no')?>
                    <?
                    $query = 'SELECT * FROM windhager_exchange_prices WHERE client_art_no is not null and year=year(now()) AND mandant=10 and pricelist_no in ("85","78","90") and art_no=? LIMIT 1';
                    $result = \Pimcore\Db::get()->fetchRow($query,[$product->getArt_no()]);
                    ?>
                    <?=$result['client_art_no']?>
                </div>
                <div class="art-nr__text">
                </div>
            </div>
            <div class="logo-wrapper">
                <img src="/plugins/Windhager/static6/img/logo-dehner.png" class="logo img-responsive img-responsive--vert">
            </div>
        </div>

        <table class="description">
            <?

            foreach(['de_DE' => 'D','fr_FR' => 'F','nl' => 'NL','en_GB' => 'GB','es' => 'E'] as $locale => $char){

                if($value = $product->getEsbTitle1($locale)){?>
                    <tr>
                        <td class="description__lang">
                           <?=$char?>
                        </td>
                        <td class="description__text">
                            <?=$value?>
                        </td>
                    </tr>
                <?
                }

            }?>
        </table>

        <div class="content__bottom">
            <div class="trade-unit__title">
                <?=$this->t('ldl_dehner_unit')?>
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
            ?>
            <? if($value){?>
            <div class="trade-unit__text">
                <span class="trade-unit__number"><?=$value?></span> <span class="trade-unit__unit"><?=$this->t($lblUnit)?></span>
            </div>
            <?}?>
        </div>
    </div>

</div>