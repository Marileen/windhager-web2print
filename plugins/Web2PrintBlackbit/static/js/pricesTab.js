pimcore.registerNS("pimcore.plugin.windhager.PricesTab");

pimcore.plugin.windhager.PricesTab = Class.create({

    title: t('plugin_windhager_pricestab_tab'),
    iconCls: 'icon_prices_tab',
    src: '/plugin/Windhager/prices/prices-tab',
    id: null,

    initialize: function(object, type) {
        this.object = object;
        this.id = object.id;
        this.src = this.src + "?id=" + this.id;
        this.type = type;
    },

    getLayout: function () {
        if (this.panel == null) {

            this.reloadButton = new Ext.Button({
                text: t("reload"),
                iconCls: "pimcore_icon_reload",
                handler: this.reload.bind(this)
            });


            this.panel = new Ext.Panel({
                id: "plugin_windhager_pricestab_tab_" + this.id,
                title: this.title,
                iconCls: this.iconCls,
                border: false,
                layout: "fit",
                closable: false,
                bodyStyle: "-webkit-overflow-scrolling:touch;",
                html: '<iframe src="about:blank" frameborder="0" width="100%" id="plugin_windhager_pricestab_tab_frame_' + this.id + '"></iframe>',
                tbar: [this.reloadButton]
            });

            this.panel.on("resize", this.onLayoutResize.bind(this));
            var that = this;
            this.panel.on("afterrender", function(e){
                that.panel.on("activate", function(e){
                    that.reload();
                });
            });

        }
        return this.panel;

    },

    onLayoutResize: function (el, width, height, rWidth, rHeight) {
        this.setLayoutFrameDimensions(width, height);
    },

    setLayoutFrameDimensions: function (width, height) {
        Ext.get("plugin_windhager_pricestab_tab_frame_" + this.id).setStyle({
            height: (height - 50) + "px"
        });
    },

    reload: function () {
        try {
            var d = new Date();
            Ext.get("plugin_windhager_pricestab_tab_frame_" + this.id).dom.src = this.src;
        }
        catch (e) {
            console.log(e);
        }
    }

});