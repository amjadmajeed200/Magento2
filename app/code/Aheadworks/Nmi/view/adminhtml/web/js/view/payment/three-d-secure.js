define([
    'jquery',
    'mage/translate'
], function ($, $t) {
    'use strict';

    return {
        threeDSecureInterface: null,

        /**
         * Place order with 3DSecure technology
         */
        placeOrder: function (adapter, options) {
            var checkoutPublicKey = adapter.checkoutPublicKeyAvailabilityMapPerStore[window.order.storeId],
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
         * Show alert message
         *
         */
        showErrorMessage: function (data) {
            alert($t(data.message));
            $('#threeDSMountPoint iframe').hide();
            $('body').trigger('processStop');
        },

        /**
         * Callback on successful validation
         */
        successfulThreeDSecureValidation: function(data, adapter) {
            this.set3DSecurePaymentDetails(data, adapter);
            adapter.placeOrder();
            $('#threeDSMountPoint iframe').hide();
            $('body').trigger('processStop');
        },

        /**
         * Set 3DSecure payment details
         */
        set3DSecurePaymentDetails: function (data, adapter) {
            var container = adapter.$container || $('#' + adapter.container);

            $.each(data, (index, data) => {
                if (data) {
                    adapter.createAdditionalParamField(index);
                    container.find('#' + adapter.getPaymentParamSelector(index)).val(data);
                }
            });
        }
    };
});
