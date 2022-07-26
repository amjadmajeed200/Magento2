define([
    'jquery',
    'underscore'
], function ($, _) {
    'use strict';

    return {
        config: {},
        collectJS: null,

        /**
         * Set configuration
         *
         * @param {Object} config
         */
        setConfig: function (config) {
            this.config = config;
        },

        /**
         * Setup Nmi SDK
         */
        setup: function () {
            var fields = {},
                config;

            this.collectJS = CollectJS;

            _.each(this.config.hostedFields, function (fieldConfig, fieldCode) {
                fields[fieldCode] = fieldConfig.config;
            }, this);

            config = _.extend(this.config.paymentConfig, {
                callback: this.config.onHandleToken,
                validationCallback: this.config.onValidation,
                paymentSelector: '#submit_order_top_button, .order-totals-actions .save',
                fields: fields,
                variant: 'inline'
            });

            this.collectJS.configure(config);
        },

        /**
         * Get payment name
         *
         * @returns {String}
         */
        getCode: function () {
            return this.config.code
        },

        /**
         * Check if sandbox mode
         *
         * @returns {String}
         */
        isSandboxMode: function () {
            return this.config.isSandboxMode;
        }
    };
});
