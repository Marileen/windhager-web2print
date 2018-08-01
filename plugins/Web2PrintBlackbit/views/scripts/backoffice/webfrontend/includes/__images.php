<?
/**
 * @var \Pimcore\Model\Object\ClassDefinition\Data $element
 * @var \Web2PrintBlackbit\Product $product
 */
$product = $this->product;

$images = [];

$addedIds = [];


$key = $this->key;
$i = $this->i;
$data = $this->tabArray[$key];

$rows = [];
if($data){
    $images = \Web2PrintBlackbit\Helper\Webfrontend::getImagesFromPanel($product,$data);
    $rows = array_chunk($images,4);
}


if($rows){
    $i = $this->i;
    $key = $this->key;
    ?>
<div class="panel panel-default">
    <div class="panel-heading detail-expand" role="tab" id="heading<?=$key?>">
        <h4 class="panel-title">
            <a class="<?if($i > 0 ){?> collapsed <?}?>" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse<?=$key?>" aria-controls="collapse<?=$key?>">
                <?=$this->t('backoffice_label_'.$key)?>
            </a>
        </h4>
    </div>
    <div id="collapse<?=$key?>" class="panel-collapse collapse <?if($i == 0 ){?>  in <?}?>" role="tabpanel" aria-labelledby="heading<?=$key?>">
        <div class="panel-body">

        <? foreach($rows as $row){?>
            <div class="row margin-bottom-10">
                <? foreach($row as $asset){?>
                    <div class="col-xs-3">
                        <a href="<?=$asset->getThumbnail('product-detail-big')?>" data-toggle="lightbox">
                            <img src="<?=$asset->getThumbnail('shop-cart-list')?>" alt="" />
                        </a>
                    </div>
                <?}?>
            </div>
            <div class="row margin-bottom-15">
                <? foreach($row as $asset){?>
                    <div class="col-xs-3">
                        <a href="<?=$this->document->getProperty('downloadPage')?>?id=<?=$asset->getId()?>" class="btn btn-default"><span class="glyphicon glyphicon-download"></span> <?=$this->t('backoffice_label_download')?></a>
                    </div>
                <?}?>
            </div>
        <?}?>
        </div>
    </div>
</div>
<?}?>
