define([
    'jquery',
    'underscore',
    'mage/translate',
], function ($, _, $t) {
    'use strict';

    return {
        config: {},

        /**
         * Set configuration
         *
         * @param {Object} config
         */
        setConfig: function (config) {
            this.config = config;
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

                if (this.config.useCvv) {
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
         * Get jQuery selector
         *
         * @param {String} field
         * @returns {String}
         */
        getSelector: function (field) {
            return '#' + this.config.code + '_' + field;
        },
    };
});
