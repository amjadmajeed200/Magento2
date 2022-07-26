define([
    'jquery',
    'uiComponent',
    'Aheadworks_Nmi/js/view/payment/adapter',
    'Aheadworks_Nmi/js/view/payment/handler',
    'Magento_Ui/js/modal/alert',
    'Magento_Ui/js/lib/view/utils/dom-observer',
    'mage/translate',
    'Aheadworks_Nmi/js/view/payment/three-d-secure',
    'Aheadworks_Nmi/js/view/payment/three-d-secure/options-provider'
], function ($, Class, nmi, handler, alert, domObserver, $t, threeDSecureAdapter, optionsProvider) {
    'use strict';

    return Class.extend({

        defaults: {
            $selector: null,
            selector: 'edit_form',
            container: 'payment_form_aw_nmi',
            code: 'aw_nmi',
            active: false,
            scriptLoaded: false,
            useCvv: true,
            paymentConfig: {
                invalidCss: {
                    color: '#B40E3E'
                },
                validCss: {
                    color: '#13AA73'
                },
                customCss: {
                    'border-color': '#ffffff',
                    'border-style': 'solid',
                    'height': '1.3rem'
                },
            },
            imports: {
                onActiveChange: 'active'
            }
        },

        /**
         * @inheritdoc
         */
        initConfig: function (config) {
            this._super();

            handler.setConfig(config);

            return this;
        },

        /**
         * Set list of observable attributes
         *
         * @returns {exports.initObservable}
         */
        initObservable: function () {
            var self = this;

            this.$selector = $('#' + self.selector);
            this._super()
                .observe([
                    'active',
                    'scriptLoaded'
                ]);

            // re-init payment method events
            self.$selector.off('changePaymentMethod.' + this.code)
                .on('changePaymentMethod.' + this.code, this.changePaymentMethod.bind(this));

            // listen block changes
            domObserver.get('#' + self.container, function () {
                if (self.scriptLoaded() && self.active()) {
                    self.resetPayment();
                    self.$selector.off('submit');
                    self.initNmi();
                }
            });

            return this;
        },

        /**
         * Enable/disable current payment method
         *
         * @param {Object} event
         * @param {String} method
         * @returns {exports.changePaymentMethod}
         */
        changePaymentMethod: function (event, method) {
            this.active(method === this.code);

            return this;
        },

        /**
         * Triggered when payment changed
         *
         * @param {Boolean} isActive
         */
        onActiveChange: function (isActive) {
            if (!isActive) {
                this.resetPayment();
                return;
            }

            this.disableEventListeners();
            window.order.addExcludedPaymentMethod(this.code);
            this.enableEventListeners();

            this.initNmi();
        },

        /**
         * Init payment method nmi
         */
        initNmi: function () {
            var config = {},
                self = this,
                intervalId = setInterval(function () {
                    // stop loader when frame will be loaded
                    if ($('#' + self.container + ' #CollectJSInlineccnumber').length) {
                        clearInterval(intervalId);
                        setTimeout(function () {
                            $('body').trigger('processStop');
                        }, 1000);
                    }
                }, 500);

            if (!nmi.collectJS) {
                $('body').trigger('processStart');
                config = {
                    hostedFields: handler.getHostedFields(),
                    onValidation: handler.onValidation.bind(handler),
                    onHandleToken: this.onHandleToken.bind(this),
                    paymentConfig: this.paymentConfig
                };

                nmi.setConfig(config);
                nmi.setup();
                self.scriptLoaded(true);
            }
        },

        /**
         * Enable form event listeners
         */
        enableEventListeners: function () {
            this.$selector.on('submitOrder.' + this.code, this.submitOrder.bind(this));
        },

        /**
         * Disable form event listeners
         */
        disableEventListeners: function () {
            this.$selector.off('submitOrder');
            this.$selector.off('submit');
        },

        /**
         * On handle token handler
         *
         * @param {object} data
         */
        onHandleToken: function(data) {
            if (data.initiatedBy instanceof Event) {
                this.setPaymentDetails(data);
                if (this.threeDSecureAvailabilityMapPerStore[window.order.storeId]) {
                    optionsProvider.setDefaultOptions({paymentToken: data.token});
                    var options = optionsProvider.getOptions(false);

                    threeDSecureAdapter.placeOrder(this, options);
                } else {
                    this.placeOrder();
                }
            } else {
                $('body').trigger('processStop');
                this.showError($t('Error creating token.'));
            }
        },

        /**
         * Show alert message
         *
         * @param {String} message
         */
        showError: function (message) {
            alert({
                content: message
            });
        },

        /**
         * Store payment details
         *
         * @param {Object} paymentData
         */
        setPaymentDetails: function (paymentData) {
            var $container = $('#' + this.container);

            $container.find('[name="payment[payment_method_token]"]').val(paymentData.token);
            $container.find('[name="payment[card_type]"]').val(paymentData.card.type);
            $container.find('[name="payment[card_number]"]').val(paymentData.card.number);
            $container.find('[name="payment[card_exp]"]').val(paymentData.card.exp);
            $container.find('[name="payment[is_vault]"]').val(0);
        },

        /**
         * Trigger order submit
         */
        submitOrder: function () {
            this.$selector.validate().form();
            this.$selector.trigger('afterValidate.beforeSubmit');
            if (!handler.validateFormFields()) {
                $('body').trigger('processStop');
                return false;
            }
        },

        /**
         * Place order
         */
        placeOrder: function () {
            // validate parent form
            if (this.$selector.validate().errorList.length) {
                $('body').trigger('processStop');
                return false;
            }
            $('#' + this.container).find('[type="submit"]').trigger('click');
        },

        /**
         * Reset payment data
         */
        resetPayment: function () {
            nmi.collectJS = null;
            handler._fields = null;
        },

        /**
         * Creates public hash selector
         *
         * {String} param
         */
        createAdditionalParamField: function (param) {
            var $input,
                container = $('#' + this.container);

            if (container.find('#' + this.getPaymentParamSelector(param)).size() === 0) {
                $input = $('<input>').attr(
                    {
                        type: 'hidden',
                        id: this.getPaymentParamSelector(param),
                        name: 'payment[' + param + ']'
                    }
                );

                $input.appendTo(container);
                $input.prop('disabled', false);
            }
        },

        /**
         * Get selector name for payment param
         *
         * {String} param
         * @returns {String}
         */
        getPaymentParamSelector: function (param) {
            return this.code + '_' + param;
        }
    });
});
