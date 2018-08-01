<? if($this->editmode){?>
    <link rel="stylesheet" type="text/css" href="/plugins/Web2PrintBlackbit/static/css/print/catalog/editmode.css">
<?}?>

    <?= $this->areablock("content",[
        "allowed" => ["print_product_gartenkatalog","print_pagebreak_gartenkatalog","print_headline_gartenkatalog"],
    ])?>

