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

use Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Payment\Model\MethodList;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Store\Model\ScopeInterface;
use Mageplaza\StoreLocator\Helper\Data;

/**
 * Class MethodListPlugin
 * @package Mageplaza\StoreLocator\Plugin\Model
 */
class MethodListPlugin
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * MethodListPlugin constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param Data $helperData
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Data $helperData
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->helperData  = $helperData;
    }

    /**
     * @param MethodList $subject
     * @param callable $proceed
     * @param CartInterface|null $quote
     *
     * @return array
     */
    public function aroundGetAvailableMethods(
        MethodList $subject,
        callable $proceed,
        CartInterface $quote = null
    ) {
        $storeId          = $quote ? $quote->getStoreId() : null;
        $availableMethods = $proceed($quote);
        if ($this->helperData->isEnabled($storeId) && $quote) {
            try {
                $newAvailableMethods = [];
                $shippingMethod      = $quote->getShippingAddress()->getShippingMethod();
                if ($shippingMethod === 'mpstorepickup_mpstorepickup') {
                    $allowPayments = $this->scopeConfig->getValue(
                        'carriers/mpstorepickup/specificpayment',
                        ScopeInterface::SCOPE_STORE,
                        $storeId
                    );

                    $allowPayments = explode(',', $allowPayments);
                    /** @var AbstractMethod $method */
                    foreach ($availableMethods as $method) {
                        if (in_array($method->getCode(), $allowPayments, true)) {
                            $newAvailableMethods[] = $method;
                        }
                    }

                    return $newAvailableMethods;
                }
            } catch (Exception $e) {
                return $availableMethods;
            }
        }

        return $availableMethods;
    }
}
