<div class="outer">
    <div class="middle">
        <div class="inner" style="background-color: #f00;">

            <div id="loginbox" class="mainbox col-md-12" style="margin: 0 auto;">
                <div class="panel panel-default" >
                    <div class="panel-heading">
                        <div class="panel-title"><?=$this->t('backoffice_webfrontend_login_title')?></div>
                    </div>

                    <div  class="panel-body" >
                        <div style="display:none" id="login-alert" class="alert alert-danger col-sm-12"></div>

                        <form id="loginform" class="form-horizontal" role="form" action="<?=$this->document?>" method="post">

                            <? if($this->error){?>
                                <div class="alert alert-danger"><?=$this->t($this->error)?></div>
                            <?}

                            if($tKey = $this->getParam('message')){?>
                                <div class="alert alert-success"><?=$this->t($tKey)?></div>
                            <?}?>
                            <div class="input-group margin-bottom-20">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                <input id="login-username" type="text" class="form-control" name="email" value="" placeholder="<?=$this->t('backoffice_webfrontend_placeholder_email')?>">
                            </div>

                            <div class="input-group margin-bottom-20">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                                <input id="login-password" type="password" class="form-control" name="password" placeholder="<?=$this->t('backoffice_webfrontend_placeholder_password')?>">
                            </div>

                            <div class="form-group margin-bottom-0">
                                <div class="col-sm-12 controls">
                                    <input type="submit" class="btn btn-success full-width" value="<?=$this->t('backoffice_webfrontend_label_login')?>"/>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>