<?php

/**
 * @var \Web2PrintBlackbit\TodoItem $item
 */
$item = $this->item;

?>
<div class="modal " id="schedule_modal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?=$this->ts('backoffice_todo_schedule')?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                    <form  id="schedule_form" action="/plugin/Windhager/Backoffice_Todo/schedule">
                        <div class="form-group form-inline">
                            <label for="nextReminderDate"><?=$this->ts('backoffice_todo_schedule_label')?></label>
                            <div class="datepicker-wrapper">
                                <?php
                                if($v = $item->getReminderDate()){
                                    $value = \Carbon\Carbon::createFromTimestamp($v->getTimestamp())->format('d.m.Y');
                                }else{
                                    $value = '';
                                }

                                ?>

                                <input type="text" class="form-control datepicker" required="required" id="reminderDate" name="reminderDate" value="<?=$value?>" placeholder="">
                                <span class="glyphicon glyphicon-calendar" style="position: relative;left:-25px;top:2px;"></span>
                            </div>
                            <small class="note"><?=$this->ts('backoffice_completeness_schedule_note')?></small>
                        </div>

                        <div class="form-group">
                            <label for="nextReminderDate"><?=$this->ts('backoffice_todo_schedule_note_label')?></label>
                            <textarea class="form-control" rows="3" name="note"><?=$item->getNote()?></textarea>
                        </div>

                        <input type="hidden" name="id" value="<?=$this->getParam('id')?>"/>
                        <input type="hidden" name="xAction" value="update" />
                    </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?=$this->ts('backoffice_completeness_schedule_cancel')?></button>
                <button type="button" class="btn btn-primary" id="applySchedule"><?=$this->ts('backoffice_completeness_schedule_save')?></button>
            </div>
        </div>
    </div>
</div>