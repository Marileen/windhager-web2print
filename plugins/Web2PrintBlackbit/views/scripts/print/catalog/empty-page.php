<?php  if($this->editmode){?>
    <link rel="stylesheet" type="text/css" href="/plugins/Windhager/static/css/print/catalog/editmode.css">
    <b>Anzahl an Leerseiten:</b>
    <?=$this->numeric('pages')?>
<?}else{
    $pages = $this->numeric('pages')->getValue();

    for($i = 0; $i < $pages; $i++){?>
        <div class="emptyPage">&nbsp;</div>
    <?php } ?>

<?php } ?>


