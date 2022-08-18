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

namespace Mageplaza\StoreLocator\Model\Api;

use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Mageplaza\StoreLocator\Api\Data\LocationsSearchResultInterface;
use Mageplaza\StoreLocator\Api\GuestLocationsInterface;

/**
 * Class GuestLocations
 * @package Mageplaza\StoreLocator\Model\Api
 */
class GuestLocations implements GuestLocationsInterface
{
    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @var Locations
     */
    private $locations;

    /**
     * GuestLocations constructor.
     *
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param Locations $locations
     */
    public function __construct(
        QuoteIdMaskFactory $quoteIdMaskFactory,
        Locations $locations
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->locations          = $locations;
    }

    /**
     * @param string $cartId
     *
     * @return SearchResultsInterface|LocationsSearchResultInterface
     * @throws LocalizedException
     */
    public function getLocations($cartId)
    {
        /** @var QuoteIdMask $quoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');

        return $this->locations->getLocations($quoteIdMask->getQuoteId());
    }
}
