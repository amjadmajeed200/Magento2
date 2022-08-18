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

use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteValidator;

/**
 * Class DismissValidator
 * @package Mageplaza\StoreLocator\Plugin\Model
 */
class DismissValidator
{
    /**
     * @param QuoteValidator $subject
     * @param callable $proceed
     * @param Quote $quote
     *
     * @return $this
     * @SuppressWarnings(Unused)
     */
    public function aroundValidateBeforeSubmit(QuoteValidator $subject, callable $proceed, $quote)
    {
        if ($quote->getShippingAddress()->getShippingMethod() === 'mpstorepickup_mpstorepickup') {
            return $this;
        }

        return $proceed($quote);
    }
}
