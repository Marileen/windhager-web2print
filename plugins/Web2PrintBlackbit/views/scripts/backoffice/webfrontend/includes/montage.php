<?
/**
 * @var \Web2PrintBlackbit\Product $product
 */
$key = $this->key;
$product = $this->product;


$text = $product->getText_application();

$videos = $product->getApplicationVideos_main();
$steps = $product->getSteps_assembly();

$constructionType = [];
foreach((array)$product->getConstruction_type() as $o){
    $constructionType[] = $o->getTitle();
}
$constructionType = array_filter($constructionType);

$fieldNames = \Web2PrintBlackbit\Helper\ProductLayout::getFieldMatrix()['fieldTitles'];

if($text || $videos || $steps || $constructionType){
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

            <? if($constructionType){?>
                <h4><?=$this->t('backoffice_label_general')?></h4>
                <table class="table table-striped">
                    <tbody>
                    <tr>
                        <td><?=$fieldNames['construction_type']?></td>
                        <td><?=implode(', ', $constructionType)?></td>
                    </tr>
                    </tbody>
                </table>
            <?}?>
            <? if($text){?>
                <div class="row">
                    <div class="col-xs-12">
                        <h4><?=$this->t('backoffice_label_text_application')?></h4>
                        <div><?=$text?></div>
                    </div>
                </div>
            <?}?>
            <? if($videos){

                ?>
                <div class="row">
                    <div class="col-xs-12">
                        <h4><?=$this->t('backoffice_label_steps_videos')?></h4>
                        <? foreach($videos as $i => $video){
                            $tag = new \Pimcore\Model\Document\Tag\Video();
                            $tag->setName('montagevideo_' . $i);
                            $tag->setOptions(['width' => 350,'height' => 280]);
                            $tag->setId($video->getId());
                            echo $tag;
                            ?>
                        <?}?>
                    </div>

                </div>

            <?}?>

            <? if($steps){?>
                <div class="row">
                    <div class="col-xs-12">
                        <h4><?=$this->t('backoffice_label_steps_assembly')?></h4>
                        <table class="table table-striped">
                            <tr>
                                <th><?=$this->t('backoffice_label_steps_image')?></th>
                                <th><?=$this->t('backoffice_label_steps_title')?></th>
                                <th><?=$this->t('backoffice_label_steps_description')?></th>
                            </tr>
                            <? foreach($steps as $step){
                                $data = $step['localizedfields']->getData();
                                ?>
                                <tr>
                                    <td width="100"><?
                                        $image = $data->getLocalizedValue('image_assembly');

                                        if($image){?>
                                            <img src="<?=$image->getThumbnail('shop-cart-list')?>" alt="" />
                                        <?} ?>

                                    </td>
                                    <td width="200"><?=$data->getLocalizedValue('title_assembly')?></td>
                                    <td><?=$data->getLocalizedValue('text_assembly')?></td>
                                </tr>
                            <?}?>
                        </table>

                    </div>
                </div>
            <?}?>
        </div>
    </div>
</div>
<?}?>