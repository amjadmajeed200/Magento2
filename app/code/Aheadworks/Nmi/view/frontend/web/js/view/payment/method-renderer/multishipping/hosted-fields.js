/**
 * Since M2.3.3 use 'Magento_Checkout/js/action/set-payment-information-extended'
 */
define([
    'jquery',
    'Aheadworks_Nmi/js/view/payment/method-renderer/hosted-fields',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/action/set-payment-information'
], function (
    $,
    Component,
    fullScreenLoader,
    setPaymentInformation
) {
    'use strict';

    return Component.extend({
        defaults: {
            paymentConfig: {
                customCss: {
                    'border-color': '#ffffff',
                    'border-style': 'solid',
                    'height': '2rem'
                },
            },
            paymentSelector: '#payment-continue',
        },

        /**
         * @inheritdoc
         */
        placeOrder: function (key) {
            if (this.isActive()) {
                fullScreenLoader.startLoader();
                if (key) {
                    return this.setPaymentInformation();
                }
            }

            return false;
        },

        /**
         * Set payment information
         */
        setPaymentInformation: function () {
            if (this.isValid()) {
                fullScreenLoader.startLoader();
                $.when(
                    setPaymentInformation(
                        this.messageContainer,
                        this.getData()
                    )
                ).done(this.done.bind(this))
                    .fail(this.fail.bind(this));
            }
        },

        /**
         * On fail handler
         *
         * @returns {exports}
         */
        fail: function () {
            fullScreenLoader.stopLoader();

            return this;
        },

        /**
         * On success handler
         *
         * @returns {exports}
         */
        done: function () {
            fullScreenLoader.stopLoader();
            $('#multishipping-billing-form').submit();

            return this;
        }
    });
});
