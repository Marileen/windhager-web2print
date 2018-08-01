<? if ($variant = $this->getParam('variant')) { ?>
    <? $this->template('print/product/ldl/' . $variant . '.php') ?>
<? } elseif($this->product) {?>
    <? \Web2PrintBlackbit\Helper\PdfTemplateSelector::embedTemplateForLDL($this->product, $this) ?>
<? } ?>

