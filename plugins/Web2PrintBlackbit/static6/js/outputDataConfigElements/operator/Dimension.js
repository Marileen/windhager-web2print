/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @category   Pimcore
 * @package    EcommerceFramework
 * @copyright  Copyright (c) 2009-2016 pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */


pimcore.registerNS("pimcore.plugin.outputDataConfigToolkit.outputDataConfigElements.operator.Dimension");

pimcore.plugin.outputDataConfigToolkit.outputDataConfigElements.operator.Dimension = Class.create(pimcore.plugin.outputDataConfigToolkit.outputDataConfigElements.operator.Text, {
    type: "operator",
    class: "Dimension",
    iconCls: "pimcore_icon_operator_dimension",
    defaultText: "operator_dimension",

    fields : ['item_length','item_width','item_height'],

    getConfigTreeNode: function(configAttributes) {

        if(configAttributes) {
            var node = {
                draggable: true,
                iconCls: this.iconCls,
                text: configAttributes.data.label,
                configAttributes: configAttributes,
                isTarget: true,
                allowChildren: true,
                expanded: true,
                leaf: false,
                expandable: false
            };
        } else {

            //For building up operator list
            var configAttributes = { type: this.type, class: this.class};

            var node = {
                draggable: true,
                iconCls: this.iconCls,
                text: t(this.defaultText),
                configAttributes: configAttributes,
                isTarget: true,
                leaf: true
            };
        }
        return node;
    },


    getCopyNode: function(source) {
        var copy = source.createNode({
            iconCls: this.iconCls,
            text: source.data.text,
            isTarget: true,
            leaf: false,
            expandable: false,
            configAttributes: {
                label: source.data.text,
                type: this.type,
                class: this.class
            }
        });
        return copy;
    },


    getConfigDialog: function(node) {

        this.node = node;
        var items = [];
        items.push({
            xtype : 'textfield',
            fieldLabel: t('label'),
            name : 'label',
            width: 440,
            style : 'float:left;margin-right:20px;',
            value: this.node.data.configAttributes.label
        });

        items.push({
            xtype : 'textfield',
            fieldLabel: t('concatenator'),
            style : 'float:left',
            width : 445,
            name : 'concatenator',
            value: this.node.data.configAttributes.concatenator
        });


        var allItems = [];

        var fieldSet = {
            xtype:'fieldset',
            title: "Einstellungen",
            collapsible: false,
            autoHeight:true,
            defaultType: 'textfield',
            style: 'margin-right:10px;',
            items :items
        };
        allItems.push(fieldSet);


        for(var i = 0; i < this.fields.length; i++){
            var field = this.fields[i];

            var items = [];
            items.push({
                fieldLabel: "Anzeigen",
                xtype :'checkbox',
                name : 'showItem_' + field,
                length: 255,
                width: 200,
                checked: this.node.data.configAttributes['showItem_' + field]
            });

            items.push({
                fieldLabel: "Zieleinheit",
                xtype : 'combo',
                editable: false,
                name: 'targetUnit_' + field,
                value: this.node.data.configAttributes['targetUnit_' + field],
                width : 250,
                store: [
                    ["", t('unit_use_default')],
                    ["mm", "mm"],
                    ["cm", "cm"],
                    ["dm", "dm"],
                    ["m", "m"]
                ],
                mode: "local",
                triggerAction: "all"
            });

            items.push({
                fieldLabel: "Einheit anzeigen",
                xtype :'checkbox',
                name : 'showUnit_' + field,
                length: 255,
                width: 200,
                checked: this.node.data.configAttributes['showUnit_' + field]
            });

            var conc = this.node.data.configAttributes['concatenator_' + field];

            if(typeof conc == 'undefined'){
                conc = ' ';
            }

            items.push({
                xtype : 'textfield',
                fieldLabel: t('concatenator'),
                name : 'concatenator_' + field,
                width: 200,
                value: conc
            });


            var fieldSet = {
                xtype:'fieldset',
                title: t('dimension_general_settings_' + field),
                collapsible: false,
                width : 305,
                autoHeight:true,
                style : 'float:left;margin-right:10px',
                defaultType: 'textfield',
                items : items
            };

            allItems.push(fieldSet);
        }



        this.formPanel = new Ext.FormPanel({
            border: false,
            autoScroll: true,
            bodyStyle:'padding:0 10px 0 10px;',
            items: allItems
        });

        if(node.data.configAttributes.data){
            this.formPanel.getForm().setValues(node.data.configAttributes.data);
        }

        this.window = new Ext.Window({
            width: 990,
            height: 450,
            modal: true,
            title: t('dimension_operator_settings'),
            layout: "fit",
            items: [this.formPanel],
            buttons: [{
                text: t("apply"),
                iconCls: "pimcore_icon_apply",
                handler: function () {
                    this.commitData();
                }.bind(this)
            }]
        });

        this.window.show();
        return this.window;
    },

    commitData: function() {
        var data = this.formPanel.getForm().getValues();
        this.node.set('text', data.label);
        this.node.label = data.label;
        this.node.data.configAttributes.data = data;
        this.window.close();
    }
});