<?php
/**
 * Created by PhpStorm.
 * User: Julian Raab
 * Date: 16.02.2017
 * Time: 16:22
 */
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?= $this->title ?: "Export" ?></title>

    <link rel="stylesheet" type="text/css" href="/plugins/Windhager/static6/css/print-style.css" media="all"/>
    <link rel="stylesheet" type="text/css" href="/plugins/Windhager/static6/css/print-edit.css" media="screen"/>


    <?= $this->headLink() ?>

    <?= $this->headScript() ?>

</head>

<body>
<? if (!$this->pdf){ ?>

<div class="canvas">
    <div class="page">
        <? } ?>

        <?= $this->layout()->content ?>

        <? if (!$this->pdf){ ?>
    </div>
</div>
<? } ?>

<?= $this->inlineScript() ?>

</body>

</html>
