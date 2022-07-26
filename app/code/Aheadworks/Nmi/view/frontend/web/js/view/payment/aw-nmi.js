define([
    'uiComponent',
    'Magento_Checkout/js/model/payment/renderer-list'
], function (Component, rendererList) {
    'use strict';

    rendererList.push(
        {
            type: 'aw_nmi',
            component: 'Aheadworks_Nmi/js/view/payment/method-renderer/hosted-fields',
            config: window.checkoutConfig.payment.aw_nmi
        }
    );

    return Component.extend({});
});
