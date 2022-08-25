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

require([
    'jquery',
    'jquery/jquery.cookie',
    'domReady!'
], function($) {
    'use strict';

    var url = document.URL.toLowerCase();
    if (url) {
        var skip = false;
        $.each(window.skipModules, function(i, path) {
            if (url.indexOf(path) !== -1) {
                skip = true;
                return false;
            }
        });
        if (!skip) {
            $.cookie(window.queryParam, document.URL, {
                path: '/'
            });
        }
    }
});