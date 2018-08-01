pimcore.registerNS("pimcore.plugin.windhager.processmanager.executor.callback.speed4Trade");
pimcore.plugin.windhager.processmanager.executor.callback.speed4Trade = Class.create(pimcore.plugin.windhager.processmanager.executor.callback.abstractCallback, {
    name: "speed4Trade",
    callbackWindowType: 'tab',
    callbackWindowKeepOpen : true,

    getFormItems: function () {
        var items = [];
        items.push(this.getCheckbox('exportCategories'));
        items.push(this.getSelectField('exportArticles',{store: [
            ['all', 'Komplettexport'],
            ['modified', 'Nur geänderte Artikel']
        ],
        width : 400
        }));

        return items;
    }
});

