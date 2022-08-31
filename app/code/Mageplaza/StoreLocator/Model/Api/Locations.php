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

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Mageplaza\StoreLocator\Api\Data\LocationsSearchResultInterface;
use Mageplaza\StoreLocator\Api\LocationsInterface;
use Mageplaza\StoreLocator\Block\Frontend;
use Mageplaza\StoreLocator\Model\LocationsRepository;

/**
 * Class Locations
 * @package Mageplaza\StoreLocator\Model\Api
 */
class Locations implements LocationsInterface
{

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var LocationsRepository
     */
    private $locationsRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var FilterGroupBuilder
     */
    private $filterGroupBuilder;

    /**
     * @var Frontend
     */
    private $frontend;

    /**
     * Locations constructor.
     *
     * @param CartRepositoryInterface $cartRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param LocationsRepository $locationsRepository
     * @param Frontend $frontend
     */
    public function __construct(
        CartRepositoryInterface $cartRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        FilterGroupBuilder $filterGroupBuilder,
        LocationsRepository $locationsRepository,
        Frontend $frontend
    ) {
        $this->cartRepository        = $cartRepository;
        $this->locationsRepository   = $locationsRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder         = $filterBuilder;
        $this->filterGroupBuilder    = $filterGroupBuilder;
        $this->frontend              = $frontend;
    }

    /**
     * @param string $cartId
     *
     * @return SearchResultsInterface|LocationsSearchResultInterface
     * @throws LocalizedException
     */
    public function getLocations($cartId)
    {
        $locationIds = $this->getLocationIdsPickup($cartId);

        $searchCriteria   = $this->searchCriteriaBuilder->create();
        $searchTermFilter = $this->filterBuilder
            ->setField('location_id')
            ->setValue($locationIds)
            ->setConditionType('in')->create();
        $this->filterGroupBuilder->addFilter($searchTermFilter);
        $filterGroups   = $searchCriteria->getFilterGroups();
        $filterGroups[] = $this->filterGroupBuilder->create();
        $searchCriteria->setFilterGroups($filterGroups);

        return $this->locationsRepository->getList($searchCriteria);
    }

    /**
     * @param $cartId
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function getLocationIdsPickup($cartId)
    {
        /** @var Quote $quote */
        $quote    = $this->cartRepository->getActive($cartId);
        $allItems = $quote->getAllItems();

        return $this->frontend->getLocationIdsPickupByQuoteItems($allItems);
    }
}
