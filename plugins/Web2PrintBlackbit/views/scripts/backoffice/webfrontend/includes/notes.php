<?php

/**
 * @var \Pimcore\Model\Object\ClassDefinition\Data $element
 * @var \Web2PrintBlackbit\Product $product
 */
$product = $this->product;
$tabArray = $this->tabArray;
$key = $this->key;


$list = new Pimcore\Model\Element\Note\Listing();
$list->setCondition('ctype="object" AND cid=?',[$product->getId()]);
$list->setOrderKey('id')->setOrder('ASC');
$notes = $list->load();

if($notes){
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
            <table class="table table-striped">
                <tr>
                    <th><?=$this->t('backoffice_label_col_title')?></th>
                    <th><?=$this->t('backoffice_label_col_description')?></th>
                    <th><?=$this->t('backoffice_label_col_type')?></th>

                    <th><?=$this->t('backoffice_label_col_User')?></th>
                    <th><?=$this->t('backoffice_label_col_Date')?></th>
                </tr>
                <?
                /**
                 * @var $note \Pimcore\Model\Element\Note
                 */
                foreach($notes as $note){
                    ?>
                    <tr>
                        <td><?=$note->getTitle()?></td>
                        <td><?=$note->getDescription()?></td>
                        <td><?=$note->getType()?></td>

                        <td><?
                            $user = \Pimcore\Model\User::getById($note->getUser());
                            if($user){
                                echo $user->getName();
                            }
                        ?></td>
                        <td><?
                            setlocale(LC_TIME, 'German');

                            $date = $note->getDate();
                            $date = \Carbon\Carbon::createFromTimestamp($date);

                            echo $date->formatLocalized('%d.%m.%Y'); ;
                            ?></td>
                    </tr>
                <?}?>
            </table>

        </div>
    </div>
</div>
<?
}

?>


