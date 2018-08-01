pimcore.registerNS("pimcore.plugin.windhager.processmanager.executor.callback.abstractCallback");
pimcore.plugin.windhager.processmanager.executor.callback.abstractCallback = Class.create(pimcore.plugin.processmanager.executor.callback.abstractCallback,{

    getFieldExportItems : function (fieldname) {
        var materialAddButton = {
            xtype: "button",
            iconCls: "pimcore_icon_add",
            handler: function () {
                var window = new pimcore.plugin.windhager.processmanager.materialSelectionWindow(fieldname);
                window.setCallbackClass(this);
                window.show();
            }.bind(this)
        };

        var config = {
            itemSelectorConfig: {type: ["object"], specific: {classes: ['Product']}},
            buttons : [materialAddButton],
            mandatory : true
        };
        return this.getItemSelector(fieldname, config);
    }
});