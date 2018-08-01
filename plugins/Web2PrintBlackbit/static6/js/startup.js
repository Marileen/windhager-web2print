pimcore.registerNS("pimcore.plugin.windhager");

pimcore.plugin.windhager = Class.create(pimcore.plugin.admin, {

    config : {},

    getClassName: function() {
        return "pimcore.plugin.windhager";
    },

    initialize: function() {
        pimcore.plugin.broker.registerPlugin(this);
    },

    pimcoreReady: function (params,broker){
        this.extendEcommerceActions();

        new Ext.KeyMap(document, {
            key:"m",
            fn:this.openByMatnr,
            ctrl:true,
            alt:false,
            shift:false,
            stopEvent:true
        });

        Ext.Ajax.request({
            url: "/plugin/Windhager/admin/config",
            success: function(response){
                this.config = Ext.decode(response.responseText);
            }.bind(this)
        });

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


        var toolbar = pimcore.globalmanager.get("layout_toolbar");

        var menuItems = toolbar.windhager;
        if (!menuItems) {
            menuItems = new Ext.menu.Menu({
                shadow: false,
                cls: "pimcore_navigation_flyout"
            });

            toolbar.windhager = menuItems;
        }

        var insertPoint = Ext.get("pimcore_menu_settings");
        if(!insertPoint) {
            var dom = Ext.dom.Query.select('#pimcore_navigation ul li:last');
            insertPoint = Ext.get(dom[0]);
        }

        var pricingPanelId = "plugin_onlineshop_pricing_config";
        var item = {
            text: t("plugin_windhager_search_article"),
            iconCls: "pimcore_icon_search",
            handler: function () {
                pimcore.helpers.itemselector(false, function (selection) {
                    pimcore.helpers.openElement(selection.id, selection.type, selection.subtype);
                }, {type: ["object"] , specific : {classes : ['Product']}}, {moveToTab: true});
            }
        };
        // add to menu
        menuItems.add(item);

        var item = {
            text: t("plugin_windhager_article_states"),
            iconCls: "plugin_windhager_icon_approval",
            handler: function () {
                pimcore.helpers.openGenericIframeWindow('windhager_completeness','/plugin/Windhager/Backoffice_Completeness/list','plugin_windhager_icon_approval',t('plugin_windhager_article_states'));
                var panel = pimcore.globalmanager.get('windhager_completeness').panel;
                panel.on('activate',function(){
                    window.completenessObject.refreshGrid();
                });
            }
        };
        menuItems.add(item);

        var item = {
            text: t("plugin_windhager_todo_list"),
            iconCls: "plugin_windhager_icon_todo_list",
            handler: function () {
                pimcore.helpers.openGenericIframeWindow('windhager_todo','/plugin/Windhager/Backoffice_Todo/list','plugin_windhager_icon_todo_list',t('plugin_windhager_todo_list'));
                var panel = pimcore.globalmanager.get('windhager_todo').panel;
            }
        };
        menuItems.add(item);


        var item = {
            text: t("plugin_windhager_webfrontend"),
            iconCls: "plugin_windhager_icon_webfrontend",
            handler: function () {
                pimcore.helpers.openGenericIframeWindow('plugin_windhager_webfrontend','/webfrontend','plugin_windhager_icon_webfrontend',t('plugin_windhager_webfrontend'));
            }
        };
        menuItems.add(item);

        if (menuItems.items.length > 0) {
            this.navEl = Ext.get(
                insertPoint.insertHtml(
                    "afterEnd",
                    '<li id="pimcore_menu_windhager" class="pimcore_menu_item" data-menu-tooltip="Windhager">' + t('plugin_windhager_mainmenu') + '</li>'
                )
            );

            this.navEl.on("mousedown", toolbar.showSubMenu.bind(menuItems));
            pimcore.helpers.initMenuTooltips();
        }

        this.enableWindhagerPriceSystem();
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
        var actions = pimcore.plugin.windhager.pricing.actions;
        for (var action in actions) {
            if (actions.hasOwnProperty(action)) {
                pimcore.plugin.OnlineShop.pricing.actions[action] = pimcore.plugin.windhager.pricing.actions[action];
            }
        }
    },

    extendEcommerceActions: function() {
        pimcore.plugin.OnlineShop.pricing.actions.actionBadge = function(panel, data, getName) {
            var niceName = t("plugin_windhager_pricing_config_action_badge");

            if(typeof getName != "undefined" && getName) {
                return niceName;
            }

            data = data || {};

            var iconCls = "plugin_windhager_pricing_icon_badge";

            var id = Ext.id();

            var item = Ext.create("Ext.form.FormPanel", {
                id: id,
                type: "Badge",
                forceLayout: true,
                style: "margin: 10px 0 0 0",
                bodyStyle: "padding: 10px 30px 10px 30px; min-height: 40px",
                tbar: this.getTopBar(niceName, id, panel, data, iconCls),
                items: [{
                    xtype: "textfield",
                    fieldLabel: t("plugin_windhager_pricing_action_badge"),
                    name: "badge",
                    width: 500,
                    cls: "input_drop_target",
                    value: data.badge,
                    listeners: {
                        "render": function(el) {
                            new Ext.dd.DropZone(el.getEl(), {
                                reference: this,
                                ddGroup: "element",
                                getTargetFromEvent: function() {
                                    return this.getEl();
                                }.bind(el),

                                onNodeOver: function() {
                                    return Ext.dd.DropZone.prototype.dropAllowed;
                                },

                                onNodeDrop: function(target, dd, e, data) {
                                    var record = data.records[0];
                                    data = record.data;

                                    if(data.type == "object") {
                                        this.setValue(data.path);
                                        return true;
                                    }

                                    return false;
                                }.bind(el)
                            });
                        }
                    }
                }]
            });

            return item;
        }
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
        var that = this;

        if (type == "object" && object.data.general.o_className == "Product" && object.data.data.productType == "article") {
           var tab = new pimcore.plugin.windhager.PricesTab(object, type);

            object.tab.items.items[1].insert(1, tab.getLayout());
            pimcore.layout.refresh();
        }

        if (type == "object" && object.data.general.o_className == "Product") {
            var copyManger = new pimcore.plugin.windhager.copyProduct(object,type);
            var button = new Ext.Button({
                tooltip: t('open_parent'),
                iconCls: "plugin_windhager_open_parent",
                scale: "medium",
                handler: function(){
                    pimcore.helpers.openObject(object.data.general.o_parentId, "object", {layoutId : object.data.currentLayoutId});
                }.bind(this,object)
            });
            var items = object.tab.items.items[0].items.items;
            object.tab.items.items[0].insert(items.length-5, button);

            // print button
            var printButtonDropdown = this.getPrintButtonDropdown(object);
            if(printButtonDropdown){
                object.tab.items.items[0].add("-");
                object.tab.items.items[0].add(printButtonDropdown);
            }

            pimcore.layout.refresh();
        }

        if(type == "object" && object.data.general.o_className == "ProductCategory") {
            var productOrderTab = new pimcore.plugin.windhager.ProductCategoryOrderTab(object, type);

            object.tab.items.items[1].insert(productOrderTab.getPanel());
            pimcore.layout.refresh();
        }
    },

    getPrintButtonDropdown:function(object){
        var that = this;

        if(object.data.data.productType == "article" || object.data.data.productType == "product") {

            var datasheetMenu = [];
            /* ldl options */
            var ldlMenu = [
                {
                    text: t('plugin.windhager.print_ldl_vke'),
                    iconCls: "pimcore_icon_page",
                    handler: function () {
                        this.printProduct(object, "ldl", {"type": "vke"})
                    }.bind(this)
                },
                {
                    text: t('plugin.windhager.print_ldl_vpe'),
                    iconCls: "pimcore_icon_page",
                    handler: function () {
                        this.printProduct(object, "ldl", {"type": "vpe"})
                    }.bind(this)
                }
            ];

            /* datasheet options */
            Ext.Array.each(pimcore.settings.websiteLanguages, function (language) {
                var menuItem = {
                    text: language,
                    handler: function () {
                        that.printProduct(object, "datasheet", {"language": language})
                    },
                    iconCls: "pimcore_icon_clone"
                };
                datasheetMenu.push(menuItem);
            });

            /* print ldl and datasheets */
            return {
                xtype: "splitbutton",
                tooltip: t('print_tooltip'),
                iconCls: "pimcore_icon_print",
                scale: "medium",
                menu: [

                    {
                        text: t('plugin.windhager.print_articlepassport'),
                        iconCls: "pimcore_icon_structuredTable",
                        handler: function () {
                            this.printProduct(object, "articlepass")
                        }.bind(this)

                    },
                    {
                        text: t('plugin.windhager.print_ldl'),
                        iconCls: "pimcore_icon_page",
                        menu: ldlMenu
                    },
                    {
                        text: t('plugin.windhager.print_datasheet'),
                        iconCls: "pimcore_icon_text",
                        menu: datasheetMenu
                    }
                ]
            };
        }

    },

    _serializeToQueryString : function(obj) {
        var str = [];
        for(var p in obj)
            if (obj.hasOwnProperty(p)) {
                str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
            }
        return str.join("&");
    },

    printProduct: function(product, mode, params){
        var url = "";
        var queryAddition = "&"+this._serializeToQueryString(params);

        switch(mode){
            case "articlepass": {
                url = "/plugin/Windhager/export_print/artikelpass?id=" + product.id + "&pdf=1"+queryAddition;
            break;
            }
            case "ldl": {
                url = "/plugin/Windhager/export_print/ldl?id=" + product.id + "&pdf=1"+queryAddition;
            break;
            }
            case "datasheet":{
                url = "/plugin/Windhager/export_print/datasheet?id=" + product.id + "&pdf=1"+queryAddition;
            }

        }

        var win = window.open(url , "_blank");
        win.focus();
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

    openByMatnr : function(){
        Ext.MessageBox.prompt("Open object by materialnumber", "Materialnumber",function(btn,text){
            if(btn == 'ok'){
                Ext.Ajax.request({
                    url: "/plugin/Windhager/Admin/get-object-by-matnr",
                    method: "post",
                    params: {
                        matnr: text
                    },
                    success: function(response){
                        var res = Ext.decode(response.responseText);
                        if(res.success){
                            pimcore.helpers.openElement(res.objectId, 'object');
                        }else{
                            pimcore.helpers.showNotification(t("error"), res.message, "error");
                        }
                    }
                });
            }
        });
    },

    prepareClassLayoutContextMenu : function (types) {
       // types.panel.push('extendedPanel');
      //  types.root.push('extendedPanel');
        types.tabpanel.push('extendedPanel');
        types.root.push('extendedPanel');
        types.extendedPanel = types.panel;
        return types;
    }
});

var windhagerPlugin = new pimcore.plugin.windhager();

