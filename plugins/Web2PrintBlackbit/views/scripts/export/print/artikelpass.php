<?php
/**
 * @var \Web2PrintBlackbit\Product $product
 */
$product = $this->product;
?>
<link rel="stylesheet" type="text/css" href="/plugins/Windhager/static6/css/artikelpass.css" media="all"/>
<script type="text/javascript" src="/static/js/min/010_jquery.1.11.1.min.js"></script>
<div id="artikelpass">
    <div class="header page-header">
        <div class="header__art-nr"><?=$product->getArt_no()?></div>
        <div class="header__art-name"><?=$product->getEsbTitle1()?></div>
    </div>

    <div class="footer page-footer">
        <div class="clearfix">
            <?php
            $date = new \Zend_Date();

            $parts = [];
            $parts[] = $date->get(\Zend_DATE::DATE_MEDIUM);
            $parts[] = $product->getArt_no();
            $parts[] = $product->getEsbTitle1();
            ?>
            <div class="footer__left"><?=implode(', ',array_filter($parts))?></div>
            <div class="footer__right">
            <!--  page numbers are filled out by css/pdfReactor -->
                <span class="page-num"></span>/<span class="page-num-total"></span>
            </div>
        </div>
    </div>


    <!-- General Info -->
    <div class="short-info-wrapper clearfix">
        <table class="table--short-info">
            <?php

            $displayValues = [];

            if($value = $product->getArt_no()){
                $displayValues['matnr'] = $value;

            }

            $value = trim($product->getEsbTitle1().' ' . $product->getEsbTitle2());
            if($value){
                $displayValues['title'] = $value;
            }


            $value = [];
            foreach (['item_length', 'item_width', 'item_height'] as $field) {
                if ($v = $product->{"get" . ucfirst($field)}()) {
                    $value[] = $this->formatter()->number($v);
                }
            }
            $value = implode(' x ', array_filter($value));

            if ($value) {
                $unit = $product->getSize_unit();
                if ($unit instanceof \Web2PrintBlackbit\AttributeUnit) {
                    $value .= ' ' . $unit->getTitle();
                }
            }

            if ($value) {
                $displayValues['dimension'] = $value;
            }

            $attributes = $product->getPmtMarketingAttributes();


            $attributeWeight = $attributes->getPMTAttributeWeight();
            if($attributeWeight instanceof \Pimcore\Model\Object\Objectbrick\Data\PMTAttributeWeight){
                if ($value = $attributeWeight->getItem_weight()) {
                    $displayValues['weight'] = $this->formatter()->number($value) . ' kg';
                }
            }

            $diameter = $attributes->getPMTAttributeDiameter();

            if ($diameter instanceof \Pimcore\Model\Object\Objectbrick\Data\PMTAttributeDiameter) {
                if ($value = $diameter->getDiameter()) {
                    $unit = $diameter->getDiameter_unit();
                    if ($unit) {
                        $value .= ' ' . $unit->getTitle();
                    }
                    $displayValues['diameter'] = $this->formatter()->number($value);
                }
            }

            $thickness = $attributes->getPMTAttributeThickness();
            if ($thickness instanceof \Pimcore\Model\Object\Objectbrick\Data\PMTAttributeThickness) {
                if ($value = $thickness->getThickness()) {
                    $value = $this->formatter()->number($value);
                    if ($unit = $thickness->getThickness_unit()) {
                        $value .= ' ' . $unit->getTitle();
                    }
                    $displayValues['thickness'] = $value;
                }
            }

            $mesh = $attributes->getPMTAttributeMesh();
            if ($mesh instanceof \Pimcore\Model\Object\Objectbrick\Data\PMTAttributeMesh) {

                $tmp  = [];

                if ($value = $mesh->getMeshWidth()) {
                    $tmp[] = $this->formatter()->number($value) . ' mm';
                }

                if ($value = $mesh->getMeshHeight()) {
                    $tmp[] = $this->formatter()->number($value) . ' mm';
                }

                $string = implode(' x ',$tmp);

                if($string){
                    $displayValues['mesh'] = $string;
                }

            }

            $grammage = $attributes->getPMTAttributeGrammage();
            if ($grammage instanceof \Pimcore\Model\Object\Objectbrick\Data\PMTAttributeGrammage) {
                if ($value = $grammage->getGrammage()) {
                    $displayValues['grammage'] = $this->formatter()->number($value) . ' g/m²';
                }
            }

            $opaque = $attributes->getPMTAttributeOpaque();
            if ($opaque instanceof \Pimcore\Model\Object\Objectbrick\Data\PMTAttributeOpaque) {
                if ($value = $opaque->getOpaque()) {
                    $displayValues['opaque'] = $this->formatter()->number($value) . ' %';
                }
            }

            foreach($displayValues as $key => $value){?>

                <tr>
                    <td><?=$this->t('lbl_articlepass_'.$key)?></td>
                    <td><?=$value?></td>
                </tr>
            <?php
            }

            ?>

        </table>
        <div class="product-image">
            <?
            $imageData = $product->getImages_packing_main();
            if($imageData && $imageData[0]['image']){
                if($image = $imageData[0]['image']->getData()){
                    echo $image->getThumbnail('articlepass-main',false)->getHTML(['class' => 'img-responsive img-responsive--vert']);
                }
            }
            ?>
        </div>
    </div>


        <? if($value = $product->getSizeDescription()){?>
            <table class="table--product-info hide-empty-columns table--full-width">
                <thead>
                <tr>
                    <th><?=$this->t('lbl_articlepass_size_description')?></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><?=$value?></td>
                </tr>
                </tbody>
            </table>
        <?}?>


        <?php

        $content = $product->getPmtMarketingAttributes()->getPmtAttributeContent();

        if($content instanceof \Pimcore\Model\Object\Objectbrick\Data\PMTAttributecontent){
            $displayValues = [];

            if($v = $content->getContents()){
                $displayValues['content'] = $this->formatter()->number($v);
                $unit = $content->getContents_unit();
                if($unit instanceof \Web2PrintBlackbit\AttributeUnit){
                   $displayValues['content_unit'] = $unit->getTitle();
                }
            }

            if($v = $product->getContents_text()){
                $displayValues['content_description'] = $v;
            }

            $displayValues = array_filter($displayValues);

            if($displayValues){
                ?>
                <table class="table--product-info hide-empty-columns table--full-width">
                    <thead>
                    <tr>

                        <?php foreach(array_keys($displayValues) as $key){?>
                            <th width="33%"><?=$this->t('lbl_articlepass_' . $key)?></th>
                        <?php } ?>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                    <?php foreach($displayValues as $value){?>
                        <td><?=$value?></td>
                    <?php }?>
                    </tr>
                    </tbody>
                </table>
            <?php }} ?>


        <?php
        /**
         * @var \Web2PrintBlackbit\AttributeColor $value
         */
        if($value = $product->getMain_color()){
            if($name = $value->getTitle()){
                $hex = $value->getCode();
            ?>
            <table class="table--product-info hide-empty-columns table--full-width">
                <thead>
                <tr>
                    <th><?=$this->t('lbl_articlepass_size_main_color')?></th>
                    <?php if($hex){?>
                    <th><?=$this->t('lbl_articlepass_size_main_color_hex')?></th>
                    <?php } ?>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><?=$name?></td>
                    <?php if($hex){?>
                        <td><?=$hex?></td>
                    <?php }?>
                </tr>
                </tbody>
            </table>
        <?php }} ?>

    <!-- Regular tables -->
    <h2><?=$this->t('lbl_articlepass_description_headline')?></h2>

    <?php if($value = $product->getDescription_articlepass()){
        ?>
        <table class="table--product-info hide-empty-columns table--full-width">
            <thead>
                <tr>
                    <th><?=$this->t('lbl_articlepass_description')?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?=$value?></td>
                </tr>
            </tbody>
        </table>
    <?php } ?>


    <?php if($materials = $product->getMaterials_articlepass()){

        $displayValues = [];

        foreach($materials as $mat){
            $tmp = [];
            foreach(['name','description','origin','color'] as $key){
                $tmp[$key] = $mat[$key]->getData();

                if($tmp[$key] && $key == 'origin'){
                    $tmp[$key] = \Zend_Locale::getTranslationList('territory')[$tmp[$key]];
                }
                if($key =='color'){
                    if($tmp[$key] instanceof \Web2PrintBlackbit\AttributeColor){
                       # $tmp[$key] = \Windhager\AttributeColor::getById($tmp[$key]->getId());
                        $tmp['colorcode'] = $tmp[$key]->getCode();
                    }else{
                        $tmp['colorcode'] = '';
                    }
                }
            }
            if(array_filter($tmp)){
                $displayValues[] = $tmp;
            }
        }

        if($displayValues){
        ?>
        <table class="table--product-info hide-empty-columns table--full-width">
            <thead>
            <tr>
                <?php foreach(array_keys($displayValues[0]) as $key){?>
                        <th
                        <?php if($key == 'description'){ ?>
                            width="50%"
                        <? }?>
                        ><?=$this->t('lbl_articlepass_materials_'.$key)?></th>
                <?php } ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach($displayValues as $e){?>
            <tr>
                <?php foreach(array_keys($e) as $key){?>
                    <td><?=$e[$key]?></td>
                <?php } ?>
            </tr>
            <?php }?>
            </tbody>
        </table>
    <?php
        }
    } ?>
    <?php

        $attributes = $product->getAttributes();

        $wood = $attributes->getWoodProduct();
        if($wood instanceof \Pimcore\Model\Object\Objectbrick\Data\WoodProduct){

            $holz = $wood->getHolz();
            $displayValues = [];

            foreach((array)$holz as $entry){
                /**
                 * @var \Pimcore\Model\Object\AttributeWoodType $woodType
                 */
                if($woodType = $entry['woodType']->getData()){

                    $origin = $entry['originCountry']->getData();

                    $displayValues[] = [
                        'name' => $woodType->getName(),
                        'originCountry' => \Zend_Locale::getTranslation($origin, 'territory'),
                    ];
                }
            }


            if($displayValues){ ?>
                <table class="table--product-info hide-empty-columns table--full-width">
                    <thead>
                    <tr>
                        <th><?=$this->t('lbl_articlepass_wood_type')?></th>
                        <th width="50%"><?=$this->t('lbl_articlepass_wood_country')?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($displayValues as $entry){?>
                        <tr>
                            <td><?=$entry['name']?></td>
                            <td><?=$entry['originCountry']?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>

            <?php

            }
        }


        $electric  = $attributes->getElectricalProduct();
        if($electric instanceof \Pimcore\Model\Object\Objectbrick\Data\ElectricalProduct){
            $displayValues = [];

            foreach(['amperage','powerOutput','frequence','voltage','batteryType','batteryQuality'] as $key){
                if($v = $electric->{"get" . ucfirst($key)}()){
                    if(in_array($key,['batteryType','batteryQuality'])){
                        $v = $this->t('lbl_articlepass_option_' . $key.'_'.$v);
                    }
                    $displayValues[$key] = $v;
                }
            }

            if($displayValues){?>

                <table class="table--product-info hide-empty-columns table--full-width">
                    <thead>
                    <tr>
                        <?php foreach(array_keys($displayValues) as $key){?>
                            <th><?=$this->t('lbl_articlepass_'.$key)?></th>
                        <?php } ?>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <?php foreach(array_keys($displayValues) as $key){?>
                            <td><?=$displayValues[$key]?></td>
                        <?php } ?>
                    </tr>
                    </tbody>
                </table>

            <?php }

        }

        $displayValues = [];
        $values = $product->getInhaltsstoffe();
        foreach((array)$values as $e){
            /**
             * @var \Pimcore\Model\Object\AttributeIngredients $o
             */
            if($o = $e['Ingredients']->getData()){
                if($o->getTitle()){
                    $displayValues[] = ['title' => $o->getTitle(),'percentage' => $e['percent']->getData()];

                }
            }
        }

        if($displayValues){?>

            <table class="table--product-info hide-empty-columns table--full-width">
                <thead>
                <tr>
                    <th><?=$this->t('lbl_articlepass_ingredients')?></th>
                    <th width="30"><?=$this->t('lbl_articlepass_ingredients_percentage')?></th>
                </tr>
                </thead>
                <tbody>
                    <?php foreach($displayValues as $e){?>
                        <tr>

                            <td><?= $e['title'] ?></td>
                            <td><?= $e['percentage'] ?></td>
                        </tr>

                    <?php } ?>
                </tbody>
            </table>
        <?}

        $displayValues = [];
        $values = $product->getNamecertificate();
        if(!$values){
           # var_dump($values); exit;

        }
    /**
     * @var $e \Pimcore\Model\Object\AttributeCertificateType
     */
        foreach((array)$values as $e){
            if($v = $e->getTitle()){
                $displayValues[] = $v;
            }
        }

        if($displayValues){?>
            <table class="table--product-info hide-empty-columns table--full-width">
                <thead>
                <tr>
                    <th><?=$this->t('lbl_articlepass_required_certificate'. (count($displayValues) > 1 ? 's' : ''))?></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><?= implode(', ',$displayValues)?></td>
                </tr>
                </tbody>
            </table>
        <?php }


    $displayValues = [];
    foreach (['widthBME', 'heightBME', 'depthBME', 'weightLooseArticleBME', 'weightTotalBME'] as $key) {

        $value = $product->{"get" . ucfirst($key)}();
        if ($value) {

            if (in_array($key, ['widthBME', 'heightBME', 'depthBME'])) {
                $value .= ' cm';
            } else {
                $value .= ' kg';
            }
            $value = $this->formatter()->number($value);
            $displayValues[$key] = $value;
        }
    }

    $this->template('export/print/includes/table.php', ['displayValues' => $displayValues,
        'headline' => 'lbl_articlepass_headline_sales_unit',
        'note' => 'lbl_articlepass_note_sales_unit'
    ], true);


    $displayValues = [];
    foreach (['vke', 'widthVKE', 'heightVKE', 'depthVKE', 'weightNetVKE', 'weightGrossVKE'] as $key) {

        $value = $product->{"get" . ucfirst($key)}();
        if ($value) {
            if ($key != 'vke') {
                if (in_array($key, ['widthVKE', 'heightVKE', 'depthVKE'])) {
                    $value .= ' cm';
                } else {
                    $value .= ' kg';
                }
            }
            $value = $this->formatter()->number($value);
            $displayValues[$key] = $value;
        }
    }
    $this->template('export/print/includes/table.php', ['displayValues' => $displayValues, 'headline' => 'lbl_articlepass_headline_inner_carton_unit'], true);

    $displayValues = [];
    foreach (['amountArticlesVPE','amountVKEinVPE','widthVPE','heightVPE','depthVPE','weightNetVPE','weightGrossVPE'] as $key) {

        $value = $product->{"get" . ucfirst($key)}();
        if ($value) {
            if ($key != 'amountArticlesVPE' && $key != 'amountVKEinVPE') {
                if (in_array($key, ['widthVPE', 'heightVPE', 'depthVPE'])) {
                    $value .= ' cm';
                } else {
                    $value .= ' kg';
                }
            }
            $displayValues[$key] = $value;
        }
    }
    $this->template('export/print/includes/table.php', ['displayValues' => $displayValues, 'headline' => 'lbl_articlepass_headline_vpe'], true);


    $displayValues = [];
    foreach (['amountArticlesPAL', 'widthPAL', 'heightPAL', 'depthPAL', 'weightNetPAL', 'weightGrossPAL'] as $key) {

        $value = $product->{"get" . ucfirst($key)}();
        if ($value) {
            if ($key != 'amountArticlesPAL') {
                if (in_array($key, ['widthPAL', 'heightPAL', 'depthPAL'])) {
                    $value .= ' cm';
                } else {
                    $value .= ' kg';
                }
            }
            $displayValues[$key] = $value;
        }
    }
    $this->template('export/print/includes/table.php', ['displayValues' => $displayValues, 'headline' => 'lbl_articlepass_headline_pal'], true);



    $displayValues = [];

    if($product->getExportRawProducts()){
        foreach ((array)$product->getRawProducts() as $e){
            if(!$e->getIgnoreexport()){
                $o = $e->getObject();
                if($o instanceof \Web2PrintBlackbit\Product){
                    $displayValues[] = ['object' => $o,'amountinvpe' => $e->getAmountinvpe()] ;
                }
            }
        }

    }

    if($displayValues){?>

        <h2><?=$this->t('lbl_articlepass_headline_attachments')?></h2>
        <table class="table--product-info hide-empty-columns table--full-width">
            <thead>
            <tr>
                <th><?=$this->t('lbl_articlepass_artno')?></th>
                <th><?=$this->t('lbl_articlepass_title')?></th>
                <th><?=$this->t('lbl_articlepass_quantity')?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            /**
             * @var \Web2PrintBlackbit\Product $item
             */
            foreach($displayValues as $e){
                $item = $e['object'];
                ?>
                <tr>
                    <td><?=$item->getArt_no()?></td>
                    <td><?=$item->getEsbTitle1()?></td>
                    <td><?=$e['amountinvpe']?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php }

    $displayValues = \Web2PrintBlackbit\Exporter\Helper\Articlepass::getAssetsByCelumThumbnail((array)$product->getArticlePassAssets());
    if($displayValues){?>

        <h2><?=$this->t('lbl_articlepass_headline_files')?></h2>
        <table class="table--product-info hide-empty-columns table--full-width">
            <thead>
            <tr>
                <th><?=$this->t('lbl_articlepass_filename')?></th>
                <th><?=$this->t('lbl_articlepass_description')?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            /**
             * @var \Web2PrintBlackbit\Product $item
             */
            foreach($displayValues as $entry){

                ?>
                <tr>
                    <td>

                        <?
                        if($entry['data']){
                            $value = '';
                            if($entry['celumId']){
                                $x = new \Web2PrintBlackbit\Exporter\Product\Tools\Celum();
                                $assetData = $x->getThumbnailByConfig($entry['celumId'],null,$entry['format'],true);
                                $value = $assetData["filename"];
                                if($value == 'no_filename'){
                                    $value = "<b style='color:#f00;'>Couldn't get Thumbnail from Celum</b>";
                                }
                            }else{
                                $value = $entry['data']->getElement()->getFilename();
                            }
                            echo $value;
                        }else{
                            $files = [];
                            foreach($entry as $e){
                                $x = new \Web2PrintBlackbit\Exporter\Product\Tools\Celum();
                                $assetData = $x->getThumbnailByConfig($e['celumId'],null,$e['format'],true);
                                $value = $assetData["filename"];
                                if($value == 'no_filename'){
                                    $value = "<b style='color:#f00;'>Couldn't get Thumbnail from Celum</b>";
                                }
                                $files[] = $value;
                            }
                            echo implode(', ',$files);
                        }

                        ?>

                    </td>
                    <td><?
                        if($entry['data']){
                            echo $entry['data']->getDescription();
                        }else{
                            echo $entry[0]['data']->getDescription();
                        }

                        ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>

    <?php

    }


    $displayValues = \Web2PrintBlackbit\Exporter\Helper\Articlepass::getAssetsByCelumThumbnail((array)$product->getArticlePassImages());


    if($displayValues){?>
        <h2 style="page-break-after: avoid;"><?=$this->t('lbl_articlepass_headline_images')?></h2>
        <table class="table--product-info hide-empty-columns table--full-width images-table" style="">
            <thead>
            <tr>
                <th></th>
                <th><?=$this->t('lbl_articlepass_description')?></th>
            </tr>
            </thead>
            <tbody>
            <? foreach($displayValues as $entry){ ?>

                <tr>
                    <?php

                    if($entry['celumId']){

                        $x = new \Web2PrintBlackbit\Exporter\Product\Tools\Celum();
                        $thumb = $entry['data']->getThumbnail();

                        $assetData = $x->getThumbnailByConfig($entry['celumId'],null,$entry['format']);
                        $finfo = new finfo(\FILEINFO_MIME_TYPE);
                        $mime = $finfo->buffer($assetData);

                        ?>
                        <td class="image-col"><img src="data:<?=$mime?>;base64,<?=base64_encode($assetData)?>" class="img-responsive img-responsive--vert center-block" alt=""/></td>
                        <?php
                    }else{
                        ?>
                        <td class="image-col"><img src="<?=$entry['data']->getElement()?>" class="img-responsive img-responsive--vert center-block" alt=""/></td>
                    <?}?>


                    <td><?=$entry['data']->getDescription()?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php }?>
</div>
<script type="text/javascript">
    $(function(){
        var hideEmptyColumns= true;

        if(hideEmptyColumns){
            $('.hide-empty-columns').each(function(i,e) {
                var data = [];
                $(this).find('tr').each(function(rowIndex,e){
                    $(this).find('td').each(function(tdIndex,td){
                        if($(this).html()){
                            if(typeof data[tdIndex] == 'undefined'){
                                data[tdIndex] = [];
                            }
                            data[tdIndex].push($(this).html());
                        }
                    });
                });
                for(var i = 0; i < data.length; i++){
                    if(!data[i]){
                        $(this).find('tr').each(function(x,e){
                            if($(this).find('td , th').length){
                                $(this).find('td:eq(' + i + ') , th:eq(' + i + ')').hide();
                            }
                        });
                    }
                }
            });
        }

    })
</script>