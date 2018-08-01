pimcore.registerNS("pimcore.plugin.windhager.processmanager.executor.callback.dataTransmission");
pimcore.plugin.windhager.processmanager.executor.callback.dataTransmission = Class.create(pimcore.plugin.windhager.processmanager.executor.callback.abstractCallback, {
    name: "dataTransmission",
    callbackWindowType: 'tab',
    callbackWindowKeepOpen : true,

    getFormItems: function () {
        var items = [];

        items.push(this.getTextField('emailReceiver'));

        items.push(this.getTextField('email'));

        items.push(this.getFieldExportItems("products"), {mandatory: true});

        items.push(this.getLocaleMultiSelection('languages', {
            mandatory: false,
            height: 150
        }));

        var validFields = ["textarea", "wysiwyg", "input","numeric"];

        var propertySelectorConfig = {
            storeUrl: '/plugin/Windhager/export_export/property-list?type=' + validFields.join("%2C") + '&',
            mandatory: false,
            //column config - optional - default to "name" column for display
            columns: [
                {
                    text: 'Property',
                    sortable: true,
                    dataIndex: "property"
                },
                {
                    text: t("plugin_pm_property_selector_propertyname"),
                    sortable: true,
                    dataIndex: "name",
                  /*  renderer : function (val) {
                       return t(val);
                    },*/
                    flex: 1
                }
            ]
        };

        items.push(this.getPropertySelector('select-product-text', propertySelectorConfig));

        propertySelectorConfig.storeUrl = '/plugin/Windhager/export_export/property-list?type=block';
        items.push(this.getPropertySelector('select-product-images', propertySelectorConfig));

        items.push(this.getSelectField("format", {
            store: [
                ['web', t('windhager_export_data-transmission_format_web')],
                ['high_resolution', t('windhager_export_data-transmission_format_hr')],
                ['preview', t('windhager_export_data-transmission_format_preview')],
            ]
        }));

        items.push(this.getTextField('image-name-schema',{
            tooltip: t("plugin_pm_image-name-schema-tooltip"),
            mandatory: false
        }));

        return items;
    },


    getLocaleMultiSelection: function (fieldName, config) {
        config = defaultValue(config, {});
        var websiteLanguages = pimcore.settings.websiteLanguages;

        // roles.unshift({
        //     'id' : 0,
        //     'name' : t('plugin_pm_role_admin')
        // });

        var store = [];
        for (var i = 0; i < websiteLanguages.length; i++) {
            var selectContent = pimcore.available_languages[websiteLanguages[i]] + " [" + websiteLanguages[i] + "]";
            store.push({id: websiteLanguages[i], name: selectContent});
        }

        var rolesStore = Ext.create('Ext.data.JsonStore', {
            fields: ["id", "name"],
            data: store
        });

        var value = this.getFieldValue(fieldName);
        if (value == '') {
            value = null;
        }

        return Ext.create('Ext.ux.form.MultiSelect', {
            name: fieldName,
            triggerAction: "all",
            editable: false,
            labelWidth: defaultValue(config.labelWidth, this.labelWidth),
            fieldLabel: this.getFieldLabel(fieldName, config),
            width: '100%',
            height: defaultValue(config.height, 100),
            store: rolesStore,
            displayField: "name",
            valueField: "id",
            afterLabelTextTpl: this.getTooltip(config.tooltip),
            value: value
        });
    }
});

