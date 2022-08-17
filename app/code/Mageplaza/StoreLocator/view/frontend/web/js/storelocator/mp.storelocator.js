/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_StoreLocator
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

define([
    'jquery',
    'Mageplaza_StoreLocator/js/lib/jquery.storelocator'
], function ($) {
    'use strict';

    $.widget('mageplaza.storelocator', {
        options: {
            dataConfig: {},
            mapStyles: {}
        },

        /**
         * @inheritDoc
         */
        _create: function () {
            this.initStoreLocator();
            this.loadDetailLocation();
            this._EventListener();
        },

        initStoreLocator: function () {
            var checkHttps     = true,
                isFilterRadius = false;

            if (window.location.protocol === 'http:') {
                checkHttps = false;
            }

            if (checkHttps && this.options.dataConfig.isFilterRadius) {
                isFilterRadius = true;
            }

            $('#bh-sl-map-container').storeLocator({
                'slideMap': false,
                'mapSettings': {
                    styles: this.options.mapStyles,
                    zoom: parseInt(this.options.dataConfig.zoom)
                },
                'markerImg': this.options.dataConfig.markerIcon,
                'markerDim': {height: 32, width: 25},
                'fullMapStart': true,
                'autoComplete': true,
                'inlineDirections': checkHttps,
                'dataLocation': this.options.dataConfig.dataLocations,
                'infowindowTemplatePath': this.options.dataConfig.infowindowTemplatePath,
                'listTemplatePath': this.options.dataConfig.listTemplatePath,
                'KMLinfowindowTemplatePath': this.options.dataConfig.KMLinfowindowTemplatePath,
                'KMLlistTemplatePath': this.options.dataConfig.KMLlistTemplatePath,
                'autoGeocode': isFilterRadius,
                'maxDistance': true,
                'defaultLoc': this.options.dataConfig.isDefaultStore,
                'defaultLat': this.options.dataConfig.defaultLat,
                'defaultLng': this.options.dataConfig.defaultLng,
                'pagination': this.options.dataConfig.pagination
            });
            this.detailLocationClick();
        },

        /** event click detail location on list locations */
        detailLocationClick: function () {
            var self          = this,
                pickupForm    = $('#mpstorepickup-pickup-form'),
                locationsData = self.options.dataConfig.locationsData;

            $.each(locationsData, function (id) {
                $('body').on('click', 'li.mpstorelocator-location-' + id + ' .link-detail', function () {
                    var el = $('li.mpstorelocator-location-' + id);

                    var locationId = el.attr('data-id'),
                        markerId   = el.attr('data-markerid'),
                        urlKey     = el.attr('data-url-key');
                    $('.mp-store-info').show();
                    $('.mp-detail-info-' + locationId).show();
                    $('.mp-storelocator-list-location').hide();
                    $('.loc-directions-' + locationId).attr('marker-id', markerId);

                    /** check not change url on pickup */
                    if (!pickupForm.length) {
                        window.history.pushState('', '', urlKey + self.options.dataConfig.urlSuffix);
                    }
                });
            });
        },

        /**
         * Show details location when load url_key
         */
        loadDetailLocation: function () {
            var i          = 0,
                locationId = this.options.dataConfig.locationIdDetail;

            if (locationId) {
                var showMarker = setInterval(function () {
                    var storeViewEl = $('.store-view-' + locationId);
                    i++;

                    if (i > 60) {
                        clearInterval(showMarker);
                    }

                    if (storeViewEl.length) {
                        clearInterval(showMarker);

                        storeViewEl.trigger('click');
                    }
                }, 1000);

                $('.mp-store-info').show();
                $('.mp-detail-info-' + locationId).show();
                $('.mp-storelocator-list-location').hide();
            }
        },

        /**
         * Event listener
         * @private
         */
        _EventListener: function () {
            var _this                  = this,
                pickupForm             = $('#mpstorepickup-pickup-form'),
                storeNameElement       = $('input[name="store_name"]'),
                streetNameElement      = $('input[name="street"]'),
                countryElement         = $('select[name="country"]'),
                stateElement           = $('input[name="state"]'),
                cityElement            = $('input[name="city"]'),
                postCodeElement        = $('input[name="post_code"]'),
                searchButtonElement    = $('button.search-by-area-btn'),
                resetButtonElement     = $('button.reset-search-by-area-btn'),
                addressSelectedElement = $('#address-selected'),
                iconSearch             = $('#mp-store-loc-search-by-area .mp-image-icon-marker');

            /** Event back button */
            $('.mp-back-results').on('click', function () {
                $('.mp-store-info').hide();
                $('.mp-detail-info').hide();
                $('.mp-storelocator-list-location').show();

                /** check not change url on pickup */
                if (!pickupForm.length) {
                    window.history.pushState('', '', _this.options.dataConfig.router + _this.options.dataConfig.urlSuffix);
                }
            });

            /**
             * Dropdown all time of location
             */
            $('.mp-detail-info-text i').each(function () {
                var el       = $(this),
                    openList = $('.mp-openday-list');
                el.on('click', function () {
                    if (el.hasClass('fa-angle-double-down')) {
                        openList.slideDown('slow');
                        el.removeClass('fa-angle-double-down');
                        el.addClass('fa-angle-double-up');
                    } else {
                        openList.slideUp(10);
                        openList.promise().done(function () {
                            el.removeClass('fa-angle-double-up');
                            el.addClass('fa-angle-double-down');
                        });
                    }
                });
            });

            /**
             * Dropdown phone2, tax info
             */
            $('.mp-detail-phone-text i').each(function () {
                var el        = $(this),
                    phoneList = $('.mp-phone-list');
                el.on('click', function () {
                    if (el.hasClass('fa-angle-double-down')) {
                        phoneList.slideDown('slow');
                        el.removeClass('fa-angle-double-down');
                        el.addClass('fa-angle-double-up');
                    } else {
                        phoneList.slideUp(10);
                        phoneList.promise().done(function () {
                            el.removeClass('fa-angle-double-up');
                            el.addClass('fa-angle-double-down');
                        });
                    }
                });
            });

            /**
             *  event click menu icon
             */
            $('.mp-menu-icon').on('click', function () {
                $('.mp-dialog-setting').toggle("slide");
            });

            /**
             *  event click close icon
             */
            $('.mp-btn-close').on('click', function () {
                $('.mp-dialog-setting').toggle("slide");
            });

            var $locIcon = $('#mp-location-icon');
            /** check https */
            if (window.location.protocol === 'http:') {
                $locIcon.hide();
                $('.loc-directions').hide();
                $('#bh-sl-submit').css({'float': 'right', 'margin-right': '15px'});
            } else {
                $locIcon.show();
                if (!$locIcon.length) {
                    $('#bh-sl-submit').css({'float': 'right', 'margin-right': '15px'});
                }

            }

            countryElement.change(function () {
                if ($(this).val() === '') {
                    $(this).addClass('empty');
                } else {
                    $(this).removeClass('empty');
                }
            });
            countryElement.change();

            if (!this.options.dataConfig.isFilter) {
                $('.mp-storelocator-list-location').css({'top': '3%', 'height': '99%'});
                $('.mp-store-info').css({'top': '3%', 'height': '95%'});
            }

            $('button.search-by-area-btn').click(function () {
                var storeName = storeNameElement.val(),
                    street    = streetNameElement.val(),
                    country   = countryElement.val(),
                    state     = stateElement.val(),
                    city      = cityElement.val(),
                    postCode  = postCodeElement.val(),
                    locations = $.extend({}, _this.options.dataConfig.locationsData);

                if (storeName || street || country || state || city || postCode) {
                    $('ul.list.mp-storelocator-list-location li').hide();

                    if (storeName) {
                        $.each(locations, function (id, value) {
                            if (value.name.toLowerCase().indexOf(storeNameElement.val().toLowerCase()) < 0) {
                                delete locations[id];
                            }
                        });
                    }

                    if (street) {
                        $.each(locations, function (id, value) {
                            if (value.street.toLowerCase().indexOf(streetNameElement.val().toLowerCase()) < 0) {
                                delete locations[id];
                            }
                        });
                    }

                    if (country) {
                        $.each(locations, function (id, value) {
                            if (value.countryId !== countryElement.val()) {
                                delete locations[id];
                            }
                        });
                    }

                    if (state) {
                        $.each(locations, function (id, value) {
                            if (value.region.toLowerCase().indexOf(stateElement.val().toLowerCase()) < 0) {
                                delete locations[id];
                            }
                        });
                    }

                    if (city) {
                        $.each(locations, function (id, value) {
                            if (value.city.toLowerCase().indexOf(cityElement.val().toLowerCase()) < 0) {
                                delete locations[id];
                            }
                        });
                    }

                    if (postCode) {
                        $.each(locations, function (id, value) {
                            if (value.postcode.toLowerCase().indexOf(postCodeElement.val().toLowerCase()) < 0) {
                                delete locations[id];
                            }
                        });
                    }

                    if (!$.isEmptyObject(locations)) {
                        $.each(locations, function (id) {
                            $('li.mpstorelocator-location-' + id).show();
                        });
                    } else {
                        $('.mp-no-location-search-by-area').show();
                    }

                    $('.mp-dialog-setting').toggle("slide");
                }
            });

            $('button.reset-search-by-area-btn').click(function () {
                storeNameElement.val('');
                streetNameElement.val('');
                countryElement.val('');
                stateElement.val('');
                cityElement.val('');
                postCodeElement.val('');
                countryElement.addClass('empty');
                $('ul.list.mp-storelocator-list-location li').show();
                $('.mp-dialog-setting').toggle("slide");
            });
        }
    });

    return $.mageplaza.storelocator;
});
