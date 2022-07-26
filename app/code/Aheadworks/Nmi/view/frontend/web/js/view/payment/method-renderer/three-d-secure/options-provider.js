define([
    'jquery',
    'underscore',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/checkout-data'
], function ($, _, quote, checkoutData) {
    'use strict';

    return {
        defaultOptions: null,
        totals: quote.getTotals(),
        quote: quote,
        checkoutData: checkoutData,

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
                currency: this.totals().quote_currency_code,
                amount: this.totals().grand_total,
                email: window.customerData.email || this.quote.guestEmail,
                phone: this.quote.billingAddress().telephone,
                city: this.quote.billingAddress().city,
                state: this.quote.billingAddress().regionCode,
                address1: this.quote.billingAddress().street[0],
                country: this.quote.billingAddress().countryId,
                firstName: this.quote.billingAddress().firstname,
                lastName: this.quote.billingAddress().lastname,
                postalCode: this.quote.billingAddress().postcode
            });
        },

        /**
         * Retrieve options for 3DSecure with customer vault
         */
        getVaultOptions: function () {
            return _.extend(this.defaultOptions, {
                currency: this.totals().quote_currency_code,
                amount: this.totals().grand_total,
                address1: this.quote.billingAddress().street[0],
            })
        },
    };
});
