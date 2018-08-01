/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) 2009-2016 pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

pimcore.registerNS("pimcore.object.classes.layout.extendedPanel");
pimcore.object.classes.layout.extendedPanel = Class.create(pimcore.object.classes.layout.layout, {
    type: "extendedPanel",

    initialize: function (treeNode, initData) {
        this.type = "extendedPanel";

        this.initData(initData);

        this.treeNode = treeNode;
    },



    getTypeName: function () {
        return t("extendedPanel");
    },

    getIconClass: function () {
        return "pimcore_icon_panel";
    },

    getLayout: function ($super) {
        $super();

        var layouts = Ext.create('Ext.data.Store', {
            fields: ['abbr', 'name'],
            data : [
                {"abbr":"", "name":"Default"},
                {"abbr":"fit", "name":"Fit"}
            ]
        });

        var roles = [];
        for(var i= 0; i < windhagerPlugin.config.roles.length; i++){
            roles.push([windhagerPlugin.config.roles[i].id,windhagerPlugin.config.roles[i].name]);
        }

        var items = [
            {
                xtype: "combo",
                fieldLabel: t("layout"),
                name: "layout",
                value: this.datax.layout,
                store: layouts,
                triggerAction: 'all',
                editable: false,
                displayField: 'name',
                valueField: 'abbr',
            },{
                xtype: "numberfield",
                name: "labelWidth",
                fieldLabel: t("label_width"),
                value: this.datax.labelWidth
            },
            {
                xtype: "combo",
                name: 'role',
                store: roles,
                editable: false,
                triggerAction: 'all',
                mode: "local",
                value : this.datax.role,
                fieldLabel: t("label_role")
            },
            {
                xtype: "checkbox",
                fieldLabel: t("mandatory"),
                name: "mandatory",
                checked: this.datax.mandatory
            }
        ];


        this.layout.add({
            xtype: "form",
            bodyStyle: "padding: 10px;",
            style: "margin: 10px 0 10px 0",
            items: items
        });

        return this.layout;
    }

});