<?
/**
 * @var \Web2PrintBlackbit\Product $product
 */

$entry = $this->entry;
$fieldMatrix = $this->fieldMatrix;
$channelConfig= include \Pimcore\Config::locateConfigFile("channels.php");

$product = $this->product;

?>

<table class="table gridChildTable" style="width: auto;float: right;border-collapse: collapse;">
    <tbody>


    <?
    $i = 0;
    foreach($fieldMatrix['channelRoleIds'] as $roleId){?>
        <tr class="row<?=++$i?>">
            <td class="girdChild_responsible"><?=$fieldMatrix['userRoles'][$roleId]?></td>
            <td class="girdChild_hint">
                <?
                if($tmp = (array)$entry['missingFields']['roleMissing'][$roleId]){
                    echo '<b>'.$this->ts('backoffice_missing_fields:') . ':</b> ' . implode(', ',(array)$entry['missingFields']['roleMissing'][$roleId]);
                }

                ?>
            </td>
            <?

            foreach($channelConfig['channels'] as $key => $channelEntry){?>
                <td class="girdChild_<?=$key?> text-center">

                    <?
                    $channelMissing = $entry['missingFields']['channelMissing'][$key];
                    $roleMissing = (array)$entry['missingFields']['roleMissing'][$roleId];
                    $tmp = array_intersect($channelMissing,$roleMissing);
                    if($tmp){

                        $s = '<ul class=\'popup-missing-list\'>';
                        foreach ($tmp as $v){
                            $s .= '<li>' . $v . '</li>';
                        }
                        $s .= '</ul>';
                        ?>
                        <a href="javascript:pimcore.helpers.openObject(<?=$product->getId()?>, 'object',{layoutId : <?=$channelEntry['customLayoutId']?>});" class="no-text-decoration glyphicon glyphicon-minus-sign color-red" data-toggle="popover"  aria-hidden="true" title="<?=$this->ts('backoffice_missing_fields:')?>" data-content="<?=$s?>"></a>
                    <?}else{?>
                        <a href="javascript:pimcore.helpers.openObject(<?=$product->getId()?>, 'object',{layoutId : <?=$channelEntry['customLayoutId']?>});" class="no-text-decoration glyphicon glyphicon-ok color-green" aria-hidden="true" title="ready"></a>
                    <?}?>

                </td>

            <?}?>
            <td class="girdChild_open text-center"><a class="fa fa-clock-o schedule remote-modal" aria-hidden="true" href="/plugin/Windhager/Backoffice_Completeness/schedule?role=<?=$roleId?>&o_id=<?=$product->getId()?>"></a>

                <?
                $e = \Pimcore\Db::get()->fetchRow('SELECT * FROM windhager_completeness_schedule WHERE o_id = ? AND role = ?',[$product->getId(),$roleId]);
                if($e){
                    $date = \Carbon\Carbon::createFromTimestamp($e['date']);
                    echo $date->format('d.m.Y');
                }
                ?>


            </td>
        </tr>
    <?}?>
    </tbody>

</table>
