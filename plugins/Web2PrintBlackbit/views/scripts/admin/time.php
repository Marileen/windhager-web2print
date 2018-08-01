<html>
<form method="post">
    <?
    $times = $this->times;
    for( $i = 0; $i <= 5; $i++){?>

        Time: <input name="time<?=$i?>" value="<?=$this->getParam('time'.$i)?>" autocomplete="off">
        <? if($times[$i]){
                $rest = $times[$i]-floor($times[$i]);
            ?>
                <?=floor($times[$i])?>:<?=str_pad($rest*60,2,'0',STR_PAD_LEFT)?>
            <?}?>
        <br/>
    <?}?>
    <input type="submit"/>
</form>

<?

if($this->totalMinutes){
    $hours = floor($this->totalMinutes/60);

    echo 'Total: ';

        ?>
        <span style="font-weight: bold;<?if($hours >= 8){?> color:#499b08<?}else{?> color: #f00;<?}?>" >
    <?
       echo $hours;
       echo ':';

       $minutes = $this->totalMinutes/60-floor($this->totalMinutes/60);
       echo str_pad($minutes*60,2,'0',STR_PAD_LEFT);


    $diff = $this->totalMinutes-60*8;

    if($diff <0){
        $diff *=-1;
        $sign = '-';
    }else{
        $sign = '+';
    }
    echo '<br/>Diff: '.$sign;

    echo floor($diff/60);
    echo ':';

    $x = $diff-(floor($diff/60)*60);
    echo str_pad($x,2,'0',STR_PAD_LEFT);
}
echo '<br/>';


?>
        </span>
<?php
if($sign == '-'){
    $date = new \Carbon\Carbon();
    $date->addMinutes($diff);
    echo 'Till: ' . $date->format('H:i');
}
?>
</html>