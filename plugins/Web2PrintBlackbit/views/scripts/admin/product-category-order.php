<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <link rel="stylesheet" href="/plugins/Windhager/static/vendor/bootstrap/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="/plugins/Windhager/static/css/product-category-order.css"/>
</head>

<body>
<div class="toolbar">
    <button type="button" class="btn btn-success" data-save><?= $this->ts("save") ?></button>
    <button type="button" class="btn btn-danger" data-clear><?= $this->ts("clear") ?></button>
</div>

<div class="container">
    <h2><?= $this->ts("plugin.windhager.order-products-by-dragging") ?>
        <small class="text-muted"><?= $this->ts("plugin.windhager.do-not-forget-to-save") ?></small>
    </h2>

    <table class="table table-bordered">
        <thead></thead>
        <tbody>
        <? if (!empty($this->products)) : ?>
            <? foreach ($this->products as $product) : ?>
                <tr class="order-entry" data-id="<?= $product->getId() ?>">
                    <td><?= $product->getId() ?></td>
                    <td><?= $product->getTitle_ecom_6() ? : $product->getTitle() ?></td>
                    <td><?= $product->getFullPath() ?></td>
                </tr>
            <? endforeach ?>
        <? else : ?>
            <tr>
                <td><?= $this->ts("plugin.windhager.nothing-to-sort") ?></td>
            </tr>
        <? endif ?>
        </tbody>
    </table>
</div>

<script src="/plugins/Windhager/static/vendor/jquery/jquery.1.11.3.min.js"></script>
<script src="/plugins/Windhager/static/vendor/jquery/jquery-sortable.js"></script>

<script>
    $(function () {
        var saving = false,
            dragging = false,
            stop = true,
            $window = $(window);

        function scroll(step) {
            var scrollTop = $window.scrollTop();

            $window.scrollTop(scrollTop + step);

            if(!stop) {
                setTimeout(function() {
                    scroll(step);
                }, 20);
            }
        }

        $(document).on("mousemove", function(event) {
            if(!dragging) return;

            stop = true;

            if(event.originalEvent.clientY < 100) {
                stop = false;
                scroll(-1);
            } else if(event.originalEvent.clientY > $window.height() - 150) {
                stop = false;
                scroll(1);
            }
        });

        $(".table").sortable({
            containerSelector: "table",
            itemPath: "> tbody",
            itemSelector: "tr",
            placeholder: "<tr class=\"placeholder\"><td><td></tr>",

            onDragStart: function() {
                dragging = true;
                stop = true;
            },

            onDrop: function() {
                dragging = false;
                stop = true;
            }
        });

        $("[data-clear]").on("click", function () {
            if (saving) {
                return false;
            }

            saving = true;

            var $button = $(this);
            $button.attr("disabled", true);

            $.ajax({
                url: "/plugin/Windhager/admin/clear-product-category-order",
                type: "post",
                dataType: "json",
                data: {
                    categoryId: "<?= $this->categoryId ?>"
                }
            }).always(function () {
                saving = false;
                $button.attr("disabled", false);
                window.location.reload();
            })
        });

        $("[data-save]").on("click", function () {
            if (saving) {
                return false;
            }

            saving = true;
            var $button = $(this);

            $button.attr("disabled", true);

            var order = [];

            $(".order-entry").each(function () {
                order.unshift($(this).data("id"));
            });

            $.ajax({
                url: "/plugin/Windhager/admin/save-product-category-order",
                type: "post",
                dataType: "json",
                data: {
                    categoryId: "<?= $this->categoryId ?>",
                    order: order
                }
            }).always(function () {
                saving = false;
                $button.attr("disabled", false);
            })
        });
    });
</script>

</body>
</html>