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
        'Magento_Ui/js/form/form',
        'ko',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/quote',
        'Magento_Ui/js/modal/modal',
        'uiRegistry',
        'Magento_Checkout/js/model/payment/additional-validators'
    ],
    function (
        $,
        Component,
        ko,
        customer,
        quote,
        modal,
        registry,
        additionalValidators
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Mageplaza_StoreLocator/pickup'
            },

            initialize: function () {
                this._super();
                var self = this;

                /** validate store pickup */
                additionalValidators.registerValidator(this);

                quote.shippingMethod.subscribe(function () {
                    self.processSelectShippingMethod(quote.shippingMethod());
                });
            },

            /**
             * check validate store pickup
             * @returns {boolean}
             */
            validate: function () {
                var notice = $('.notice-select-store'),
                    locationId = $('#mpstorepickup-loc-id-selected').val();

                if (locationId ||
                    locationId !== '' ||
                    (quote.shippingMethod() !== null && quote.shippingMethod()['method_code'] !== 'mpstorepickup') ||
                    quote.isVirtual())
                {
                    notice.hide();
                    return true;
                }

                notice.show();
                return false;
            },

            /**
             * process after render shipping method list template
             */
            afterRenderTemplate: function () {
                var shippingMethod = quote.shippingMethod();
                this.processSelectShippingMethod(shippingMethod);
            },

            /**
             * process when choose shipping method
             * @param shippingMethod
             */
            processSelectShippingMethod: function (shippingMethod) {
                this.mpCheckShippingAddressFields(shippingMethod);

                var shippingEl = $('#shipping'),
                    addressFormEl = $('#co-shipping-form'),
                    shippingMethodEl = $('#checkout-shipping-method-load'),
                    pickupEl = $('#mpstorelocator-store-pickup');

                /** Show pickup content when selected mageplaza store pickup method **/
                if (shippingMethod && shippingMethod.method_code === 'mpstorepickup') {
                    if (customer.isLoggedIn()) {
                        shippingEl.hide();
                        $("<style>#shipping {display:none !important}</style>").appendTo(document.documentElement);
                    } else {
                        addressFormEl.hide();
                        $("<style>form.form-shipping-address {display:none}</style>").appendTo(document.documentElement);
                    }
                    if (shippingMethodEl.length) {
                        pickupEl.show();
                        pickupEl.appendTo(shippingMethodEl);
                    }
                } else {
                    shippingEl.attr('style','display: block !important');
                    addressFormEl.show();
                    pickupEl.hide();
                }
            },

            /**
             * Check enable/disable shipping address fields
             * @param shippingMethod
             */
            mpCheckShippingAddressFields: function(shippingMethod) {
                if (shippingMethod) {
                    registry.async('checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset')(function (fieldset) {
                        $.each(fieldset.elems(),function (key,elem) {
                            if(elem.hasOwnProperty('componentType') !== false && elem.componentType === 'group'){
                                $.each(elem.elems(),function (childKey,childElem) {
                                    childElem.disabled(shippingMethod.method_code === 'mpstorepickup');
                                })
                            }else{
                                if(elem.hasOwnProperty('disabled') === true){
                                    elem.disabled(shippingMethod.method_code === 'mpstorepickup');
                                }
                            }
                        })
                    });
                }
            },
        });
    }
);
