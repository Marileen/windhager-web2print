<?php if($this->editmode){?>
Hier wird der Index ausgegeben
<? }else { ?>
<div id="glossary" style="page-break-before: always;">
    <h1>Index</h1>
</div>
<script>
    <?php $this->inlineScript()->captureStart(); ?>
        $(function(){
            var pages = {};

            $('h3').each(function (i,e) {
                var boxDesc = ro.layout.getBoxDescriptions(this)[0];

                // Liest den Seitenindex aus
                var pIndex = boxDesc.pageIndex;

                pages[$(this).html().substring(0, 1).toUpperCase()] = typeof pages[$(this).html().substring(0, 1).toUpperCase()] != 'undefined' ? pages[$(this).html().substring(0, 1).toUpperCase()] : [];

                pages[$(this).html().substring(0, 1).toUpperCase()].push({title: $(this).html(), page: parseInt(pIndex+1)});
            });

            function ksort(obj){
                var keys = Object.keys(obj).sort()
                    , sortedObj = {};

                for(var i in keys) {
                    sortedObj[keys[i]] = obj[keys[i]];
                }

                return sortedObj;
            }

            pages = ksort(pages);

            $.each(pages, function(i,items){
                //items.sort();

                $('#glossary').append('<p><strong>' + i + '</strong></p>');
                $.each(items, function(j, item) {
                    $('#glossary').append('<div><span>' + item.title + '</span> <i>' + item.page + '</i></div>');
                });
                $('#glossary').append('<br>');

            });
        });
    <?php $this->inlineScript()->captureEnd(); ?>

</script>
<?php } ?>