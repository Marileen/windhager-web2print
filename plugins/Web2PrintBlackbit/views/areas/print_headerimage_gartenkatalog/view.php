
<? if($this->editmode){?>
    <div>
        <b>Kapitelbild:</b>
        <?=$this->input('headline')?>
        <?=$this->image("headerImage"); ?>
    </div>
<?}else{
    if(!empty($this->image("headerImage"))){
        ?>
        <div class="chapter-title-image">
            <?=$this->image("headerImage"); ?>
        </div>

    <?}}?>
