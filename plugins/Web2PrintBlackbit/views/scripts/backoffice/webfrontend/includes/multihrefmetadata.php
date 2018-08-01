<?
$product = $this->product;
$element = $this->element;

$hasMetadata = false;
if($element instanceof \Pimcore\Model\Object\ClassDefinition\Data\MultihrefMetadata){
    $hasMetadata = true;
}

$key = $this->key;
$displayData = \Web2PrintBlackbit\Helper\Webfrontend::getDisplayData($product, $element);

$mixed = $element->getAssettypes()[0] == null;

if (!is_null($displayData['value'])) { ?>

    <div class="panel panel-default">
        <div class="panel-heading detail-expand" role="tab" id="heading<?= $key ?>">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
                   href="#collapse<?= $key ?>" aria-controls="collapse<?= $key ?>">
                    <?= $displayData['name'] ?>
                </a>
            </h4>
        </div>

        <div id="collapse<?= $key ?>" class="panel-collapse collapse" role="tabpanel"
             aria-labelledby="heading<?= $key ?>">
            <div class="panel-body">
                <? if (!$mixed) {
                    foreach ($displayData['value'] as $index => $value) {
                        $hasMetadata ? $img = $displayData['value'][$index]->getElement() : $img = $displayData['value'][$index]; ?>
                            <div class="panel-heading detail-expand" role="tab" id="heading">
                                <h4 class="panel-title">
                                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
                                       href="#collapse" aria-controls="collapse">
                                        <div class="col-xs-3">
                                            <a href="<?= $img->getThumbnail('product-detail-big') ?>"
                                               data-toggle="lightbox">
                                                <img src="<?= $img->getThumbnail('shop-cart-list') ?>" alt=""/>
                                            </a>
                                        </div>
                                    </a>
                                </h4>
                            </div>
                        <?
                    }
                } else { ?>
                    <div class="panel-heading detail-expand" role="tab" id="heading">
                        <h4 class="panel-title">
                            <table class="table table-striped table-hover x-btn-click">
                                <thead>
                                <td><?= $this->t('backoffice_webfrontend_label_id') ?></td>
                                <td><?= $this->t('backoffice_webfrontend_label_size') ?></td>
                                </thead>
                                <tbody>
                                <? foreach ($displayData['value'] as $index => $value) {
                                    $hasMetadata ? $asset = $displayData['value'][$index]->getElement() : $asset = $displayData['value'][$index]; ?>
                                    <a href="<?= $this->document->getProperty('downloadPage') ?>?id=<?= $asset->getId() ?>">
                                        <tr class="goto">
                                            <td >
                                                <a href="<?= $this->document->getProperty('downloadPage') ?>?id=<?= $asset->getId() ?>" style="display: none;"></a>
                                                <?= $asset->getId() ?></td>
                                            <td><?= $asset->getFileSize('KB') ?> </td>
                                        </tr>
                                    </a>

                                <? } ?>
                                </tbody>
                            </table>
                        </h4>
                    </div>
                <? } ?>
            </div>
        </div>
    </div>
<? } ?>