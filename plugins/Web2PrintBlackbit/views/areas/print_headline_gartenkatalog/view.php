
<? if($this->editmode){?>
    <div>
        <b>Überschrift:</b>
        <?=$this->input('headline')?>
    </div>
<?}else{
    if($txt = $this->input('headline')->getValue()){
        ?>
        <h1 class="chapter-headline">
            <?=$txt?>
        </h1>
    <?}}?>
