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
        'ko',
        'underscore',
        'Magento_Ui/js/form/form',
        'Magento_Customer/js/model/customer',
        'Magento_Customer/js/model/address-list',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/action/create-billing-address',
        'Magento_Checkout/js/action/select-billing-address',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/model/checkout-data-resolver',
        'Magento_Customer/js/customer-data',
        'Magento_Checkout/js/action/set-billing-address',
        'Magento_Ui/js/model/messageList',
        'mage/translate'
    ],
    function (
        $,
        ko,
        _,
        Component,
        customer,
        addressList,
        quote,
        createBillingAddress,
        selectBillingAddress,
        checkoutData,
        checkoutDataResolver,
        customerData,
        setBillingAddressAction,
        globalMessageList,
        $t
    ) {
        'use strict';

        var newAddressOption = {
                /**
                 * Get new address label
                 * @returns {String}
                 */
                getAddressInline: function () {
                    return $t('New Address');
                },
                customerAddressId: null
            },
            addressOptions = addressList().filter(function (address) {
                return address.getType() === 'customer-address';
            });
        addressOptions.push(newAddressOption);

        var mixin = {
            defaults: {
                template: 'Mageplaza_StoreLocator/billing-address'
            },

            /**
             * @return {exports.initObservable}
             */
            initObservable: function () {
                this._super()
                .observe({
                    selectedAddress: null,
                    isAddressDetailsVisible: quote.billingAddress() != null,
                    isAddressFormVisible: !customer.isLoggedIn() || addressOptions.length === 1,
                    isAddressSameAsShipping: true,
                    saveInAddressBook: 1
                });
                var self = this;
                if (quote.isVirtual()) {
                    this.isAddressSameAsShipping(false);
                }

                quote.billingAddress.subscribe(function (newAddress) {
                    if (quote.isVirtual()) {
                        this.isAddressSameAsShipping(false);
                    } else {
                        this.isAddressSameAsShipping(
                            newAddress != null &&
                            newAddress.getCacheKey() === quote.shippingAddress().getCacheKey()
                        );
                    }

                    if (newAddress != null && newAddress.saveInAddressBook !== undefined) {
                        this.saveInAddressBook(newAddress.saveInAddressBook);
                    } else {
                        this.saveInAddressBook(1);
                    }
                    this.isAddressDetailsVisible(true);

                    /** set show option select list billing address */
                    if (quote.shippingMethod() && quote.shippingMethod()['method_code'] === 'mpstorepickup') {
                        $('#mp-billing-address-title').show();
                        this.isAddressDetailsVisible(false);
                        this.isAddressSameAsShipping(false);
                    }
                }, this);

                /** compatible OSC **/
                quote.shippingMethod.subscribe(function () {
                    var billing = $('#billing'),
                        sameCheckbox = $('.billing-address-same-as-shipping-block');

                    if (quote.shippingMethod() && quote.shippingMethod()['method_code'] === 'mpstorepickup') {
                        billing.show();
                        sameCheckbox.hide();
                        self.isAddressSameAsShipping(false);
                    } else if (self.isAddressSameAsShipping()){
                        billing.hide();
                        sameCheckbox.show();
                    } else {
                        billing.show();
                        sameCheckbox.show();
                    }
                });

                return this;
            },

            canUseShippingAddress: ko.computed(function () {
                if (quote.shippingMethod() && quote.shippingMethod()['method_code'] === 'mpstorepickup') {
                    return false;
                }

                return !quote.isVirtual() && quote.shippingAddress() && quote.shippingAddress().canUseForBilling();
            }),

            /**
             * Cancel address edit action
             */
            cancelAddressEdit: function () {
                this.restoreBillingAddress();

                if (quote.billingAddress()) {
                    /** hide option select list billing address */
                    if (quote.shippingMethod() && quote.shippingMethod()['method_code'] === 'mpstorepickup') {
                        this.isAddressSameAsShipping(false);
                        if (!this.source.get('params.invalid')) {
                            this.isAddressDetailsVisible(true);
                        }
                    } else {
                        this.isAddressSameAsShipping(
                            quote.billingAddress() != null &&
                            quote.billingAddress().getCacheKey() === quote.shippingAddress().getCacheKey() &&
                            !quote.isVirtual()
                        );
                        this.isAddressDetailsVisible(true);
                    }
                }
            },

            /**
             * Trigger action to update shipping and billing addresses
             */
            updateAddresses: function () {
                /** hide option select list billing address */
                if (quote.shippingMethod() && quote.shippingMethod()['method_code'] === 'mpstorepickup') {
                    if (!this.source.get('params.invalid')) {
                        this.isAddressDetailsVisible(true);
                    }
                    this.isAddressSameAsShipping(false);
                }

                if (window.checkoutConfig.reloadOnBillingAddress ||
                    !window.checkoutConfig.displayBillingOnPaymentMethod
                ) {
                    setBillingAddressAction(globalMessageList);
                }
            }
        };

        return function (target) {
            return target.extend(mixin);
        };
    }
);
