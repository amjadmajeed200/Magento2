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

define(
    [
        'jquery',
        'uiComponent',
        'Magento_Checkout/js/model/quote'
    ],
    function ($, Component, quote) {
        'use strict';

        var mixin = {
            getShippingMethodTitle: function () {
                var shippingMethod = quote.shippingMethod();

                if (shippingMethod['method_code'] === 'mpstorepickup') {
                    var locationId = 0;

                    /** edit shipping information by location selected */
                    $.ajax({
                        url: mpPickupData.locationSessionUrl,
                        type: 'post',
                        data: {locationId: locationId, type: 'get'},
                        dataType: 'json',
                        showLoader: true,
                        success: function (res) {
                            locationId = res.locationId;
                            var location = mpPickupData.locationsData[parseInt(locationId)],
                                address = location.name + '<br/>' +
                                    location.street + '<br/>' +
                                    location.region + ', ' + location.city + ' ' + location.postcode + '<br/>' +
                                    location.countryId + '<br/>' +
                                    '<a href="tel:' + location.telephone + '">' + location.telephone + '</a>';
                            $('.ship-to .shipping-information-content').html(address);
                        }
                    });
                }

                return shippingMethod ? shippingMethod['carrier_title'] + ' - ' + shippingMethod['method_title'] : '';
            },
        };

        return function (target) {
            return target.extend(mixin);
        };
    }
);
