<?php
$this->layout()->setLayout('backoffice');
$paginator = $this->paginator;

$fieldMatrix = \Web2PrintBlackbit\Helper\ProductLayout::getFieldMatrix();
$userRoles = $fieldMatrix['userRoles'];

$this->headLink()->appendStylesheet('/plugins/Windhager/static-backoffice/css/todo-list.css');
?>
<? $this->headScript()->appendFile('/plugins/Windhager/static-backoffice/js/todo.js');?>

<!-- Modal -->
<div id="schedule_modal_wrapper">

</div>


<div class="container">
<div class="content">
    <div class="row">
        <div class="col-xs-12">
            <h1 style="margin-top: 0;"><?=$this->ts('backoffice_todo_list')?>
            </h1>
        </div>
    </div>


<div class="panel panel-default">
    <div class="panel-heading">

        <div class="row">
            <div class="col-xs-10">
                &nbsp;        <b style="font-size: 1.2em;"><?=$this->ts('backoffice_filter')?></b>

            </div>
            <div class="col-xs-2">
                <a class="btn btn-default btn-xs clear-all-link pull-right" href="<?=$this->url()?>"><span class="glyphicon glyphicon-remove"></span> <?=$this->ts('backoffice_clear_filters')?></a>
            </div>
        </div>

    </div>
    <form class="form-horizontal" action="/plugin/Windhager/Backoffice_Todo/list/">

    <div class="panel-body" >
            <div class="row">
                <? if(count($this->categories) > 1){?>
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label for="type"
                                   class="col-sm-3 control-label"><?= $this->ts('backoffice_filter_path') ?></label>
                            <div class="col-sm-9">
                                <select name="type[]" class=" js-states form-control" id="type" multiple="multiple" style="min-height: 80px">
                                    <? foreach ($this->categories as $e) { ?>
                                        <option
                                            value="<?= $e ?>" <?php if (in_array($e, $this->getParam('type', []))) { ?> selected <?php } ?>> <?= $this->ts('backoffice_todo_taks_' . $e) ?></option>
                                    <? } ?>
                                </select>
                            </div>
                        </div>
                    </div>

                <?}?>

                    <?
                    $fieldMatrix = \Web2PrintBlackbit\Helper\ProductLayout::getFieldMatrix();
                    $roles = [];

                    foreach(array_keys($fieldMatrix['roleFields']) as $roleId){
                        if($fieldMatrix['userRoles'][$roleId]){
                            $roles[$roleId] = $fieldMatrix['userRoles'][$roleId];
                        }
                    }
                    asort($roles);
                    ?>

                    <? if(count($roles) > 1){?>
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label for="path"
                                       class="col-sm-2 control-label"><?= $this->ts('backoffice_filter_roles') ?></label>
                                <div class="col-sm-10">

                                    <select name="roles[]" class=" js-states form-control js-filter" id="roles" multiple style="width: 200px;min-height: 80px">
                                        <? foreach ($roles as $roleId => $role) { ?>
                                            <option
                                                value="<?= $roleId ?>" <?php if (in_array($roleId, $this->getParam('roles', []))) { ?> selected <?php } ?>><?= $role ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                            </div>
                        </div>

            <?}?>
            </div>

            <div class="row">
                <div class="col-xs-6">
                    <div class="form-group">
                        <label for="type"
                               class="col-sm-3 control-label"><?= $this->ts('backoffice_filter_date') ?></label>
                        <div class="col-sm-9">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="col-auto">
                                        <div class="input-group mb-2 mb-sm-0">
                                            <div class="input-group-addon"><?=$this->ts('backoffice_from')?></div>
                                            <input type="text" name="from" class="form-control datepicker" value="<?=$this->getParam('from')?>">
                                            <span class="glyphicon glyphicon-calendar" ></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="col-auto">
                                        <div class="input-group mb-2 mb-sm-0">
                                            <div class="input-group-addon"><?=$this->ts('backoffice_till')?></div>
                                            <input type="text" name="till" class="form-control datepicker" value="<?=$this->getParam('till')?>">
                                            <span class="glyphicon glyphicon-calendar" ></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-6">
                    <div class="form-group">
                        <label for="query" class="col-sm-2 control-label"><?=$this->ts('backoffice_filter_state')?></label>
                        <div class="col-sm-10">
                            <select name="state" class="form-control">
                                <? foreach (['all','open','pending','resolved']as $state) { ?>
                                    <option value="<?= $state?>" <?php if ($this->getParam('state') == $state || (!$this->getParam('state') && $state == 'open')){ ?> selected <?php } ?>><?= $this->ts('backoffice_state_'.$state)?></option>
                                <? } ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="form-group">
                    <label for="path" class="col-sm-2 control-label"
                           style="width: 12.5%"><?= $this->ts('backoffice_filter_path_products') ?></label>
                    <div class="col-sm-10" style="width: 87.5%">
                        <select name="path" class="select2 js-states form-control pathFilter js-filter" id="path">
                            <option value=""><?= $this->ts('backoffice_filter_choose') ?></option>
                            <? foreach ($this->tree as $e) { ?>
                                <option
                                    value="<?= $e['path'] ?>" <? if ($this->getParam('path') == $e['path']) { ?> selected <? } ?>> <?= $e['name'] ?></option>
                            <? } ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel-footer text-center">
        <button type="submit" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-filter"></span> <?=$this->ts('search')?></button>
    </div>
    </form>

</div>

<?php
/**
 * @var \Zend_Paginator $paginator
 */

if($paginator->getTotalItemCount()) { ?>

<table class="table table-striped table-hover list-table">
    <caption>
        <?php
        $firstItemNumber  = (($paginator->getCurrentPageNumber() - 1) * $paginator->getItemCountPerPage()) + 1;
        $lastItemNumber   = $firstItemNumber + $paginator->getCurrentItemCount() - 1;
        $totalItems = $paginator->getTotalItemCount();

        ?>
        <?=$firstItemNumber?> - <?=$lastItemNumber?> <?=$this->ts('from')?> <?= $paginator->getTotalItemCount(); ?></caption>
    <thead>
        <tr>
            <th>ID</th>
            <th><?=$this->ts('backoffice_todo_status')?></th>

            <? if(count($userRoles) > 1){?>
                <th><?=$this->ts('backoffice_role')?></th>
            <?}?>
            <th><?=$this->ts('backoffice_todo_creationDate')?></th>
            <th><?=$this->ts('backoffice_todo_type')?></th>
            <th><?=$this->ts('backoffice_todo_info')?></th>
            <th><?=$this->ts('backoffice_todo_note')?></th>
            <th width="70"><?=$this->ts('backoffice_todo_action')?></th>
        </tr>
    </thead>
    <tbody>
    <?php
    /**
    * @var Web2PrintBlackbit\TodoItem $item
    */
    foreach ($paginator as $item) {
    ?>
        <tr>
            <td><?=$item->getId()?></td>
            <td>
                <?php

                $status = $item->getStatus();

                $icon = '';
                if($status == 'open'){
                    $icon = 'warning.svg';
                }
                if($status == 'pending'){
                    $icon = 'expired.svg';
                }
                if($status == 'resolved'){
                    $icon = 'ok.svg';
                }
                ?>
                <img src="/pimcore/static6/img/flat-color-icons/<?=$icon?>" alt="<?=$status?>" title="<?=$this->ts('status_'.$status)?>" width="17" height="17" />
            </td>
            <? if(count($userRoles) > 1){?>
                <td><?=$item->getRole()?></td>
            <?}?>
            <td>
                <?

                    $ts = $item->getCreationDate();
                    $date = \Carbon\Carbon::createFromTimestamp($ts);
                    echo $date->format('d.m.Y');
                    ?>
            </td>
            <td><?=$this->ts('backoffice_todo_'.$item->getItemType().'-'.$item->getItemSubType())?></td>
            <td><?php
                if($item->getItemType() == 'new_article'){
                    $article = $item->getTargetItem();
                    /**
                     * @var $article \Web2PrintBlackbit\Product
                     */
                    if($article){
                        echo $article->getArt_no().' - ' . $article->getEsbTitle1();
                    }
                }
                if($item->getItemType() == 'lifecycle'){
                    $getter = "getDateLifecycle" . $item->getItemSubType();
                    if($article = $item->getTargetItem()){
                        if(method_exists($article,$getter)){
                            $value = $article->$getter();
                            if($value instanceof \Pimcore\Date){
                                echo $article->getArt_no().' - ' .$article->getEsbTitle1(). ' - ' . $this->ts('backoffice_date').': ' . date('d.m.Y',$value->getTimestamp());
                            }

                        }
                    }
                }

                if($item->getItemType() == 'certificate'){
                    /**
                     * @var $certificate \Pimcore\Model\Object\AttributeCertificateType
                     */
                    if($certificate = $item->getTargetItem()){
                        $metaData = $item->getMetaData();
                        if($metaData){
                            $x = [];
                            $d = json_decode($metaData,true);

                            foreach($d as $date => $localse){
                                $x[] = '<span style="white-space: nowrap;" >'.$date.' <span class="glyphicon glyphicon-info-sign" title="' . implode(', ',$localse).'"></span></span>';
                            }
                        }
                        if ($o = $item->getTargetItem()) {
                            if ($v = $o->getTitle()) {
                                echo '<br/>' . $v;
                            }
                        }
                        echo $this->ts('backoffice_expired') . ' (' . implode($x, " / ");
                    }
                }

                if($item->getItemType() == 'asset'){

                    if($asset = $item->getTargetItem()){
                        $ablaufdatum = $asset->getMetadata('Ablaufdatum','de_AT');
                        echo $this->ts('backoffice_expired') . ' ' . $ablaufdatum.' (' .$asset->getFilename().')';
                    }

                }

                if($item->getItemType() == 'vke_change'){
                    if($article = $item->getTargetItem()){
                        echo $article->getArt_no().' - ' . $article->getEsbTitle1().'<br/>';
                    }

                    echo $item->getMetaData();
                }

                if($item->getItemType() == 'asset_rohwaren'){
                    $tmp = [];
                    $metaData = json_decode($item->getMetaData(),true);
                    foreach($metaData as $e){
                        if($product = \Web2PrintBlackbit\Product::getById($e['src_id'])){
                            $tmp[] = '<a href="javascript:pimcore.helpers.openObject('.$product->getId() .',\'object\');">'.$product->getArt_no().'</a>';
                        }
                    }
                    if($tmp){
                        echo $this->ts('backoffice_used_by_article').': ' . implode(', ', $tmp);
                    }
                }

                if($item->getItemType() == 'note'){
                    $note = \Pimcore\Model\Element\Note::getById($item->getMetaData());
                    /**
                     * @var \Pimcore\Model\Element\Note $note
                     */
                    if($note){
                        $data = [];

                        if($article = $item->getTargetItem()){
                            $data[] = $article->getArt_no().' - ' . $article->getEsbTitle1();
                        }
                        if($note->getTitle()){
                            $data[] = $this->ts('backoffice_label_type').': ' . $this->ts('backoffice_label_type_'.$note->getType());
                        }
                        if($note->getTitle()){
                            $data[] = $this->ts('backoffice_label_title').': ' . $note->getTitle();
                        }
                        if($note->getDescription()){
                            $data[] = $this->ts('backoffice_label_description').': ' . $note->getDescription();
                        }
                        echo implode("<br/>",$data);

                    }
                }

                if($item->getItemType() == 'stock_change'){
                    echo $item->getMetaData();
                }
                ?></td>
            <td>
                <?=$item->getNote()?>
            </td>
            <td>
                <?php if($targetItem = $item->getTargetItem()){
                    if($targetItem instanceof \Pimcore\Model\Object){
                        $script = 'pimcore.helpers.openObject('.$targetItem->getId() .',\'object\');';
                    }

                    if($targetItem instanceof \Pimcore\Model\Asset){
                        $script = 'pimcore.helpers.openAsset('.$targetItem->getId() .',\''.$targetItem->getType().'\');';
                    }

                    ?>
                    <a class="open-pimcore" style="position: relative;top:2px;" data-no-pimocre-hide="1"
                       href="javascript:<?=$script?>"
                       title="<?= $this->ts('backoffice_webfrontend_open_pimcore') ?>"><img
                            style="position: relative;top: -3px;"
                            src="/pimcore/static6/img/flat-color-icons/cursor.svg" alt="open" width="20"/></a>
                <?php } ?>

                <?php if(!$item->getResolvedBy()){?>
                    <a class="fa fa-clock-o schedule remote-modal" aria-hidden="true" href="/plugin/Windhager/Backoffice_Todo/schedule?id=<?=$item->getId()?>" title="<?=$this->ts('backoffice_schedule')?>"></a>
                <?php } ?>
                <?php if(!$item->getResolvedBy()){?>

                    <a class="fa fa-check remote-modal"  href="/plugin/Windhager/Backoffice_Todo/resolved?id=<?=$item->getId()?>" title="<?=$this->ts('backoffice_mark_resolved')?>"></a>
                <?php } ?>
            </td>
        </tr>
    <? } ?>
    </tbody>
</table>
    <div class="row">
<?php
    if($paginator->getPages()->pageCount > 1){ ?>
    <div class="text-center">

        <?= $this->paginationControl($paginator, 'Sliding', 'includes/pagination/default.php', ['getParams' => $_GET]); ?>
    </div>
<? } ?>
    </div>

<?php }else {

        if($_GET){
            $tKey = 'backoffice_no_filter_result';
        }else{
            $tKey = 'backoffice_no_result_result';
        }
    ?>
    <div class="alert alert-<?php if($_GET){?>warning<?php } else{?>success<?php }?> text-center">
        <?=$this->ts($tKey)?>
    </div>

<?php } ?>
</div>
</div>
