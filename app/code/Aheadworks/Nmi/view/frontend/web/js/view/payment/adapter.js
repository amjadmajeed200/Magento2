define([
    'jquery',
    'underscore',
    'Magento_Ui/js/model/messageList'
], function ($, _, globalMessageList) {
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
                paymentSelector: this.config.paymentSelector,
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
            return 'aw_nmi';
        },

        /**
         * Check if sandbox mode
         *
         * @returns {String|*}
         */
        isSandboxMode: function () {
            return window.checkoutConfig.payment[this.getCode()].isSandboxMode;
        },

        /**
         * Show error message
         *
         * @param {String} errorMessage
         */
        showError: function (errorMessage) {
            globalMessageList.addErrorMessage({
                message: errorMessage
            });
        }
    };
});
