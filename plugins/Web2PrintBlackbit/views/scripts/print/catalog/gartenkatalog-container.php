<?php


$color = $this->getProperty('color');

$children = (array)$this->allChildren;

?>

<?php  if($chapter = $this->getProperty('chapter')){
    \Web2PrintBlackbit\Helper\Catalog::$chapterCount++;
    ?>

<div class="page">

    <div class="rubric--left rubric-<?=strtolower($this->getProperty('color'))?>">
        <div class="rubric">
            <? if (strtolower($this->getProperty('color')) != 'schaedlingsschutz') { ?>
                <div class="icon" style="right: 0; left: unset;"><img src="/plugins/Web2PrintBlackbit/static/img/<?=$this->getProperty('color')?>_L.svg"/></div>
            <?}?>
            <div class="headline"><p><?=$this->getProperty('chapter')?></p></div>
        </div>

        <div class="cut"></div>

    </div>

    <div class="rubric--right rubric-<?=strtolower($this->getProperty('color'))?>">
        <div class="rubric rubric-<?=strtolower($this->getProperty('color'))?>">
            <? if (strtolower($this->getProperty('color')) != 'schaedlingsschutz') { ?>
                <div class="icon" style="left: 0; right: unset;"><img src="/plugins/Web2PrintBlackbit/static/img/<?=$this->getProperty('color')?>_R.svg"/></div>
            <?}?>
             <div class="headline"><p><?=$this->getProperty('chapter')?></p></div>
        </div>

        <div class="cut"></div>

    </div>

        <div class="<?=$this->getProperty('color')?> chapter-wrapper no-container">

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