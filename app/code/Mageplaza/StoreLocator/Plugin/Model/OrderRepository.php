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

namespace Mageplaza\StoreLocator\Plugin\Model;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Mageplaza\StoreLocator\Helper\Data as HelperData;

/**
 * Class OrderRepository
 * @package Mageplaza\StoreLocator\Plugin\Model
 */
class OrderRepository
{
    /**
     * @var helperData
     */
    protected $_helperData;

    /**
     * OrderRepository constructor.
     *
     * @param HelperData $helperData
     */
    public function __construct(
        HelperData $helperData
    ) {
        $this->_helperData = $helperData;
    }

    /**
     * @param \Magento\Sales\Model\OrderRepository $subject
     * @param OrderInterface $result
     *
     * @return mixed
     */
    public function afterGet(\Magento\Sales\Model\OrderRepository $subject, $result)
    {
        if ($timePickup = $result->getMpTimePickup()) {
            $orderExtensionAtt = $result->getExtensionAttributes();
            if ($orderExtensionAtt) {
                $shippingAssignments = $orderExtensionAtt->getShippingAssignments();
                if ($shippingAssignments) {
                    $shipping       = $shippingAssignments[0]->getShipping();
                    $shippingExtAtt = $shipping->getExtensionAttributes();
                    if ($shippingExtAtt) {
                        $shippingExtAtt->setMpTimePickup($timePickup);
                        $shipping->setExtensionAttributes($shippingExtAtt);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @param \Magento\Sales\Model\OrderRepository $subject
     * @param OrderSearchResultInterface $result
     *
     * @return OrderSearchResultInterface
     */
    public function afterGetList(\Magento\Sales\Model\OrderRepository $subject, $result)
    {
        foreach ($result->getItems() as $order) {
            if (!($timePickup = $order->getMpTimePickup())) {
                continue;
            }

            $orderExtensionAtt = $order->getExtensionAttributes();
            if ($orderExtensionAtt) {
                $shippingAssignments = $orderExtensionAtt->getShippingAssignments();
                if ($shippingAssignments) {
                    $shipping       = $shippingAssignments[0]->getShipping();
                    $shippingExtAtt = $shipping->getExtensionAttributes();
                    if ($shippingExtAtt) {
                        $shippingExtAtt->setMpTimePickup($timePickup);
                        $shipping->setExtensionAttributes($shippingExtAtt);
                    }
                }
            }
        }

        return $result;
    }
}
