<?
/**
 * @var \Web2PrintBlackbit\Product $product
 * @var \Pimcore\Model\Object\ClassDefinition\CustomLayout $classDef
 */
$product = $this->product;

$list = new \Pimcore\Model\Object\ClassDefinition\CustomLayout\Listing();
$list->setCondition('name="Webfrontend"');
$classDef =$list->load()[0];

$tabs = $classDef->getLayoutDefinitions()->getChildren()[0]->getChildren();
$tabArray = [];
foreach($tabs as $tab){
    $tabArray[$tab->getName()] = $tab;
}



?>

<div class="content">
    <div class="row margin-bottom-15">
        <div class="col-xs-12">
            <a href="javascript:history.back();"><span class="glyphicon glyphicon-menu-left"></span> <?=$this->t('backoffice_back')?></a>

            <a style="position: relative;top:-5px;" class="open-pimcore btn btn-default btn-sm pull-right" data-no-pimocre-hide="1" href="javascript:pimcore.helpers.openObject(<?=$product->getId()?>,'object');" title="<?= $this->translate('backoffice_webfrontend_open_pimcore') ?>"><img src="/pimcore/static6/img/flat-color-icons/cursor.svg" alt="open" width="20" style="position: relative;top:-1px;"/> <?= $this->translate('backoffice_webfrontend_open_pimcore') ?></a>
        </div>
    </div>

<div class="row">
    <div class="col-md-5 col-lg-4">
        <?
        $mainImage = $product->getImagesByField('in_use',null,'ecom_6')[0];

        if($mainImage){?>
            <img src="<?=$mainImage->getThumbnail('product-detail-preview')?>" alt="" />
        <?} ?>
    </div>
    <div class="col-md-7 col-lg-8">
        <? if($v = $product->getWebFrontendTitle(false)){?>
            <h1 class="headline-bottom-border"><?=$v?></h1>
        <?}?>
        <table class="table table-striped">
            <tbody>
            <?
            foreach($tabArray['mainTable']->getChildren() as $element){
                $this->template('backoffice/webfrontend/includes/tr.php',['product' => $product,'element' => $element],true);
                ?>
            <?}?>

            <?
            if($product->getProductType() == 'article'){?>
                <?

                $price = \Pimcore\Db::get()->fetchRow('SELECT * FROM windhager_exchange_prices WHERE  YEAR="' . date('Y') .'" AND mandant="10" AND pricelist_no="00" AND art_no=?',[$product->getArt_no()] );
                if($price){?>
                    <tr>
                        <td><?=$this->t('backoffice_label_price')?></td>
                        <td>
                            € <?=number_format($price['uvp'],2,',','')?>
                        </td>
                    </tr>
                <?} ?>
               <tr>
                   <td><?=$this->t('backoffice_label_product')?></td>
                   <td>
                       <a href="<?=$this->url(['id' => $product->getParent()->getId(), 'name' => $product->getParent()->getOSProductNumber() ?: 'article', 'docPath' => $this->document->getProperty('detailPage')], 'webfrontend-product-detail');?>"><?=$this->t('backoffice_label_product_goto')?></a>
                   </td>
               </tr>


            <?}else{
                $childLinks = [];
                if($product->getChildren()){?>
                    <tr>
                        <td><?=$this->t('backoffice_label_articles')?></td>
                        <td>
                            <?
                            $childs = $product->getChildren();
                            foreach($childs as  $x => $article){?>
                                <a href="<?=$this->url(['id' => $article->getId(), 'name' => $article->getOSProductNumber() ?: 'article', 'docPath' => $this->document->getProperty('detailPage')], 'webfrontend-product-detail');?>"><?=$article->getArt_no().' - ' . $article->getEsbTitle1()?></a><?
                            if($childs[$x+1]){
                                echo ', ';
                            }
                            } ?>
                        </td>
                    </tr>
                <?}
                ?>

            <?}
            ?>
            </tbody>
        </table>
    </div>
</div>

    <div class="row margin-top-30">
        <div class="col-xs-12">

            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">

                <?
                $key = 'BaseData'
                ?>

                <div class="panel panel-default">
                    <div class="panel-heading detail-expand" role="tab" id="headingFirst">
                        <h4 class="panel-title">
                            <a class="<?if($i > 0 ){?> collapsed <?}?>" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse<?=$key?>" aria-controls="collapse<?=$key?>">
                                <?=$this->t('backoffice_label_'.$key)?>
                            </a>
                        </h4>
                    </div>
                    <div id="collapse<?=$key?>" class="panel-collapse collapse <?if($i == 0 ){?>  in <?}?>" role="tabpanel" aria-labelledby="heading<?=$key?>">
                        <div class="panel-body">
                            <div class="row">
                                <?
                                $tables = [];
                                foreach($tabArray['attributes']->getChildren() as $panel){
                                    foreach($panel->getChildren() as $element){
                                        if($element->getName() == 'lifecycleCalculated'){
                                            if($value = $product->getLifecycleCalculated()){
                                                $tables[$panel->getName()] .= '<tr><td>Aktueller Lebenszyklus</td><td>'.$value->getTitle().'</td></tr>';
                                            }
                                        }else{
                                            $tables[$panel->getName()] .= $this->template('backoffice/webfrontend/includes/tr.php',['product' => $product,'element' => $element],true,true);

                                        }
                                        ?>
                                    <?} ?>
                                <?}
                                $tables = array_filter($tables);

                                foreach($tables as $table => $trs){?>
                                    <div class="col-xs-<?=(12/count($tables))?>">
                                        <h4><?=$this->t('backoffice_label_'.str_replace(' ','',$table))?></h4>
                                        <table class="table table-striped">
                                            <tbody>
                                            <?=$trs?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?}?>
                            </div>
                        </div>
                    </div>

                </div>

                    <?
                    $html = $this->template('backoffice/webfrontend/includes/attributes.php',['product' => $product,'i' => 2, 'key' => 'Produkt Details','headline' => 'general','tabArray' => $tabArray],true,true);
                    echo $html;
                    ?>



                    <? $key = 'Kataloginformationen';
                    $i = 3;


                    $tables = [];
                    foreach($tabArray['Kataloginformationen']->getChildren() as $panel){

                        ?>
                        <?
                        $trs = '';
                        foreach($panel->getChildren() as $element){
                            $trs .= $this->template('backoffice/webfrontend/includes/tr.php',['product' => $product,'element' => $element],true,'placeholder_'.$key);
                            ?>
                        <?}

                        if($trs){
                            $tables[] = '
                                            <h4>'.$this->t('backoffice_label_'.str_replace(' ','',$panel->getName())).'</h4>
                                            <table class="table table-striped">
                                                <tbody>'.$trs.'</tbody> </table>';
                        }?>
                    <?}

                    if($tables){ ?>
                        <div class="panel panel-default">
                                <div class="panel-heading detail-expand" role="tab" id="headingFirst">
                                    <h4 class="panel-title">
                                        <a class="<?if($i > 0 ){?> collapsed <?}?>" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse<?=$key?>" aria-controls="collapse<?=$key?>">
                                            <?=$this->t('backoffice_label_'.$key)?>
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapse<?=$key?>" class="panel-collapse collapse <?if($i == 0 ){?>  in <?}?>" role="tabpanel" aria-labelledby="heading<?=$key?>">
                                    <div class="panel-body">
                                        <div class="row">
                                            <? foreach($tables as $table){?>
                                                <div class="col-xs-<?=round(12/count($tables))?>"><?=$table?></div>
                                            <?}?>
                                        </div>
                                    </div>
                                </div>
                        </div>

                    <?}?>

                    <?
                    $html = $this->template('backoffice/webfrontend/includes/attributes.php',['product' => $product,'i' =>4, 'key' => 'ProduktionLager','headline' => 'general','tabArray' => $tabArray],true,true);
                    echo $html;
                    ?>

                <?
                $html = $this->template('backoffice/webfrontend/includes/images.php',['product' => $product,'key' => 'Images','tabArray' => $tabArray['Bilder']],true,true);
                echo $html;
                ?>

                <?
                $html = $this->template('backoffice/webfrontend/includes/videos.php',['product' => $product,'key' => 'Videos','tabArray' => $tabArray],true,true);
                echo $html;
                ?>
                    <?
                    $html = $this->template('backoffice/webfrontend/includes/montage.php',['product' => $product,'key' => 'Montage'],true,true);
                    echo $html;
                    ?>
                    <?
                    $html = $this->template('backoffice/webfrontend/includes/notes.php',['product' => $product,'key' => 'Notes'],true,true);
                    echo $html;
                    ?>



        </div>
    </div>


    <? if(false){?>
    <div class="row margin-top-30">
        <div class="col-xs-12">
        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
            <?
            $i = 0;
            foreach($tabArray['attributes']->getChildren() as $childs){
                $key = str_replace(' ','',$childs->getName());
                $html = $this->template('backoffice/webfrontend/includes/attributes.php',['product' => $product,'i' => $i, 'key' => $key,'elements' => $childs],true,true);
                echo $html;
                $i++;
            }
            ?>
        </div>
        </div>
    </div>
    <?}?>


</div>
