pimcore.registerNS("pimcore.plugin.windhager.processmanager.executor.callback.exportProducts");
pimcore.plugin.windhager.processmanager.executor.callback.exportProducts = Class.create(pimcore.plugin.windhager.processmanager.executor.callback.abstractCallback, {
    name: "exportProducts",
    callbackWindowType: 'tab',

    getFormItems: function () {
        var items = [];

        items.push(this.getFieldExportItems("products"), {mandatory: true});

        items.push(this.getTextField('emailReceiver'));

        items.push(this.getTextField('email'));

        items.push(this.getTextField('order-number'));

        /* type */
        items.push(this.getSelectField('type', {
            mandatory: false,
            store: [
                ['offer', t('plugin_pm_type-offer')],
                ['data-pass', t('plugin_pm_type-data-pass')]
            ]
        }));

        items.push(this.getTextField('image-name-schema',{
            mandatory: false,
                tooltip: t("plugin_pm_image-name-schema-tooltip")
        }));

        items.push(this.getCheckbox('inc-images'),{
            mandatory: false,
            labelWidth: 180
        });

        items.push(this.getCheckbox('inc-dependencies', {
            mandatory: false,
            labelWidth: 180,
            tooltip: t("plugin_pm_inc-dependencies-tooltip")
        }));

        // items.push(this.getSelectField('format', {
        //     store : [
        //         ['format1',t('plugin_windhager_offer')],
        //         ['format2',t('plugin_windhager_transfer')]
        //     ]
        // }));

        // var config = {
        //     panelWidth : 700,
        //     mandatory : true,
        //     storeUrl : '/plugin/Windhager/admin/property-list'
        // };

        // items.push(this.getPropertySelector('languages',config));
        // items.push(this.getPropertySelector('textFields',config));
        // items.push(this.getPropertySelector('images',config));

        return items;
    }
});