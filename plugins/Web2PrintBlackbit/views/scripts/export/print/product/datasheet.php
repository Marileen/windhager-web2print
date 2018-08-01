<?
/**
 * @var $product \Web2PrintBlackbit\Product
 */
$product = $this->product;
if($this->product) {?>
    <? \Web2PrintBlackbit\Helper\PdfTemplateSelector::embedTemplateForDatasheet($this->product, $this) ?>
<? } else {


    ?>
    <? $this->template('export/print/product/datasheet/default.php') ?>
<? } ?>

