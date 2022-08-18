/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_StoreLocator
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

define([
    'jquery'
], function ($) {
    "use strict";

    return {
        useConfigAction: function (selectElement, inputElement) {
            selectElement.parent().removeClass('addafter');
            if (selectElement.is(':checked')) {
                inputElement.prop('disabled', true);
            } else {
                inputElement.prop('disabled', false);
            }
            selectElement.on('change', function () {
                if ($(this).is(':checked')) {
                    $(this).val(1);
                    inputElement.prop('disabled', true);
                } else {
                    $(this).val(0);
                    inputElement.prop('disabled', false);
                }
            });
        }
    };
});