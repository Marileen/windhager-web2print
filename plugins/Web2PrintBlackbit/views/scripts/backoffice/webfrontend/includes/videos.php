<?
/**
 * @var \Web2PrintBlackbit\Product $product
 */
$key = $this->key;
$product = $this->product;
$data = $this->tabArray[$key];


$videos = [];
foreach($data->getChildren() as $child){
    $getter = 'get'. ucfirst($child->getName());
    if($v = $product->$getter()){
        $videos[]  = $v;
    }
}
if($videos){
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
            <? if($videos){

                ?>
                <div class="row">
                    <div class="col-xs-12">
                        <? foreach($videos as $i => $video){
                            $tag = new \Pimcore\Model\Document\Tag\Video();
                            $tag->setName('montagevideo_' . $i);
                            $tag->setOptions(['width' => 350,'height' => 230]);

                            $data = (array)$video;
                            if(!$data['id']){
                                $data['id'] = $data['data'];
                            }
                            $tag->setDataFromEditmode($data);
                            echo $tag;
                            ?>
                        <?}?>
                    </div>

                </div>

            <?}?>
        </div>
    </div>
</div>
<?}?>