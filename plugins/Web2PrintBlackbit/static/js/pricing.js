/**
 * Created by Julian Raab on 29.11.2015.
 */


/**
 * WINDHAGER CONDITIONS
 */
pimcore.registerNS("pimcore.plugin.windhager.pricing.conditions");
pimcore.plugin.windhager.pricing.conditions = {
    /**
     * @param panel
     * @param data
     * @param getName
     * @returns Ext.form.FormPanel
     */
    conditionInternalTenant: function (panel, data, getName) {

        var niceName = t("plugin_windhager_pricing_config_condition_internalTenant");
        if (typeof getName != "undefined" && getName) {
            return niceName;
        }

        if (typeof data == "undefined") {
            data = {};
        }
        var myId = Ext.id();

        var item = new Ext.form.FormPanel({
            layout: "pimcoreform",
            id: myId,
            type: 'InternalTenant',
            forceLayout: true,
            style: "margin: 10px 0 0 0",
            bodyStyle: "padding: 10px 30px 10px 30px; min-height:40px;",
            tbar: this.getTopBar(niceName, myId, panel, data, "plugin_windhager_pricing_icon_conditionInternalTenant"),
            items: [
                new pimcore.plugin.OnlineShop.pricing.config.objects(data.portalConfigurations, {
                    classes: [
                        {classes: "PortalConfiguration"}
                    ],
                    name: "portalConfigurations",
                    title: "",
                    visibleFields: "path",
                    height: 200,
                    width: 500,
                    columns: [],

                    // ?
                    columnType: null,
                    datatype: "data",
                    fieldtype: "objects",

                    // ??
                    index: false,
                    invisible: false,
                    lazyLoading: false,
                    locked: false,
                    mandatory: false,
                    maxItems: "",
                    noteditable: false,
                    permissions: null,
                    phpdocType: "array",
                    queryColumnType: "text",
                    relationType: true,
                    style: "",
                    tooltip: "",
                    visibleGridView: false,
                    visibleSearch: false
                }).getLayoutEdit()
            ]
        });

        return item;
    }, /**
     * @param panel
     * @param data
     * @param getName
     * @returns Ext.form.FormPanel
     */
    conditionRebateInKind: function (panel, data, getName) {

        var niceName = t("plugin_windhager_pricing_config_condition_rebateInKind");
        if (typeof getName != "undefined" && getName) {
            return niceName;
        }

        if (typeof data == "undefined") {
            data = {};
        }
        var myId = Ext.id();

        var item = new Ext.form.FormPanel({
            layout: "pimcoreform",
            id: myId,
            type: 'RebateInKind',
            forceLayout: true,
            style: "margin: 10px 0 0 0",
            bodyStyle: "padding: 10px 30px 10px 30px; min-height:40px;",
            tbar: this.getTopBar(niceName, myId, panel, data, "plugin_windhager_pricing_icon_conditionRebateInKind"),
            items: [
                {
                    xtype: "numberfield",
                    fieldLabel: t("plugin_windhager_pricing_config_condition_rebateInKind_amount"),
                    name: "amount",
                    width: 200,
                    value: data.amount
                },
                {
                    xtype: "textfield",
                    fieldLabel: t("plugin_windhager_pricing_config_condition_rebateInKind_product"),
                    name: "product",
                    width: 400,
                    cls: "input_drop_target",
                    value: data.product,
                    listeners: {
                        "render": function (el) {
                            new Ext.dd.DropZone(el.getEl(), {
                                reference: this,
                                ddGroup: "element",
                                getTargetFromEvent: function(e) {
                                    return this.getEl();
                                }.bind(el),

                                onNodeOver : function(target, dd, e, data) {
                                    return Ext.dd.DropZone.prototype.dropAllowed;
                                },

                                onNodeDrop : function (target, dd, e, data) {
                                    if (data.node.attributes.type == "object") {
                                        this.setValue(data.node.attributes.path);
                                        return true;
                                    }
                                    return false;
                                }.bind(el)
                            });
                        }
                    }
                },
                //new pimcore.plugin.OnlineShop.pricing.config.objects(data.products, {
                //    classes: [
                //        {classes: "Product"}
                //    ],
                //    name: "products",
                //    title: "",
                //    visibleFields: "path",
                //    height: 200,
                //    width: 500,
                //    columns: [],
                //
                //    // ?
                //    columnType: null,
                //    datatype: "data",
                //    fieldtype: "objects",
                //
                //    // ??
                //    index: false,
                //    invisible: false,
                //    lazyLoading: false,
                //    locked: false,
                //    mandatory: false,
                //    maxItems: "1",
                //    noteditable: false,
                //    permissions: null,
                //    phpdocType: "array",
                //    queryColumnType: "text",
                //    relationType: true,
                //    style: "",
                //    tooltip: "",
                //    visibleGridView: false,
                //    visibleSearch: false
                //}).getLayoutEdit()
            ]
        });

        return item;
    }

};


/**
 * WINDHAGER ACTIONS
 */

pimcore.registerNS("pimcore.plugin.windhager.pricing.actions");

pimcore.plugin.windhager.pricing.actions = {};