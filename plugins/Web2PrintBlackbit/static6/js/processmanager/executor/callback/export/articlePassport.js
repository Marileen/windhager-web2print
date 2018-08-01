pimcore.registerNS("pimcore.plugin.windhager.processmanager.executor.callback.articlePassport");
pimcore.plugin.windhager.processmanager.executor.callback.articlePassport = Class.create(pimcore.plugin.windhager.processmanager.executor.callback.abstractCallback, {
    name: "articlePassport",
    callbackWindowType: 'tab',
    callbackWindowKeepOpen : true,

    getFormItems: function () {
        var items = [];

        items.push(this.getLocaleSelection('locale', {mandatory: true}));
        items.push(this.getTextField('emailReceiver'));

        items.push(this.getTextField('email'));

        items.push(this.getFieldExportItems("products"), {mandatory: false});

        //items.push(this.getTextField('order-number'));
        items.push(this.getTextField('charge'));

        /* type */
        items.push(this.getSelectField('type', {
            mandatory: true,
            store: [
                ['offer', t('plugin_pm_type-offer')],
                ['data-pass', t('plugin_pm_type-data-pass')]
            ]
        }));

        items.push(this.getTextField('image-name-schema',{
            tooltip: t("plugin_pm_image-name-schema-tooltip"),
            mandatory: false
        }));

        return items;
    }
});