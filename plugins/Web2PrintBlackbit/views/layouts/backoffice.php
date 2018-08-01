<!DOCTYPE html>
<html lang="en">
<head>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="/plugins/Windhager/static-backoffice/css/lib/bootstrap.min.css">

    <link href="/plugins/Windhager/static-backoffice/css/back-office-webfrontend.css" rel="stylesheet">
    <link href="/plugins/Windhager/static-backoffice/font-awesome-4.7.0/css/font-awesome.css" rel="stylesheet">
    <!-- Optional theme -->
    <link rel="stylesheet" href="/plugins/Windhager/static-backoffice/css/lib/bootstrap-theme.min.css">

    <link href="//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">
    <link rel="stylesheet" href="/plugins/Windhager/static-backoffice/jqgrid/css/ui.jqgrid.css">
    <link rel="stylesheet" href="/plugins/Windhager/static-backoffice/jqgrid/css/jqgrid.bootstrap.css">
    <link rel="stylesheet" href="/plugins/Windhager/static-backoffice/select2/dist/css/select2.css">

    <link rel="stylesheet" href="/plugins/Windhager/static-backoffice/css/workflow-list.css">
    <script>
        var pimcore = parent.pimcore;
    </script>
    <?=$this->headLink()?>

    <!-- Latest compiled and minified JavaScript -->

    <script src="/plugins/Windhager/static-backoffice/jqgrid/js/jquery-1.11.0.min.js"></script>
    <script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="/plugins/Windhager/static-backoffice/js/datepicker-de.js"></script>
    <script src="/plugins/Windhager/static-backoffice/jqgrid/js/jquery.jqGrid.src.js"></script>
    <script src="/plugins/Windhager/static-backoffice/jqgrid/js/i18n/grid.locale-en.js"></script>
    <script src="/plugins/Windhager/static-backoffice/js/bootstrap.min.js"></script>
    <script src="/plugins/Windhager/static-backoffice/js/jquery.deparam.js"></script>
    <script src="/plugins/Windhager/static-backoffice/select2/dist/js/select2.full.js"></script>
</head>
<body>
<?= $this->layout()->content ?>

<?=$this->headScript()?>
</body>
</html>