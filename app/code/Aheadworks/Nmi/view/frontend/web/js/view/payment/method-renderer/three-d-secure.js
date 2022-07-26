define([
    'jquery',
    'Magento_Ui/js/model/messageList'
], function ($, globalMessageList) {
    'use strict';

    return {
        threeDSecureInterface: null,

        /**
         * Place order with 3DSecure technology
         */
        placeOrder: function (adapter, options) {
            var checkoutPublicKey = adapter.checkoutPublicKey || window.checkoutConfig.payment.aw_nmi.checkoutPublicKey,
                gateway = Gateway.create(checkoutPublicKey),
                threeDS = gateway.get3DSecure();

            $('body').trigger('processStart');

            if (this.threeDSecureInterface) {
                this.threeDSecureInterface.unmount('#threeDSMountPoint');
            }

            this.threeDSecureInterface = threeDS.createUI(options);
            this.threeDSecureInterface.start('#threeDSMountPoint');
            this.bindEvents(gateway, adapter);
        },

        /**
         * Event Binding
         */
        bindEvents: function (gateway, adapter) {
            this.threeDSecureInterface.on('challenge', function() {
                $('#threeDSMountPoint iframe').show();
            });

            this.threeDSecureInterface.on('complete', (data) => this.successfulThreeDSecureValidation(data, adapter));
            this.threeDSecureInterface.on('failure', this.showErrorMessage);
            this.threeDSecureInterface.on('error', this.showErrorMessage);
            gateway.on('error', this.showErrorMessage);
        },

        /**
         * Show error message after checking the card
         */
        showErrorMessage: function (data) {
            globalMessageList.addErrorMessage({
                message: data.message
            });
            $('#threeDSMountPoint iframe').hide();
            $('body').trigger('processStop');
        },

        /**
         * Callback on successful validation
         */
        successfulThreeDSecureValidation: function(data, adapter) {
            adapter.additionalData = _.extend(data, adapter.additionalData);
            adapter.placeOrder('parent');
            $('#threeDSMountPoint iframe').hide();
            $('body').trigger('processStop');
        }
    };
});
