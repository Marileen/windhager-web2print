<?
$paginator = $this->paginator;
?>
<div class="content">
    <div class="row">
        <div class="col-xs-12">
            <h1  class="headline-bottom-border"><?=$this->t('backoffice_webfrontend')?></h1>
        </div>
    </div>
</div>


<div class="panel panel-default">
    <div class="panel-heading">

        <div class="row">
            <div class="col-xs-10">
        &nbsp;        <b style="font-size: 1.2em;"><?=$this->t('backoffice_filter')?></b>
            </div>
        </div>

    </div>
    <form class="form-horizontal" id="filterForm">

        <input type="hidden" name="orderKey" value="<?=$this->escape($this->getParam('orderKey'))?>" />
        <input type="hidden" name="order" value="<?=$this->escape($this->getParam('order'))?>" />

    <div class="panel-body" >

            <div class="row">
                <div class="col-xs-6">
                    <div class="form-group">
                        <label for="query" class="col-sm-3 control-label"><?=$this->t('backoffice_filter_text')?></label>
                        <div class="col-sm-9">
                            <input type="text" id="query" name="query" class="form-control" value="<?=$this->escape($this->getParam('query'))?>" />
                        </div>
                    </div>
                </div>

                <div class="col-xs-6">
                    <div class="form-group">
                        <label for="path" class="col-sm-3 control-label"><?=$this->t('backoffice_filter_tenant')?></label>
                        <div class="col-sm-9">
                            <?
                            $tenants = \OnlineShop\Framework\Factory::getInstance()->getAllTenants();

                            ?>
                            <select name="tenant" class=" js-states form-control js-filter" id="tenant"  >
                                <option value=""><?=$this->t('backoffice_filter_choose')?></option>

                                <? foreach($tenants as $tenant){
                                    ?>
                                    <option value="<?=$tenant ?>" <? if($this->getParam('tenant') == $tenant){?> selected <?}?>><?=$this->t('backoffice_tenant_' . $tenant)?></option>
                                <?}?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6">
                    <div class="form-group">
                        <label for="path" class="col-sm-3 control-label"><?=$this->t('backoffice_filter_productType')?></label>
                        <div class="col-sm-9">

                            <?php

                            $options = ["article","product"];
                            ?>
                            <select name="productType" class=" js-states form-control js-filter" id="productType"  >
                                <option value=""><?=$this->t('backoffice_filter_choose')?></option>

                                <? foreach($options as $value){
                                    ?>
                                    <option value="<?=$value ?>" <? if($this->getParam('productType') == $value){?> selected <?}?>><?=$this->t('backoffice_select_productType_' . $value)?></option>
                                <?}?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-xs-6">
                    <div class="form-group">
                        <label for="path" class="col-sm-3 control-label"><?=$this->t('backoffice_filter_esbType')?></label>
                        <div class="col-sm-9">

                            <?php

                                $options = ["D","F","R"];
                            ?>
                            <select name="esbType" class=" js-states form-control js-filter" id="esbType"  >
                                <option value=""><?=$this->t('backoffice_filter_choose')?></option>

                                <? foreach($options as $value){
                                    ?>
                                    <option value="<?=$value ?>" <? if($this->getParam('esbType') == $value){?> selected <?}?>><?=$this->t('backoffice_select_esb_type_' . $value)?></option>
                                <?}?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <div class="form-group">
                        <label for="path" class="col-sm-2 control-label" style="width: 12.5%"><?=$this->t('backoffice_filter_path')?></label>
                        <div class="col-sm-10" style="width: 87.5%">
                            <select name="path" class="select2 js-states form-control pathFilter js-filter" id="path">
                                <option value=""><?=$this->t('backoffice_filter_choose')?></option>
                                <? foreach($this->tree as $e){?>
                                    <option value="<?=$e['path']?>" <? if($this->getParam('path') == $e['path']){?> selected <?}?>> <?=$e['name']?></option>
                                <?}?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

    </div>

    <div class="panel-footer text-center">
        <button type="submit" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-filter"></span> Suchen</button>
    </div>
    </form>

</div>


<?php

    $isSearch = array_key_exists('path',$_GET);

    if(!$isSearch){?>
        <div class="alert alert-info text-center"><?=$this->t('backoffice_list_welcome_text')?></div>
    <?}else{

    if($paginator->getTotalItemCount()) {

        $order = $this->getParam('order','ASC');
        ?>

        <table class="table table-striped table-hover list-table">
            <caption><?= $this->translate('online-shop.back-office.order-list.result-count') ?>
                : <?= $paginator->getTotalItemCount(); ?></caption>
            <thead>
            <tr>
                <th width="70"><?= $this->translate('backoffice_webfrontend_image') ?></th>

                <?

                $cols = [
                    'art_no' => ['width' => 130,
                        'label' => 'backoffice_webfrontend_article_nr',
                    ],
                    'productType' => ['width' => 90,
                        'label' => 'backoffice_webfrontend_article_type'
                    ],
                    'article_name' => ['width' => '',
                    'label' => 'backoffice_webfrontend_article_name'
                ]
                ];

                foreach($cols as $key => $options){?>
                    <th width="<?=$options['width']?>" class="<? if($this->getParam('orderKey') == $key){?> active-sorting <?}?>"">
                        <div class="sorting-arrows" >
                            <a href="<?=$this->url(['orderKey' => $key,'order' => 'ASC']) ?> " class="arrow-up <? if($this->getParam('orderKey') == $key && $this->getParam('order') == 'ASC'){?> active-arrow <?}?>"></a>
                            <a href="<?=$this->url(['orderKey' => $key,'order' => 'DESC']) ?>" class="arrow-down <? if($this->getParam('orderKey') == $key && $this->getParam('order') == 'DESC'){?> active-arrow <?}?>"></a>
                        </div>
                        <span class="sorting-label"><?= $this->translate($options['label']) ?></span>
                    </th>
                <?}

                ?>

                <? if(false){?>
                <th width="90"><span class="sorting-label"><?= $this->translate('backoffice_webfrontend_article_nr') ?></span>
                    <div class="sorting-arrows">
                        <a href="" ><i class="fa fa-sort-asc" aria-hidden="true" ></i></a>
                        <a href="" ><i class="fa fa-sort-desc" aria-hidden="true"></i></a>
                    </div>

                </th>
                <th width="90"><?= $this->translate('backoffice_webfrontend_article_type') ?></th>
                <th><?= $this->translate('backoffice_webfrontend_article_name') ?></th>

            <?}?>
                <th width="100"></th>
            </tr>
             </thead>
            <tbody>
            <?php

            /**
             * @var Web2PrintBlackbit\Product $item
             */
            foreach ($paginator as $item) {

                $detailUrl = $this->url(['id' => $item->getId(), 'name' => $item->getOSProductNumber() ?: 'article', 'docPath' => $this->document->getProperty('detailPage')], 'webfrontend-product-detail');
                ?>
                <tr class="goto">
                    <td>
                        <?

                        $mainImage = $item->getImagesByField('in_use',null,'ecom_6')[0];
                        if ($mainImage) {
                            if($mainImage->getImage() instanceof \Pimcore\Model\Asset\Folder){
                                $mainImage = $item->getImagesByField('in_use',null,'ecom_6')[0];
                                var_dump($item->getId());
                                var_dump($mainImage); exit;
                            }
                            ?>
                            <img src="<?= $mainImage->getThumbnail('shop-cart-list') ?>" alt="" height="50">
                        <?
                        } ?>
                    </td>
                    <td><?= $item->getOSProductNumber() ?></td>
                    <td><?= $this->translate('backoffice_webfrontend_article_type_' . $item->getProductType()) ?></td>

                    <td><?=$item->getWebFrontendTitle()?><br/>

                        <?
                        $data = [];
                        foreach (['Item_length', 'Item_width', 'Item_height'] as $k) {
                            if ($v = $item->{"get" . ucfirst($k)}()) {
                                $data[] = $this->t('backoffice_webfrontend_' . $k) . ': ' . $v;
                            }
                        }
                        if ($mainColor = $item->getMain_color()) {
                            $data[] = $this->t('backoffice_webfrontend_main_color') . ': ' . $mainColor->getTitle();
                        }

                        echo implode(' ', $data);

                        ?>

                    </td>
                    <td class="text-right action-col">
                        <a href="<?= $detailUrl ?>" class="goto-link"
                           title="<?= $this->translate('backoffice_webfrontend_open_detail') ?>"
                           style="font-size: 1.2em;"><span class="glyphicon glyphicon-eye-open"></span></a>

                        <a class="open-pimcore" style="margin-left:10px;" data-no-pimocre-hide="1"
                           href="javascript:pimcore.helpers.openObject(<?= $item->getId() ?>,'object');"
                           title="<?= $this->translate('backoffice_webfrontend_open_pimcore') ?>"><img
                                style="position: relative;top: -3px;"
                                src="/pimcore/static6/img/flat-color-icons/cursor.svg" alt="open" width="23"/></a>
                    </td>
                </tr>
            <?
            } ?>
            </tbody>
        </table>
        <?php

    }else{
        ?>

        <div class="alert alert-danger"><?=$this->t('backoffice_no_results')?></div>
    <?php }



?>


    <? if($paginator->getPages()->pageCount > 1){ ?>
        <div class="text-center">
            <?= $this->paginationControl($paginator, 'Sliding', 'includes/pagination/default.php', $this->getAllParams()); ?>
        </div>
    <? }


    }
?>


