define([
    'jquery',
    'underscore',
    'Aheadworks_Nmi/js/view/payment/method-renderer/three-d-secure',
    'Aheadworks_Nmi/js/view/payment/method-renderer/three-d-secure/options-provider',
    'Aheadworks_Nmi/js/view/payment/method-renderer/cc-form',
    'Aheadworks_Nmi/js/view/payment/adapter',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/model/payment/additional-validators',
    'mage/translate'
], function ($, _, threeDSecureAdapter, optionsProvider, Component, nmi, fullScreenLoader, additionalValidators, $t) {
    'use strict';

    return Component.extend({
        defaults: {
            paymentConfig: {
                invalidCss: {
                    color: '#B40E3E'
                },
                validCss: {
                    color: '#13AA73'
                },
            },
            paymentSelector: '#aw-nmi-pay-button',
        },
        _fields: {},

        nmiOnHandleCallbackDeferred: null,

        /**
         * {@inheritdoc}
         */
        initNmi: function () {
            var config,
                self = this,
                intervalId = setInterval(function () {
                // stop loader when frame will be loaded
                if ($('#co-transparent-form-aw-nmi #CollectJSInlineccnumber').length) {
                    clearInterval(intervalId);
                    setTimeout(function () {
                        fullScreenLoader.stopLoader();
                    }, 1000);
                }
            }, 500);

            fullScreenLoader.startLoader();

            this.nmiOnHandleCallbackDeferred = $.Deferred();


            _.each(this.getHostedFields(), function (fieldConfig) {
                self.validateField(fieldConfig.config.selector, true);
            });
            config = {
                hostedFields: this.getHostedFields(),
                onValidation: this.onValidation.bind(this),
                onHandleToken: this.onHandleToken.bind(this),
                paymentConfig: this.paymentConfig,
                paymentSelector: this.paymentSelector
            };

            nmi.setConfig(config);
            nmi.setup();
        },

        /**
         * Retrieve deferred object for NMI onHandle callback
         *
         * @return $.Deferred
         */
        getNmiOnHandleCallbackDeferred: function () {
            return this.nmiOnHandleCallbackDeferred;
        },

        /**
         * On complete fields handler
         *
         * @param {String} field
         * @param {Boolean} isValid
         */
        onValidation: function(field, isValid) {
            this.getHostedFields()[field].isValid = isValid;
            this.validateFormFields(field);
        },

        /**
         * Toggle invalid class on selector
         *
         * @param selector
         * @param state
         * @returns {boolean}
         */
        validateField: function (selector, state) {
            var $selector = $(selector),
                invalidClass = 'aw-nmi-field-error';

            if (state === true) {
                $selector.removeClass(invalidClass);
                return true;
            }

            $selector.addClass(invalidClass);
            return false;
        },

        /**
         * Validate all fields
         *
         * @param {String|null} toValidateFieldName
         * @returns {boolean}
         */
        validateFormFields: function (toValidateFieldName) {
            var isValid = true;

            _.each(this.getHostedFields(), function (fieldConfig, fieldName) {
                if (_.isEmpty(toValidateFieldName)
                    || (!_.isEmpty(toValidateFieldName) && toValidateFieldName === fieldName)
                ) {
                    isValid = isValid && fieldConfig.isValid;
                    this.validateField(fieldConfig.config.selector, fieldConfig.isValid)
                }
            }, this);

            return isValid;
        },

        /**
         * On handle token handler
         *
         * @param {object} data
         */
        onHandleToken: function(data) {
            if (data.initiatedBy instanceof Event) {
                this.isPlaceOrderActionAllowed(true);
                this.setPaymentMethodToken(data.token);
                this.setCardData(data.card);
                this.additionalData['is_vault'] = 0;
                if (this.isThreeDSecureEnabled || window.checkoutConfig.payment.aw_nmi.isThreeDSecureEnabled) {
                    optionsProvider.setDefaultOptions({paymentToken: data.token});
                    var options = optionsProvider.getOptions(this.additionalData['is_vault']);

                    threeDSecureAdapter.placeOrder(this, options);
                } else {
                    this.placeOrder('parent');
                }
            } else {
                this.isPlaceOrderActionAllowed(true);
                this.setPaymentMethodToken(null);
                this.setCardData({});
                this.showError($t('Error creating token.'));
            }
            this.nmiOnHandleCallbackDeferred.resolve();
        },

        /**
         * Is valid
         *
         * @returns {Boolean}
         */
        isValid: function () {
            return this.validateFormFields() && additionalValidators.validate();
        },

        /**
         * On click place order button
         */
        placeOrderClick: function () {
            if (this.isValid()) {
                this.isPlaceOrderActionAllowed(false);
                this.placeOrder();
            }
        },

        /**
         * Action to place order
         *
         * @param {string} key
         */
        placeOrder: function (key) {
            if (key) {
                return this._super();
            }

            return false;
        },

        /**
         * Retrieve Hosted Fields
         *
         * @returns {Object}
         */
        getHostedFields: function () {
            if (_.isEmpty(this._fields)) {
                this._fields = {
                    ccnumber: {
                        config: {
                            selector: this.getSelector('cc_number'),
                            title: 'Card Number',
                            placeholder: $t('Card number')
                        },
                        isValid: false
                    },
                    ccexp: {
                        config: {
                            selector: this.getSelector('expiry'),
                            title: 'Card Expiration',
                            placeholder: $t('MM / YY')
                        },
                        isValid: false
                    }
                };

                if (this.hasVerification()) {
                    this._fields.cvv = {
                        config: {
                            selector: this.getSelector('cc_cid'),
                            title: 'CVV Code',
                            placeholder: $t('CVV')
                        },
                        isValid: false
                    };
                }
            }

            return this._fields;
        },
    });
});
