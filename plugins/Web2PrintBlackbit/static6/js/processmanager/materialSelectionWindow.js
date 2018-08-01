pimcore.registerNS("pimcore.plugin.windhager.processmanager.materialSelectionWindow");
pimcore.plugin.windhager.processmanager.materialSelectionWindow = Class.create({

    fieldname : '',

    initialize : function (fieldname) {
        this.fieldname = fieldname;
    },

    setCallbackClass : function (c) {
      this.callbackClass = c;
    },

    show: function () {

        if(!this.detailWindow) {
            this.detailWindow = new Ext.Window({
                width: '80%',
                height: 530,
                title: t('plugin_windhager_material_select_window_title'),
                closeAction:'close',
                plain: true,
                maximized: false,
                autoScroll: true,
                modal: true,
                buttons: [
                    {
                        text: t('plugin_windhager_material_add_materials'),
                        iconCls: "pimcore_icon_apply",
                        scale: "medium",
                        handler: function(){
                            var values = this.formPanel.getForm().getValues();

                            Ext.Ajax.request({
                                url: "/plugin/Windhager/admin/get-material-data",
                                params: values,
                                method : 'POST',
                                success: function (transport) {
                                    var res = Ext.decode(transport.responseText);
                                    if(res.success){
                                        for(var i = 0; i < res.data.length;i++){
                                            this.callbackClass['formElement' + this.fieldname].getStore().add(res.data[i]);
                                        }
                                        this.detailWindow.destroy();
                                        if(res.missing){
                                            Ext.Msg.alert(t('error'), res.missing);
                                        }
                                    }else{
                                        Ext.Msg.alert(t('error'), res.message);
                                    }
                                }.bind(this)
                            });
                        }.bind(this)
                    }
                ]
            });

            this.materialNumbers = {
                xtype: "textarea",
                fieldLabel: t('plugin_windhager_material_material_numbers'),
                name: "materialNumbers",
                value: '',
                width : "100%",
                hideLabel: true,
                height:  360
            };
            this.formPanel = new Ext.form.FormPanel({
                border: false,
                frame:false,
                bodyStyle: 'padding:10px',
                items: [
                    {
                        xtype: "displayfield",
                        hideLabel: true,
                        width: '100%',
                        value: t('plugin_windhager_material_description'),
                        cls: "windhager_material_info_note"
                    },
                    this.materialNumbers],
                labelWidth: 130,
                collapsible: false,
                autoScroll: true
            });

            this.detailWindow.add(this.formPanel);
        }
        this.detailWindow.show();
        return this.detailWindow;
    }

});