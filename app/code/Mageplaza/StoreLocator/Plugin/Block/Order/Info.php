<?php
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

namespace Mageplaza\StoreLocator\Plugin\Block\Order;

/**
 * Class Info
 * @package Mageplaza\StoreLocator\Plugin\Block\Order
 */
class Info
{
    /**
     * @param \Magento\Sales\Block\Order\Info $subject
     */
    public function beforeToHtml(\Magento\Sales\Block\Order\Info $subject)
    {
        $order = $subject->getOrder();
        if ($order->getShippingMethod() === 'mpstorepickup_mpstorepickup'
            && !$this->checkTemplate($subject->getTemplate())
        ) {
            $subject->setTemplate('Mageplaza_StoreLocator::order/info.phtml');
        }
    }

    /**
     * @param $template
     *
     * @return bool
     */
    public function checkTemplate($template)
    {
        return in_array(
            $template,
            [
                'order/order_status.phtml',
                'order/order_date.phtml',
                'Magento_Sales::order/order_status.phtml',
                'Magento_Sales::order/order_date.phtml'
            ]
        );
    }
}
