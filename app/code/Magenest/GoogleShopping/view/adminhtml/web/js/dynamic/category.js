define([
    'jquery',
    'ko',
    'underscore',
    'uiComponent',
    'Magento_Ui/js/modal/alert',
    'collapsable',
    'jquery/ui'
], function ($, ko, _, Component, alert) {
    'use strict';
    $.Map = function () {
        var self = this;
        this.rows = null;
        this.category_id = null;
        this.parent_id = null;
        this.name = null;
        this.level = null;
        this.has_childs = null;

        this.category = ko.observable();
        this.map = ko.observable();
        this.placeholder = ko.observable();
        this.setValue = ko.observable();
        this.opened = ko.observable(false);
        this.visible = ko.observable(false);

        this.opened.subscribe(function () {
            // self.map.notifySubscribers();
            self.placeholder.notifySubscribers();

            _.each(self.rows(), function (row) {
                if (row.parent_id == self.category_id) {
                    row.visible(self.opened());
                }
            });
        });

        // placeholder
        this.map.subscribe(function () {
            _.each(self.rows(), function (row) {
                if (row.parent_id == self.category_id) {
                    // console.log(row);
                    row.placeholder(self.map());
                }
            });
        });
        this.placeholder.subscribe(function () {
            _.each(self.rows(), function (row) {
                if (row.parent_id == self.category_id) {
                    if (self.map()) {
                        row.placeholder(self.map());
                    } else {
                        row.placeholder(self.placeholder());
                    }
                }
            });
        });

        this.visible.subscribe(function () {
            // hide child categories
            if (!self.visible()) {
                _.each(self.rows(), function (row) {
                    if (row.parent_id == self.category_id) {
                        row.visible(false);
                    }
                });
            }
        });

        this.load = function (obj) {
            self.category_id = obj.category_id;
            self.name = obj.name;
            self.map(obj.map);
            self.level = obj.level;
            self.parent_id = obj.parent_id;
            self.has_childs = obj.has_childs;

            if (obj.level == 0) {
                self.visible(true);
            }

            this.onSuggestSelect = function(map, e, ui) {
                self.map(ui.item.path);
            };

            return self;
        };

        this.toggle = function (map) {
            map.opened(!map.opened());
        }
    };

    return Component.extend({
        defaults: {
            template: 'Magenest_GoogleShopping/category/mapping'
        },
        mappingResult: ko.observableArray([]),
        saveMappingUrl: '',

        initialize: function () {
            var self = this;
            this._super();

            self.rows = ko.observableArray([]);
            self.saveMappingUrl = self.saveMappingUrl;
            _.each(self.mapping, function (row) {
                var obj = new $.Map();
                obj.rows = self.rows;
                obj.load(row);

                self.rows.push(obj);
            });
        },
        saveMapping: function () {
            var self = this;
            self.mappingResult([]);
            $('.map').each(function () {
                var mapp =  $(this).find(".category_mapping").val(),
                    category = $(this).find(".category").val();
                if(mapp != "" && category != ""){
                    self.mappingResult.push({
                        mapp: mapp,
                        category: category,
                    });
                }
            });
            if (self.mappingResult().length) {
                $.ajax({
                    type: "POST",
                    url: self.saveMappingUrl,
                    data: {
                        content_template: self.mappingResult(),
                        form_key: FORM_KEY
                    },
                    beforeSend: function () {
                        $('body').trigger('processStart');
                    },
                    success: function (response) {
                        if (response) {
                            alert({
                                title: 'Mapping data have been saved'
                            });
                            $('body').trigger('processStop');
                        }

                    },
                    error: function (response) {
                        console.log(response);
                        $('body').trigger('processStop');
                    }
                });
            }
        }
    });

});
