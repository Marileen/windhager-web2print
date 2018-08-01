pimcore.registerNS("pimcore.plugin.windhager");

pimcore.plugin.windhager = Class.create(pimcore.plugin.admin, {
    getClassName: function() {
        return "pimcore.plugin.windhager";
    },

    initialize: function() {
        pimcore.plugin.broker.registerPlugin(this);
    },
 
    pimcoreReady: function (params,broker){
        var user = pimcore.globalmanager.get("user");
        if(user.isAllowed("assets")) {

            var fileMenu = pimcore.globalmanager.get("layout_toolbar").fileMenu;

            var idx = 0;

            for (i = 0; i < fileMenu.items.items.length; i++) {
                var item = fileMenu.items.items[i];
                var text = item.text;
                if (text == t("open_object_by_id")) {
                    idx = i + 1;
                    break;
                }
            }

            fileMenu.insert(idx, '-');

            fileMenu.insert(idx,
                {
                    text: t("plugin_windhager_open_asset_by_celumid"),
                    iconCls: "pimcore_icon_open_asset_by_id",
                    handler: this.openByCelumId.bind(this)
                }
            );
        }
        this.enableWindhagerPriceSystem();
    },

    openByCelumId: function() {
        Ext.MessageBox.prompt(t('plugin_windhager_open_asset_by_celumid'), t('plugin_windhager_please_enter_the_celumid'),
            function (button, value, object) {
                if(button == "ok" && !Ext.isEmpty(value)) {
                    Ext.Ajax.request({
                        url: "/plugin/Windhager/admin/get-asset-id",
                        params: {
                            "celumId": value
                        },
                        success: function(response) {
                            var data = Ext.decode(response.responseText);
                            pimcore.helpers.openElement(data.id, 'asset');
                        },
                        failure: function() {
                            Ext.MessageBox.alert(t("error"), t("element_not_found"))
                        }
                    });
                }
            });
    },

    postOpenObject: function (object, type) {
        if (type == "object" && object.data.general.o_className == "Product" && object.data.data.productType == "article") {
            var tab = new pimcore.plugin.windhager.PricesTab(object, type);

            object.tab.items.items[1].insert(1, tab.getLayout());
            object.tab.items.items[1].doLayout();
            pimcore.layout.refresh();
        }

        if (type == "object" && object.data.general.o_className == "Product") {
            var button = new Ext.Button({
                tooltip: t('copy-images'),
                iconCls: "icon_copy_images",
                scale: "medium",
                handler: this.copyImages.bind(this, object,  "object")
            });

            var items = object.tab.items.items[0].items.items;


            object.tab.items.items[0].insert(items.length-5, button);


            pimcore.layout.refresh();
        }
    },

    copyImages: function(element, elementType, button) {

        if(confirm('Möchten Sie die Bilder wirklich von den Hauptbildern zu "Eigene Webshops" kopieren?')) {
            Ext.Ajax.request({
                url: "/plugin/Windhager/admin/copy-images",
                params: {
                    id: element.id
                },
                success: function (button, response) {
                   var res = Ext.decode(response.responseText);
                   if(res.success) {
                       var options = {};
                       options.layoutId = element.data.currentLayoutId;
                       window.setTimeout(function (id) {
                           pimcore.helpers.openObject(id, "object", options);
                       }.bind(window, element.id), 500);

                       pimcore.helpers.closeObject(element.id);
                   } else {
                       alert('Fehlgeschlagen');
                   }
                }.bind(this, button)
            });
        }

    },
    /**
     *  Enable custom pricing rules
     */
    enableWindhagerPriceSystem: function () {

        // enable conditions
        var conditions = pimcore.plugin.windhager.pricing.conditions;
        for (var condition in conditions) {
            if (conditions.hasOwnProperty(condition)) {
                pimcore.plugin.OnlineShop.pricing.conditions[condition] = pimcore.plugin.windhager.pricing.conditions[condition];
            }
        }

        // enable actions
        //var actions = pimcore.plugin.windhager.pricing.actions;
        //for (var action in actions) {
        //    if (actions.hasOwnProperty(action)) {
        //        pimcore.plugin.OnlineShop.pricing.actions[action] = pimcore.plugin.windhager.pricing.actions[action];
        //    }
        //}
    }
});

var windhagerPlugin = new pimcore.plugin.windhager();

