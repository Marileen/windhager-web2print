<!DOCTYPE html>
<html>
<head>

    <link rel="stylesheet" href="/plugins/Web2PrintBlackbit/static/css/print/catalog/style_windhager-catalogue.css">
    <link rel="stylesheet" href="/plugins/Web2PrintBlackbit/static/css/print/catalog/style_gartenkatalog-blackbit.css">

    <?php if($this->printermarks) { ?>
        <link rel="stylesheet" type="text/css" href="/static/css/style_windhager-catalogue/printermarks.css" media="print" />
    <?}?>

        <!--     <link rel="stylesheet" type="text/css" href="/plugins/Windhager/static/css/print/catalog/frontend.css" /> -->
        <script type="text/javascript" src="/static/js/windhager-catalogue/libs/jquery-3.2.1.min.js"></script>
        <script type="text/javascript" src="/static/js/windhager-catalogue/libs/awesomizr.js"></script>
        <script type="text/javascript" src="/static/js/windhager-catalogue/script.js"></script>
</head>
<body>
<div id="bohrungen">
    <div class="bohrung"></div>
    <div class="bohrung"></div>
    <div class="bohrung"></div>
    <div class="bohrung"></div>
</div>
<?=$this->layout()->content?>
<?=$this->inlineScript()?>
</body>
</html>