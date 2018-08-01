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
                <h4 class="modal-title" id="myModalLabel"><?=$this->ts('backoffice_todo_resolved')?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                    <form  id="schedule_form" action="/plugin/Windhager/Backoffice_Todo/resolved">
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