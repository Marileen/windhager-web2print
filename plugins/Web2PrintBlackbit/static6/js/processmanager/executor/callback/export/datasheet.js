pimcore.registerNS("pimcore.plugin.windhager.processmanager.executor.callback.datasheet");
pimcore.plugin.windhager.processmanager.executor.callback.datasheet = Class.create(pimcore.plugin.windhager.processmanager.executor.callback.abstractCallback, {
    name: "datasheet",
    callbackWindowType: 'tab',
    callbackWindowKeepOpen : true,

    getFormItems: function () {
        var items = [];

        items.push(this.getTextField('emailReceiver'));

        items.push(this.getTextField('email'));

        items.push(this.getTextField('name-schema',{
            tooltip: t("plugin_pm_image-name-schema-tooltip"),
            mandatory: false
        }));

        items.push(this.getFieldExportItems("products"), {mandatory: true});

        /* custom fields */


        var self = this;
        var checkEnableFields = function (e) {
            var checkFields = self.formPanel.query("[name='region'], [name='year']");
            var targetFields = self.formPanel.query("[name='mandant'], [name='includeFields']");

            var year = new Date().getYear();

            var enableFields = true;
            checkFields.forEach(function (field) {
                if (!field.value) {
                    enableFields = false;
                    return;
                }
                if (field.name == "year") {
                    year = field.value;
                }
            });

            if (enableFields) {
                targetFields.forEach(function (field) {
                    field.setDisabled(false);
                });

                var storeUrl = mandantStore.proxy.url.split("?")[0];
                mandantStore.proxy.url = storeUrl + "?year=" + year;
                mandantStore.load();

            }

        };

        /* region */
        items.push(this.getSelectField('region', {
            mandatory: false,
            labelWidth: 150,
            store: [
                ['1', t('plugin_pm_region_AT')],
                ['2', t('plugin_pm_region_DE')],
                ['3', t('plugin_pm_region_CH')],
                ['4', t('plugin_pm_region_FR')],
                ['5', t('plugin_pm_region_CEE')]
            ],
            onChange : checkEnableFields
        }));

        /* year */

        items.push(this.getSelectField('year', {
            mandatory: false,
            store: getYearsStore(),
            labelWidth: 150,
            onChange : checkEnableFields
        }));

        /* mandant */
        var mandantStore = new Ext.data.Store({
            proxy: {
                url: '/plugin/Windhager/export_export/get-mandant-store',
                type: 'ajax',
                reader: {
                    type: 'json',
                    root: "data"
                }
            }
        });

        var mandant = this.getSelectField("mandant", {
            store: mandantStore,
            labelWidth: 150,
            disabled: true,
            displayField : "n",
            valueField :  "v",
            onChange : function (newVal, oldVal) {
                if (!newVal) {
                    return;
                }

                self.formPanel.query("[name='pricelist']").forEach(function (field) {
                    field.setDisabled(false);
                });

                var storeUrl = pricelistStore.proxy.url.split("?")[0];
                pricelistStore.proxy.url = storeUrl + "?mandant=" + newVal;
                pricelistStore.load();
            }
        });
        items.push(mandant);

        /* pricelist */
        var pricelistStore = new Ext.data.Store({
            proxy: {
                url: '/plugin/Windhager/export_export/get-pricelist-store',
                type: 'ajax',
                reader: {
                    type: 'json',
                    root: "data"
                }
            }
        });

        var priceList = this.getSelectField("pricelist", {
            store: pricelistStore,
            labelWidth: 150,
            disabled: true,
            displayField :  "n",
            valueField :  "v"
        });
        items.push(priceList);

        items.push(this.getIncludesSelection("includeFields", {labelWidth: 150, heigth: 150}, {disabled: true}));

        /* language */
        items.push(this.getLocaleSelection('language', {
            mandatory: false,
            labelWidth: 150
        }));

        return items;
    },

    getIncludesSelection: function (fieldName, config, assignConfig) {
        config = defaultValue(config, {});

        var incStore = Ext.create('Ext.data.Store', {
            fields: ['value', 'name'],
            data: [
                {value: "EK", name: t('EK')},
                {value: "UVP", name: t('UVP')},
                {value: "KUArtNr", name: t('KUArtNr')}
            ]
        });

        var value = this.getFieldValue(fieldName);
        if (value == '') {
            value = null;
        }

        return Ext.create('Ext.ux.form.MultiSelect', Object.assign({
            name: fieldName,
            triggerAction: "all",
            editable: false,
            labelWidth: defaultValue(config.labelWidth, this.labelWidth),
            fieldLabel: this.getFieldLabel(fieldName, config),
            width: '100%',
            height: defaultValue(config.height, 100),
            store: incStore,
            displayField: "name",
            valueField: "value",
            afterLabelTextTpl: this.getTooltip(config.tooltip),
            value: value
        }, assignConfig));
    },

});


function getYearsStore() {
    var date = new Date,
        years = [],
        year = date.getFullYear();

    for (var i = year; i > year - 5; i--) {
        years.push([i, i]);
    }
    return years;
}