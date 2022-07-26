define([
    'jquery',
    'underscore'
], function ($, _) {
    'use strict';

    return {
        defaultOptions: null,

        /**
         * Set default options
         */
        setDefaultOptions: function (defaultOptions) {
            this.defaultOptions = defaultOptions;
        },

        /**
         * Retrieve options for 3DSecure
         */
        getOptions: function (isVault = false) {
            return isVault ? this.getVaultOptions() : this.getCollectOptions();
        },

        /**
         * Retrieve options for 3DSecure with collect js
         */
        getCollectOptions: function () {
            return _.extend(this.defaultOptions, {
                currency: $('.order-sidebar #currency_switcher').val(),
                amount: window.awNmiGrandTotal,
                email: $('[name="order[account][email]"]').val(),
                phone: $('#order-billing_address_telephone').val(),
                city: $('#order-billing_address_city').val(),
                address1: $('#order-billing_address_street0').val(),
                country: $('#order-billing_address_country_id').val(),
                firstName: $('#order-billing_address_firstname').val(),
                lastName: $('#order-billing_address_lastname').val(),
                postalCode: $('#order-billing_address_postcode').val()
            });
        },

        /**
         * Retrieve options for 3DSecure with customer vault
         */
        getVaultOptions: function () {
            return _.extend(this.defaultOptions, {
                currency: $('.order-sidebar #currency_switcher').val(),
                amount: window.awNmiGrandTotal,
                address1: $('#order-billing_address_street0').val(),
            });
        },
    };
});
