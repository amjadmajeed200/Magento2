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
    $.widget('mpstorelocator.is_selected', {
        _create: function () {
            var selectedValue = $('#available_products_is_selected_all_product').val(),
                productGrid   = $('#productsGrid');

            if (selectedValue === '1') {
                productGrid.hide();
            }
            this.isSelectedAll();
        },

        isSelectedAll: function () {
            var button      = $('#available_products_is_selected_all_product'),
                productGrid = $('#productsGrid');

            button.on('change', function () {
                if (button.val() === '1') {
                    productGrid.hide();
                } else {
                    productGrid.show();
                }
            });
        }
    });

    return $.mpstorelocator.is_selected;
});
