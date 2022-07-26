define([
    'jquery',
    'underscore',
    'Magento_Vault/js/view/payment/vault-enabler',
    'Magento_Payment/js/view/payment/cc-form'
], function ($, _, VaultEnabler, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Aheadworks_Nmi/payment/form',
            paymentMethodToken: null,
            cardData: {},
            code: 'aw_nmi',
            imports: {
                onActiveChange: 'active'
            }
        },

        /**
         * Additional payment data
         *
         * {Object}
         */
        additionalData: {},

        /**
         * {inheritdoc}
         */
        initialize: function () {
            this._super();
            this.vaultEnabler = new VaultEnabler();
            this.vaultEnabler.setPaymentCode(this.getVaultCode());

            return this;
        },

        /**
         * {@inheritdoc}
         */
        initObservable: function () {
            this._super()
                .observe([
                    'active'
                ]);

            return this;
        },

        /**
         * Retrieve payment code
         *
         * @returns {string}
         */
        getCode: function() {
            return this.code;
        },

        /**
         * Returns state of place order button
         *
         * @returns {Boolean}
         */
        isButtonActive: function () {
            return this.isActive() && this.isPlaceOrderActionAllowed();
        },

        /**
         * Check if payment is active
         *
         * @returns {Boolean}
         */
        isActive: function () {
            var active = this.getCode() === this.isChecked();

            this.active(active);

            return active;
        },

        /**
         * Triggers when payment method change
         */
        onActiveChange: function () {
            if (!this.active() || !this.renderer) {
                return;
            }
            this.initNmi();
        },

        /**
         * On toolbar render
         */
        onToolbarRender: function () {
            this.renderer = true;
            this.onActiveChange();
        },

        /**
         * Init payment method nmi
         */
        initNmi: function () {

        },

        /**
         * Set payment nonce
         *
         * @param {String} paymentMethodToken
         */
        setPaymentMethodToken: function (paymentMethodToken) {
            this.paymentMethodToken = paymentMethodToken;
        },

        /**
         * Set payment nonce
         *
         * @param {String} card
         */
        setCardData: function (card) {
            this.cardData = card;
        },

        /**
         * Get data
         *
         * @returns {Object}
         */
        getData: function () {
            var data = {
                'method': this.getCode(),
                'additional_data': {
                    'payment_method_token': this.paymentMethodToken,
                    'card_type': this.cardData.type,
                    'card_number': this.cardData.number,
                    'card_exp': this.cardData.exp
                }
            };
            this.vaultEnabler.visitAdditionalData(data);

            data['additional_data'] = _.extend(data['additional_data'], this.additionalData);

            return data;
        },

        /**
         * Get full selector name
         *
         * @param {String} field
         * @returns {String}
         */
        getSelector: function (field) {
            return '#' + this.getCode() + '_' + field;
        },

        /**
         * Show error message
         *
         * @param {String} errorMessage
         */
        showError: function (errorMessage) {
            this.messageContainer.addErrorMessage({
                message: errorMessage
            });
        },

        /**
         * Check if vault is enabled
         *
         * @returns {boolean}
         */
        isVaultEnabled: function () {
            return this.vaultEnabler.isVaultEnabled();
        },

        /**
         * Retrieve vault code
         *
         * @returns {String}
         */
        getVaultCode: function () {
            return window.checkoutConfig.payment[this.getCode()].ccVaultCode;
        },

        /**
         * Is multi shipping
         *
         * @returns {String}
         */
        isMultiShipping: function () {
            return !!Number(window.checkoutConfig.quoteData.is_multi_shipping);
        }
    });
});
