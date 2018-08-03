
<? if($this->editmode){?>
    <div>
        <b>Kapitelbild:</b>
        <?=$this->input('headline')?>
        <?=$this->image("headerImage"); ?>
    </div>
<?}else{
    if(!empty($this->image("headerImage"))){
        ?>
        <div class="chapter-title-image" style="height: 72.5mm">
            <?=$this->image("headerImage"); ?>
        </div>

    <?}}?>
