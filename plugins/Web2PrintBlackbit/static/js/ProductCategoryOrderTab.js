var Windhager = pimcore.registerNS("pimcore.plugin.windhager.ProductCategoryOrderTab");

Windhager.orderTabConfig = {
    TAB_SLUG: "product_category_order_tab",
    TITLE: t("plugin.windhager.product-order"),
    ICON: "pimcore_icon_whatever",
    URL: "/plugin/Windhager/admin/product-category-order"
};

Windhager.ProductCategoryOrderTab = Class.create({
    initialize: function (object, type) {
        this.config = {
            id: Windhager.orderTabConfig.TAB_SLUG + object.id,
            object: object,
            type: type
        };

        this._panel = null;
    },

    getPanel: function () {
        if (this._panel) return this._panel;

        this.reloadButton = new Ext.Button({
            text: t("reload"),
            iconCls: "pimcore_icon_reload",
            handler: this.reload.bind(this)
        });

        this._panel = new Ext.Panel({
            title: Windhager.orderTabConfig.TITLE,
            iconCls: Windhager.orderTabConfig.ICON,
            border: false,
            layout: "fit",
            scrollable: true,
            html: '<iframe src="about:blank" frameborder="0" style="width: 100%" id="' + this.config.id + '"></iframe>',
            tbar: [this.reloadButton]
        });

        this._panel.on("resize", this.onResize.bind(this));
        this._panel.on("afterrender", function() {
            this._panel.on("activate", function() {
                this.reload();
                this.resize(this._panel.getHeight());
            }.bind(this));
        }.bind(this));

        return this._panel;
    },

    onResize: function (el, width, height) {
        this.resize(height);
    },

    resize: function(height) {
        if (!this._iframe) return;

        this._iframe.setStyle({
            height: (height - 50) + "px"
        });
    },

    reload: function () {
        if (!this._iframe) {
            this._iframe = Ext.get(this.config.id);
        }

        var url = Windhager.orderTabConfig.URL;

        url += "?product-category=" + this.config.object.id;

        this._iframe.dom.src = url;
    }
});