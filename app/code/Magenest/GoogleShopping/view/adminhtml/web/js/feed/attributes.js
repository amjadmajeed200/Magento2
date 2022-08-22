define([
    "jquery",
    "ko",
    "uiComponent",
    'underscore',
    'mageUtils',
    'Magento_Ui/js/modal/alert'
], function ($, ko, Component, _, utils,alert) {
    "use strict";
    function Group(label, value, children) {
        var self = this;
        self.label = ko.observable(label);
        self.value = ko.observable(value);
        self.childrens = ko.observableArray([]);
        var arr = {};
        var i = 0;
        for (var skey in children) {
            if (children.hasOwnProperty(skey)) {
                var child = children[skey],
                    label = child.label + " (" + child.feed_name + ")";
                var option = new Option(label, child.name, child.feed_name);
                self.childrens.push(option);
                i++;
            }
        }
        return self.childrens;
    }
    function Option(label, name, feed_name) {
        var self = this;
        self.label = ko.observable(label);
        self.value = ko.observable(name);
        self.feed_name = ko.observable(feed_name);
    }
    return Component.extend({
        defaults: {
            template: "Magenest_GoogleShopping/view/feed/template-attributes",
        },
        magentoFields: ko.observableArray([]),
        googleshoppingAttribute: ko.observableArray([]),
        mappingResult: ko.observableArray([]),
        selectedField: ko.observable(),
        mappedField: ko.observableArray([]),
        templateName: '',
        saveMappingUrl: '',
        templateListingUrl: '',

        /**
         * @inheritdoc
         */
        initialize: function (config) {
            var self = this;
            this._super;
            this.initConfig(config);
            self.saveMappingUrl = config.save_mapping_url;
            self.templateListingUrl = config.template_listing_url;
            self.templateName = config.template_name;
            this.getOptionValue(config);
            return this;
        },
        /**
         * Calls 'initObservable' of parent
         *
         * @returns {Object} Chainable.
         */
        initObservable: function () {
            this._super();
            return this;
        },
        /**
         *
         * @param config
         */
        getOptionValue: function (config)
        {
            var self = this,
                observableTemplateFields = ko.observableArray([]),
                children = ko.observableArray([]);
            self.googleshoppingAttribute([]);
            self.magentoFields([]);
            self.mappedField([]);
            self.mappingResult([]);
            self.selectedField();
            observableTemplateFields.push({label: '--Choose Fields--', value: "", children: false});
            var googleshoppingAttribute = config.googleshopping_attribute;
            for (var skey in googleshoppingAttribute) {
                if (googleshoppingAttribute.hasOwnProperty(skey)) {
                    var attribuitesObj = googleshoppingAttribute[skey];
                    var group = new Group(attribuitesObj.label,skey,attribuitesObj.attributes);
                    observableTemplateFields.push({label: attribuitesObj.label, value: skey, children: group});
                }
            }
            var mappedField = config.mapped_fields;
            var magentoFields = config.magento_fields;
            for (var mkey in magentoFields) {
                if (magentoFields.hasOwnProperty(mkey)) {
                    var field = "",
                        stt = 1;
                    for (var i in mappedField) {
                        if (mkey == mappedField[i]['magento_attribute']) {
                            field = mappedField[i]['google_attribute'];
                            stt = mappedField[i]['status'];
                            break;
                        }
                    }
                    self.magentoFields.push({
                        key: magentoFields[mkey],
                        value: mkey,
                        googleshoppingAttribute: observableTemplateFields,
                        selectedField: field,
                        status: stt
                    });
                }
            }
        },

        saveMapping: function () {
            var self = this;
            var saveMappingUrl = self.saveMappingUrl;
            $('.admin__table-secondary > tbody > tr').each(function () {
                var cells = $(this).find("td");
                var select = $(this).find('#select_google_product_attribute');
                var status = $(this).find('#select_status');
                if (select.val()) {
                    self.mappingResult.push({
                        magento_attribute: cells.val(),
                        google_attribute: select.val(),
                        status: status.val()
                    });
                }
            });
            if (self.mappingResult().length) {
                var name = $("#template_name").val(),
                    templateId = $("#template_id").val();
                $.ajax({
                    type: "POST",
                    url: saveMappingUrl,
                    data: {
                        id: templateId,
                        template_mapping: self.mappingResult(),
                        template_name: name,
                        form_key: FORM_KEY
                    },
                    beforeSend: function () {
                        $('body').trigger('processStart');
                    },
                    success: function (response) {
                        if (response) {
                            window.location.href =  self.templateListingUrl;
                            $('body').trigger('processStop');
                        }
                    },
                    error: function (response) {
                        console.log(response);
                        $('body').trigger('processStop');
                    }
                });
            }else{
                alert({
                    title: 'No field mapping has been selected.'
                });
                $('body').trigger('processStop');
            }
        }
    });
});
