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

namespace Mageplaza\StoreLocator\Plugin\Adminhtml\Order\Create\Shipping\Method;

use Magento\Sales\Block\Adminhtml\Order\Create\Shipping\Method\Form;

/**
 * Class RemovePickupMethod
 * @package Mageplaza\StoreLocator\Plugin\Adminhtml\Order\Create\Shipping\Method
 */
class RemovePickupMethod
{
    /**
     * Remove pickup shipping method when create order in backend
     *
     * @param Form $subject
     * @param $result
     *
     * @return mixed
     * @SuppressWarnings(Unused)
     */
    public function afterGetShippingRates(Form $subject, $result)
    {
        foreach ($result as $code => $_rates) {
            if ($code === 'mpstorepickup') {
                unset($result['mpstorepickup']);
            }
        }

        return $result;
    }
}
