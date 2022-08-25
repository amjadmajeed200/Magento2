/**
 * Cpl
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Cpl
 * @package    Cpl_SocialConnect
 * @copyright  Copyright (c) 2022 Cpl (https://www.magento.com/)
 */

define([
    'jquery'
], function ($) {
    'use strict';
    return function(config){
        $(document).on("click", ".copy-link", function(event){
            event.preventDefault();
            var provider = jQuery(this).attr('data-provider');
            var textToCopy = $('#cpl_socialconnect_'+provider+'_callback');
            var tooltipElement = $('#copied_' + provider);
            textToCopy.attr('disabled', false);
            tooltipElement.fadeIn();
            textToCopy.select();
            document.execCommand("copy");
            setTimeout(function() {
                tooltipElement.fadeOut('slow');
            }, 3000);
            textToCopy.attr('disabled', true);
        });
    }
});