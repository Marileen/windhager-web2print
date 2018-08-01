<?
/**
 * @var \Web2PrintBlackbit\Controller\Assistant\Webfrontend $assistant
 */
$assistant = $this->assistant;
?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Windhager Webfrontend</title>

    <!-- Bootstrap core CSS -->
    <link href="/plugins/Windhager/static/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/plugins/Windhager/static-backoffice/select2/dist/css/select2.css">
    <link rel="stylesheet" href="/plugins/Windhager/static-backoffice/css/lib/ekko-lightbox.css">

    <!-- Custom styles for this template -->
    <link href="/plugins/Windhager/static-backoffice/css/back-office-webfrontend.css" rel="stylesheet">
    <link href="/plugins/Windhager/static-backoffice/font-awesome-4.7.0/css/font-awesome.css" rel="stylesheet">
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>

    <![endif]-->

    <script src="/plugins/Windhager/static-backoffice/jqgrid/js/jquery-1.11.0.min.js"></script>
    <script src="/plugins/Windhager/static-backoffice/js/bootstrap.min.js"></script>

    <script src="/plugins/Windhager/static-backoffice/js/jquery.deparam.js"></script>
    <script src="/plugins/Windhager/static-backoffice/js/ekko-lightbox.js"></script>
    <script src="/plugins/Windhager/static-backoffice/select2/dist/js/select2.full.js"></script>
    <script src="/plugins/Windhager/static-backoffice/js/webfrontend.js"></script>

    <script>
        var pimcore = parent.pimcore;
    </script>
</head>
<body>
<? if(!$this->document->getProperty('isLoginPage')){?>
    <div class="navbar navbar-default" role="navigation">
        <div class="container">

            <div class="navbar-header">
                <a href="<?=$this->getProperty('home')?>" class="navbar-brand dropdown-toggle"><span class="glyphicon glyphicon-star"></span> <?=$this->t('backoffice_webfrontend')?></a>
            </div>

            <div class="navbar-collapse collapse">
                <ul class="nav navbar-nav navbar-right">
                    <?
                    $session  = $assistant->getSession();
                    if($session->userType != 'pimcore'){
                    ?>
                    <li>
                        <a href="<?=$this->url(['controller' => 'backoffice_webfrontend','action' => 'login','doLogout' => '1'],'action',true)?>"><span class="glyphicon glyphicon-log-out color-green"></span> <?=$this->t('backoffice_webfrontend_button_logout')?></a>
                    </li>
                    <?}?>
                </ul>
            </div>
        </div>
    </div>
<?}?>

<? if(!$this->document->getProperty('isLoginPage')){?>
<div class="container">
<?}?>

<?=$this->layout()->content?>
<? if(!$this->document->getProperty('isLoginPage')){?>
    </div>
<?}?>
</body>
</html>

