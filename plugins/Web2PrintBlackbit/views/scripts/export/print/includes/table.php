<?php

$displayValues = $this->displayValues;
$headline =$this->headline;

?>

<?php

if($displayValues){
if($headline){?>
    <h2><?=$this->t($headline)?></h2>
<?php } ?>
<table class="table--product-info table--full-width" >
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
    <?php if($this->note){ ?>

    <div style="margin-bottom: 10mm;">
        <?=$this->t($this->note)?>
    </div>

    <?php } ?>
<?php } ?>