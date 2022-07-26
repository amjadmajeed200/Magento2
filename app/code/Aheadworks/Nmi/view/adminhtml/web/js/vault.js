define([
    'jquery',
    'uiComponent',
    'Magento_Ui/js/modal/alert',
    'Aheadworks_Nmi/js/view/payment/three-d-secure',
    'Aheadworks_Nmi/js/view/payment/three-d-secure/options-provider'
], function ($, Class, alert, threeDSecureAdapter, optionsProvider) {
    'use strict';

    return Class.extend({
        defaults: {
            $selector: null,
            selector: 'edit_form',
            $container: null,
            paramToken: 'payment_method_token',
            paramIsVault: 'is_vault'
        },

        /**
         * Set list of observable attributes
         *
         * @returns {exports.initObservable}
         */
        initObservable: function () {
            var self = this;

            this.$selector = $('#' + this.selector);
            this.$container =  $('#' + this.container);
            this.$selector.on(
                'setVaultNotActive.' + self.getCode(),
                function () {
                    self.$selector.off('submitOrder.' + self.getCode());
                }
            );
            this._super();

            this.initEventHandlers();

            return this;
        },

        /**
         * Get payment code
         *
         * @returns {String}
         */
        getCode: function () {
            return this.code;
        },

        /**
         * Init event handlers
         */
        initEventHandlers: function () {
            $(this.$container).find('[name="payment[token_switcher]"]')
                .on('click', this.selectPaymentMethod.bind(this));
        },

        /**
         * Select current payment token
         */
        selectPaymentMethod: function () {
            this.disableEventListeners();
            this.enableEventListeners();
        },

        /**
         * Enable form event listeners
         */
        enableEventListeners: function () {
            this.$selector.on('submitOrder.' + this.getCode(), this.submitOrder.bind(this));
        },

        /**
         * Disable form event listeners
         */
        disableEventListeners: function () {
            this.$selector.off('submitOrder');
        },

        /**
         * Pre submit for order
         *
         * @returns {Boolean}
         */
        submitOrder: function () {
            this.$selector.validate().form();
            this.$selector.trigger('afterValidate.beforeSubmit');
            $('body').trigger('processStop');

            // validate parent form
            if (this.$selector.validate().errorList.length) {
                return false;
            }
            this.setPaymentDetails();
            if (this.threeDSecureAvailabilityMapPerStore[window.order.storeId]) {
                optionsProvider.setDefaultOptions({customerVaultId: this.customerVaultId});
                var options = optionsProvider.getOptions(true);

                threeDSecureAdapter.placeOrder(this, options);
            } else {
                this.placeOrder();
            }
        },

        /**
         * Place order
         */
        placeOrder: function () {
            this.$selector.trigger('realOrder');
        },

        /**
         * Set payment details
         */
        setPaymentDetails: function () {
            this.createAdditionalParamField(this.paramToken);
            this.createAdditionalParamField(this.paramIsVault);

            this.$selector.find('[name="payment[public_hash]"]').val(this.publicHash);
            this.$container.find('#' + this.getPaymentParamSelector(this.paramToken)).val(this.publicHash);
            this.$container.find('#' + this.getPaymentParamSelector(this.paramIsVault)).val(1);
        },

        /**
         * Creates public hash selector
         *
         * {String} param
         */
        createAdditionalParamField: function (param) {
            var $input;

            if (this.$container.find('#' + this.getPaymentParamSelector(param)).size() === 0) {
                $input = $('<input>').attr(
                    {
                        type: 'hidden',
                        id: this.getPaymentParamSelector(param),
                        name: 'payment[' + param + ']'
                    }
                );

                $input.appendTo(this.$container);
                $input.prop('disabled', false);
            }
        },

        /**
         * Show alert message
         *
         * @param {String} message
         */
        error: function (message) {
            alert({
                content: message
            });
        },

        /**
         * Get selector name for payment param
         *
         * {String} param
         * @returns {String}
         */
        getPaymentParamSelector: function (param) {
            return this.getCode() + '_' + param;
        }
    });
});
