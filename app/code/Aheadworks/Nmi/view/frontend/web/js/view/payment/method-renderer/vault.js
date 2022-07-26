define([
    'jquery',
    'Magento_Vault/js/view/payment/method-renderer/vault',
    'Aheadworks_Nmi/js/view/payment/adapter',
    'Aheadworks_Nmi/js/view/payment/method-renderer/three-d-secure',
    'Aheadworks_Nmi/js/view/payment/method-renderer/three-d-secure/options-provider',
    'Magento_Checkout/js/model/full-screen-loader'
], function ($, VaultComponent, Nmi, threeDSecureAdapter, optionsProvider, fullScreenLoader) {
    'use strict';

    return VaultComponent.extend({
        defaults: {
            template: 'Magento_Vault/payment/form',
            modules: {
                hostedFields: '${ $.parentName }.aw_nmi'
            }
        },

        /**
         * Get last 4 digits of card
         * @returns {String}
         */
        getMaskedCard: function () {
            return this.details.lastCcNumber;
        },

        /**
         * Get expiration date
         * @returns {String}
         */
        getExpirationDate: function () {
            return this.details.expirationDate;
        },

        /**
         * Get card type
         *
         * @returns {String}
         */
        getCardType: function () {
            return this.details.typeCode;
        },

        /**
         * Place order
         */
        placeOrder: function () {
            var self = this;
            optionsProvider.setDefaultOptions({customerVaultId: self.customer_vault_id});

            self.hostedFields(function (formComponent) {
                formComponent.paymentSelector = '.' + self.getId();
                formComponent.initNmi();
                formComponent.setPaymentMethodToken(self.publicHash);
                formComponent.additionalData['is_vault'] = 1;
                formComponent.additionalData['public_hash'] = self.publicHash;
                formComponent.code = self.code;
                formComponent.messageContainer = self.messageContainer;
                if (window.checkoutConfig.payment.aw_nmi.isThreeDSecureEnabled) {
                    var options = optionsProvider.getOptions(formComponent.additionalData['is_vault']);
                    threeDSecureAdapter.placeOrder(formComponent, options);
                } else {
                    formComponent.placeOrder('parent');
                }
            });
        }
    });
});
