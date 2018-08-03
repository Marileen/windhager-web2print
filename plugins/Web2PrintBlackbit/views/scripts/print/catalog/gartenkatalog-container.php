<?php


$color = $this->getProperty('color');

$children = (array)$this->allChildren;

?>

<?php  if($chapter = $this->getProperty('chapter')){
    \Web2PrintBlackbit\Helper\Catalog::$chapterCount++;

    /*$p = clone $this->document;

    while ($p->getParent() instanceof \Pimcore\Model\Document\PrintAbstract){
        $p = $p->getParent();
    }
    foreach($p->getChildren() as $i => $child){
        $i++;
        if($child->getId() == $this->document->getId()){
            \Windhager\Helper\Catalog::$chapterCount = $i;
        }
    }*/


    $chapterCount = \Web2PrintBlackbit\Helper\Catalog::getChapterCount($this->document);
    $wrapperWidth = 297;
    $infoTabWidth = 10;
    $singleTab = ($wrapperWidth - $infoTabWidth) / ($chapterCount-1);
    $tabWidth = \Web2PrintBlackbit\Helper\Catalog::$chapterCount * $singleTab;
    $remainderWidth = $wrapperWidth - $tabWidth;
//    TODO: Bitte $isIndex auf true setzen, wenn es das letzte Kapitel (Index) ist
    $isIndex = ($chapter == 'i');
    if ($isIndex) {
        $tabWidth = $wrapperWidth;
    }
    ?>

<div class="page rubric-growing rubric-<?=$this->getProperty('color')?> right">

        <div class="rubric chapter-tabs-wrapper">
            <? if (! strtolower($this->getProperty('color')) == 'schaedlingsschutz') { ?>
            <div class="icon"><img src="/plugins/Web2PrintBlackbit/static/img/<?=$this->getProperty('color')?>_L.svg"/></div>
            <?}?>
            <div class="headline"><p><?=$this->getProperty('chapter')?></p></div>
        </div>

        <div class="rubric chapter-tabs-wrapper--right" style="right: -2mm; left: unset;">
        <? if (! strtolower($this->getProperty('color')) == 'schaedlingsschutz') { ?>
            <div class="icon" style="left: 0; right: unset;"><img src="/plugins/Web2PrintBlackbit/static/img/<?=$this->getProperty('color')?>_R.svg"/></div>
        <?}?>
            <div class="headline"><p><?=$this->getProperty('chapter')?></p></div>
        </div>

        <div class="cut"></div>

        <div class="<?=$this->getProperty('color')?> chapter-wrapper container">

            <?php } ?>
                    <?php foreach ($this->allChildren as $child) {

                        ?>

                        <?php
                        if ($child instanceof \Pimcore\Model\Document\Hardlink) {
                            $child = \Pimcore\Model\Document\Hardlink\Service::wrap($child);
                        }
                        ?>

                        <?= $this->inc($child) ?>
                    <?php } ?>

            <?php  if($this->getProperty('chapter')){?>
        </div>
</div>
<?php }?>