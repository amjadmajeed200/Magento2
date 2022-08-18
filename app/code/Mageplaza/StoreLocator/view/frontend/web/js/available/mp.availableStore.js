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
    'jquery',
    'Mageplaza_Core/js/jquery.magnific-popup.min'
], function ($) {
    'use strict';

    $.widget('mageplaza.availableStore', {

        /**
         * @inheritDoc
         */
        _create: function () {
            $('#mpstorepickup-available-store-button').magnificPopup({
                delegate: 'a.mpstorepickup-available-popup',
                removalDelay: 0,
                midClick: true,
                callbacks: {
                    beforeOpen: function () {
                        jQuery('body').css('overflow', 'hidden');
                    },
                    beforeClose: function () {
                        jQuery('body').css('overflow', 'auto');
                    }
                }
            });
        }
    });

    return $.mageplaza.availableStore;
});