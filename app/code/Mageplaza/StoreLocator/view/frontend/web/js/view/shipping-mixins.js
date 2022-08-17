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
        'underscore',
        'Magento_Ui/js/form/form',
        'ko',
        'Magento_Customer/js/model/customer',
        'Magento_Customer/js/model/address-list',
        'Magento_Checkout/js/model/address-converter',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/action/create-shipping-address',
        'Magento_Checkout/js/action/select-shipping-address',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'Magento_Checkout/js/model/shipping-address/form-popup-state',
        'Magento_Checkout/js/model/shipping-service',
        'Magento_Checkout/js/action/select-shipping-method',
        'Magento_Checkout/js/model/shipping-rate-registry',
        'Magento_Checkout/js/action/set-shipping-information',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Ui/js/modal/modal',
        'Magento_Checkout/js/model/checkout-data-resolver',
        'Magento_Checkout/js/checkout-data',
        'uiRegistry',
        'mage/translate',
        'Magento_Checkout/js/model/shipping-rate-service'
    ],
    function (
        $,
        _,
        Component,
        ko,
        customer,
        addressList,
        addressConverter,
        quote,
        createShippingAddress,
        selectShippingAddress,
        shippingRatesValidator,
        formPopUpState,
        shippingService,
        selectShippingMethodAction,
        rateRegistry,
        setShippingInformationAction,
        stepNavigator,
        modal,
        checkoutDataResolver,
        checkoutData,
        registry,
        $t
    ) {
        'use strict';

        var mixin = {
            /**
             * Set shipping information handler
             */
            setShippingInformation: function () {
                var shippingAddress = quote.shippingAddress();

                if (quote.shippingMethod() && quote.shippingMethod()['method_code'] === 'mpstorepickup') {
                    var locationId = $('#mpstorepickup-loc-id-selected').val();

                    if (locationId) {
                        if (!customer.isLoggedIn()) {
                            var loginForm = 'form[data-role=email-with-possible-login]';
                            $(loginForm).validation();
                            var validate = Boolean($(loginForm + ' input[name=username]').valid());

                            if (validate) {
                                var location = mpPickupData.locationsData[parseInt(locationId)];

                                shippingAddress.firstname  = location.name;
                                shippingAddress.middlename = '';
                                shippingAddress.lastname   = ',';
                                shippingAddress.countryId  = location.countryId;
                                shippingAddress.regionId   = location.regionId;
                                shippingAddress.regionCode = location.regionCode;
                                shippingAddress.region     = location.region;
                                shippingAddress.street     = [
                                    location.street
                                ];
                                shippingAddress.telephone  = location.telephone;
                                shippingAddress.postcode   = location.postcode;
                                shippingAddress.city       = location.city;
                                shippingAddress.prefix     = '';
                                selectShippingAddress(shippingAddress);

                                setShippingInformationAction().done(
                                    function () {
                                        stepNavigator.next();
                                    }
                                );

                                return;
                            }
                        }
                    } else {
                        this.errorValidationMessage($t('Please select store.'));

                        return false;
                    }
                }

                this._super();
            }
        };

        return function (target) {
            return target.extend(mixin);
        };
    }
);
