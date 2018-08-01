/**
 * Created by Julian Raab on 29.11.2015.
 */


/**
 * WINDHAGER CONDITIONS
 */
pimcore.registerNS("pimcore.plugin.windhager.copyProduct");
pimcore.plugin.windhager.copyProduct = Class.create({

    fieldConfig : {
        name : 'sourceItem'
    },

    initialize : function (object,type) {
        this.object = object;
        this.type = type;

        if(windhagerPlugin.config.productMergerConfig.actions){
            var button = new Ext.Button({
                tooltip: t('copy-data'),
                iconCls: "plugin_windhager_copy_icon",
                scale: "medium",
                handler: this.openWindow.bind(this)
            });
            var items = object.tab.items.items[0].items.items;
            object.tab.items.items[0].insert(items.length-5, button);
        }
    },

    openWindow : function(){
        this.window = new Ext.Window({
            width: 850,
            height: 600,
            title: t('plugin_windhager_prouct_copy_title'),
            closeAction:'close',
            plain: true,
            maximized: false,
            autoScroll: false,
            modal: false,
            buttons: [
                {
                    text: t('close'),
                    iconCls: "pimcore_icon_cancel",
                    scale: "medium",
                    handler: function(){
                        this.window.hide();
                        this.window.destroy();
                    }.bind(this)
                },
                {
                    text: t('apply'),
                    iconCls: "pimcore_icon_apply",
                    scale: "medium",
                    handler: function(){
                        var data = this.formPanel.getForm().getValues();
                        data.id = this.object.id;

                        var errors = [];
                        if(!data.sourceItem){
                            errors.push(t('plugin_windhager_error_source_item'));
                        }
                        if(!data.mergeActions){
                            errors.push(t('plugin_windhager_error_action'));
                        }

                        if(errors.length == 0){
                            Ext.Ajax.request({
                                url: "/plugin/Windhager/Admin/merge-product-data/",
                                method: "post",
                                params: data,
                                success: function (response) {
                                    var res = Ext.decode(response.responseText);
                                    if(res.success){
                                        pimcore.helpers.showNotification(t("success"), t('plugin_windhager_data_merged'), "success");
                                        this.object.reload(this.object.data.currentLayoutId);
                                        this.window.destroy();
                                    }else{
                                        pimcore.helpers.showNotification(t("error"), res.message, "error");
                                    }
                                }.bind(this)
                            });
                        }else{
                            Ext.MessageBox.alert('Error', errors.join("<br/>"));
                        }
                    }.bind(this)
                }
            ]
        });
        this.createPanel();

        this.window.show();
    },


    createPanel: function() {
        var items = [];

        this.sourceItem = new Ext.form.TextField({
            fieldLabel: t("plugin_windhager_source_item")  + ' <span style="color:#f00;">*</span>',
            name: "sourceItem",
            //value: this.object.data.general.fullpath,
            width: 730,
            readOnly: false,
            cls: "input_drop_target",
            enableKeyEvents: true,

            listeners: {
                "keydown" : function(el,newValue,oldValue){
                    if(newValue != oldValue){
                        this.itemData = { path : '',id : '',type : ''}; //resetting values if manually changed
                        this.setValue('');
                    }
                },
                "render": function (el) {
                    new Ext.dd.DropZone(el.getEl(), {
                        ddGroup: "element",

                        getTargetFromEvent: function(e) {
                            return this.getEl();
                        },

                        onNodeOver : function(target, dd, e, data) {
                            var record = data.records[0];
                            if(record.data.elementType == "object" && record.data.className == "Product"){
                                return Ext.dd.DropZone.prototype.dropAllowed;
                            }else{
                                return Ext.dd.DropZone.prototype.dropNotAllowed;
                            }
                        },

                        onNodeDrop : function (target, dd, e, data) {
                            var record = data.records[0];
                            if(record.data.elementType == "object" && record.data.className == "Product"){
                                this.itemData = { path : record.data.path,
                                    id : record.data.id,
                                    type : record.data.elementType
                                };
                                this.setValue(this.itemData.path);
                                return true;
                            }else{
                                return false;
                            }
                        }.bind(this)
                    });
                }
            }
        });

        var items = [];

        var self = this;

        this.composite = Ext.create('Ext.form.FieldContainer', {
            fieldLabel: this.fieldConfig.title,
            layout: 'hbox',
            items: [this.sourceItem,{
                xtype: "button",
                iconCls: "pimcore_icon_search",
                style: "margin-left: 5px",
                handler: function () {
                    pimcore.helpers.itemselector(false, function (selection) {
                        self.sourceItem.setValue(selection.fullpath);
                        /*console.log(selection.id);
                        console.log(selection.fullpath);*/
                       // pimcore.helpers.openElement(selection.id, selection.type, selection.subtype);
                    }, {type: ["object"] , specific : {classes : ['Product']}}, {moveToTab: true});
                }.bind(this)
            }
            ],
            componentCls: "object_field",
            border: false,
            style: {
                padding: 0
            },
            listeners: {
                afterrender: function() {
                    // this.requestNicePathData();
                }.bind(this)
            }
        });

        items.push(this.composite);

        var actionstore = [];
        var actions = [];

        for (var prop in windhagerPlugin.config.productMergerConfig.actions) {
            // skip loop if the property is from prototype
            if(!windhagerPlugin.config.productMergerConfig.actions.hasOwnProperty(prop)) continue;
            actionstore.push({
                value : prop , name : t('plugin_windhager_action_' + prop)
            });
            actions.push(prop);

        }


        this.actions = Ext.create('Ext.ux.form.MultiSelect', {
            name:"mergeActions",
            triggerAction:"all",
            editable:false,
            fieldLabel:t('plugin_windhager_actions') + ' <span style="color:#f00;">*</span>',
            width:380,
            minHeight: 50,
            style : 'float:left;margin-right:10px;',
            maxHeight: 150,
            store: {
                fields : ['value','name'],
                data : actionstore
            },
            mode : "local",
            displayField: "name",
            valueField: "value",
            value : ''
        });

        items.push(this.actions);

        var localestore = [];
        var languages = [];
        for (var i = 0; i < windhagerPlugin.config.productEditableLanguages.length; i++) {
            var v = windhagerPlugin.config.productEditableLanguages[i];
            localestore.push({
               value : v,name : pimcore.available_languages[v]
            });
            languages.push(v);
        }

        this.languages = Ext.create('Ext.ux.form.MultiSelect', {
            name:"languages",
            triggerAction:"all",
            editable:false,
            fieldLabel:t("plugin_windhager_copy_languages"),
            width:380,
            labelWidth: 60,
            style : 'float:left',
            minHeight: 50,
            maxHeight: 150,
            store: {
                fields : ['value','name'],
                data : localestore
            },
            mode : "local",
            displayField: "name",
            valueField: "value",
            value : languages.join(',')
        });


        items.push(this.languages);

        this.override = new Ext.form.Checkbox({
            xtype: "checkbox",
            labelSeparator: '',
            hideLabel: true,
            boxLabel: t('plugin_windhager_override_data'),
            style : 'margin-left:105px',
            name: 'override',
            checked: true
        });
        items.push(this.override);



        this.copyEmptyValues = new Ext.form.Checkbox({
            xtype: "checkbox",
            labelSeparator: '',
            hideLabel: true,
            boxLabel: t('plugin_windhager_copyEmptyValues_data'),
            style : 'margin-left:105px',
            name: 'copyEmptyValues',
            checked: false
        });
        items.push(this.copyEmptyValues);

        this.onlyText = new Ext.form.Checkbox({
            xtype: "checkbox",
            labelSeparator: '',
            hideLabel: true,
            boxLabel: t('plugin_windhager_copy_only_text'),
            style : 'margin-left:105px',
            name: 'onlyText',
            checked: false
        });
        items.push(this.onlyText);

        this.onlyImage = new Ext.form.Checkbox({
            xtype: "checkbox",
            labelSeparator: '',
            hideLabel: true,
            boxLabel: t('plugin_windhager_copy_only_image'),
            style : 'margin-left:105px',
            name: 'onlyImage',
            checked: false
        });
        items.push(this.onlyImage);

        this.formPanel = new Ext.form.FormPanel({
            border: false,
            frame:false,
            bodyStyle: 'padding:10px',
            items: items,
            labelWidth: 130,
            collapsible: false,
            autoScroll: true
        });

        this.window.add(this.formPanel);
    }
});

