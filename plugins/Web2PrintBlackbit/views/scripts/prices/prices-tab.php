<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Preise</title>

    <!-- Bootstrap -->
    <link href="/plugins/Windhager/static/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">


</head>
<body>
<div class="container">
    <h2><?=$this->product->getEsbTitle1()?> <small>EAN <?=$this->product->getEan()?></small></h2>

    <?php foreach($this->prices as $tenant => $prices) {?>
        <hr/>
        <h3><?=$tenant?></h3>

        <table class="table table-condensed table-striped table-bordered" style="width:200px;">
            <tr>
                <th width="100">Menge</th>
                <th width="100" class="text-right">Nettopreis</th>
            </tr>

            <?php foreach($prices as $price) {?>
                <tr>
                    <td><?=$price['amount']?></td>
                    <td class="text-right"><?=$price['retail_price']?></td>
                </tr>
            <?php }?>
        </table>


    <?php }?>
</div>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="/plugins/Windhager/static/vendor/jquery/jquery.1.11.3.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="/plugins/Windhager/static/vendor/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
