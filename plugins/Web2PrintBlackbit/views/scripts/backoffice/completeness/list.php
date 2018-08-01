<?
$this->layout()->setLayout('backoffice');
?>

<? $this->headScript()->appendFile('/plugins/Windhager/static-backoffice/js/completeness.js');?>


<?
if(false){
    $this->template('backoffice/includes/nav.php');
}

?>
<!-- Modal -->
<div id="schedule_modal_wrapper">

</div>



<div class="content">
    <div class="row">
        <div class="col-xs-12">
            <h1 style="margin-top: 0;"><?=$this->ts('backoffice_completeness')?>
                <a class="btn btn-default clear-all-link pull-right" id="reloadGrid" style="margin-left: 10px;"><span class="glyphicon glyphicon-refresh color-green"></span> <?=$this->ts('backoffice_button_reload')?></a>

            </h1>
        </div>
    </div>
</div>


<div class="panel panel-default">
    <div class="panel-heading">

        <div class="row">
            <div class="col-xs-10">
                &nbsp;        <b style="font-size: 1.2em;"><?=$this->ts('backoffice_filter')?></b>

            </div>
            <div class="col-xs-2">
                <a class="btn btn-default btn-xs clear-all-link pull-right" ><span class="glyphicon glyphicon-remove"></span> <?=$this->ts('backoffice_clear_filters')?></a>
            </div>
        </div>

    </div>
    <div class="panel-body" >
        <form class="form-horizontal" id="filterForm">
            <div class="row">
                <div class="col-xs-6">
                    <div class="form-group">
                        <label for="path" class="col-sm-2 control-label"><?=$this->ts('backoffice_filter_path')?></label>
                        <div class="col-sm-10">
                            <select name="path" class="select2 js-states form-control pathFilter js-filter" id="path">
                                <option value=""><?=$this->ts('backoffice_filter_choose')?></option>
                                <? foreach($this->tree as $e){?>
                                    <option value="<?=$e['path']?>"> <?=$e['name']?></option>
                                <?}?>
                            </select>
                        </div>
                    </div>
                </div>


            </div>
            <div class="row">
                <div class="col-xs-6">
                    <div class="form-group">
                        <label for="path" class="col-sm-2 control-label"><?=$this->ts('backoffice_filter_roles')?></label>
                        <div class="col-sm-10">
                            <?
                            $fieldMatrix = \Web2PrintBlackbit\Helper\ProductLayout::getFieldMatrix();
                            $roles = [];

                            foreach(array_keys($fieldMatrix['roleFields']) as $roleId){
                                 if($fieldMatrix['userRoles'][$roleId]){
                                     $roles[$roleId] = $fieldMatrix['userRoles'][$roleId];
                                 }
                            }
                            asort($roles);
                            ?>
                            <select name="roles[]" class=" js-states form-control js-filter" id="roles" multiple style="width: 200px;height: <?=count($roles)*17+15?>px">
                                <? foreach($roles as $roleId => $role){ ?>
                                   <option value="<?=$roleId?>"><?=$role?></option> 
                                <? }?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6 col-xs-offset-1">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="showScheduled" value="1" class="js-filter js-states"/>
                            <?=$this->ts('backoffice_filter_show_scheduled')?>
                        </label>
                    </div>
                </div>
            </div>

        </form>

    </div>
</div>


<div class="grid-wrapper">
    <table id="workflowList"></table>
    <div id="workflowListPager"></div>
    <div id="filter"></div>
</div>

<table id="grid"></table>
<script type="text/javascript">
    var colNames = [
        '<?=$this->ts('colname_productId')?>',
        '<?=$this->ts('colname_articleId')?>',
        '<?=$this->ts('colname_title')?>',
        '<?=$this->ts('colname_responsible')?>',
        '<?=$this->ts('colname_hint')?>',
        <?
        $channelConfig= include \Pimcore\Config::locateConfigFile("channels.php");

    foreach($channelConfig['channels'] as $key => $channelEntry){
        $layout = \Pimcore\Model\Object\ClassDefinition\CustomLayout::getById($channelEntry['customLayoutId']);
        echo "'".$layout->getName()."',";
    }?>
        '<?=$this->ts('colname_open')?>',

        'childRow'
    ];

    var colModel = [
        {name:'o_parentId',index:'o_parentId',sortable : false,width: 30,searchoptions:{clearSearch:false}},
        {name:'art_no',index:'art_no',width: 30,sortable : false,searchoptions:{clearSearch:false}},
        {name:'title',classes: 'cell-large', index:'title', width: 70,sortable : false,searchoptions:{clearSearch:false}},
        {name:'responsible', classes:'cell-large', width: 40,search:false,sortable : false,index:'responsible',searchoptions:{clearSearch:false}},
        {name:'hint',classes:'cell-large',search:false,sortable : false,index:'hint',searchoptions:{clearSearch:false}},
<?
        foreach($channelConfig['channels'] as $key => $channelEntry){?>
        {name:'<?=$key?>',index:'<?=$key?>',sortable : false, align: "center", width:40,  stype:'select',searchoptions:{clearSearch:false,value:":<?=$this->ts('backoffice_state_all')?>;ready:<?=$this->ts('backoffice_state_ready')?>;notReady:<?=$this->ts('backoffice_state_not_ready')?>;ignore:<?=$this->ts('backoffice_state_ignore')?>"}},
        <?}
        ?>
        {name:'open',width: 25,index:'open',align: 'center', stype:'select',searchoptions:{clearSearch:false,search:false}},
        {name:'childRow',index:'childRow',width: 40,searchoptions:{clearSearch:false},hidden: true}
    ];


    var archiveMode = false;
</script>
