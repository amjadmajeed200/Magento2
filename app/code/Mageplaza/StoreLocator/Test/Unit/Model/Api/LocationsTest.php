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

namespace Mageplaza\StoreLocator\Test\Unit\Model\Api;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\Search\SearchCriteria;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Mageplaza\StoreLocator\Block\Frontend;
use Mageplaza\StoreLocator\Model\Api\Locations;
use Mageplaza\StoreLocator\Model\LocationsRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class LocationsTest
 * @package Mageplaza\StoreLocator\Test\Unit\Model\Api
 */
class LocationsTest extends TestCase
{
    /**
     * @var Locations
     */
    private $object;

    /**
     * @var CartRepositoryInterface|MockObject
     */
    private $cartRepositoryMock;

    /**
     * @var MockObject
     */
    private $locationsRepositoryMock;

    /**
     * @var MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * @var MockObject
     */
    private $filterBuilderMock;

    /**
     * @var MockObject
     */
    private $filterGroupBuilderMock;

    /**
     * @var MockObject
     */
    private $frontendMock;

    public function testGetLocations()
    {
        $cartId = 1;
        $quote  = $this->getMockBuilder(Quote::class)->disableOriginalConstructor()->getMock();
        $this->cartRepositoryMock->expects($this->once())->method('getActive')->with($cartId)
            ->willReturn($quote);

        $item = $this->getMockBuilder(Item::class)->disableOriginalConstructor()->getMock();

        $quote->expects($this->once())->method('getAllItems')->willReturn([$item]);
        $this->frontendMock->expects($this->once())->method('getLocationIdsPickupByQuoteItems')
            ->with([$item])->willReturn(['1', '2', '3']);
        $searchCriteria = $this->getMockBuilder(SearchCriteria::class)->disableOriginalConstructor()->getMock();
        $this->searchCriteriaBuilderMock->expects($this->once())->method('create')->willReturn($searchCriteria);
        $this->filterBuilderMock->expects($this->once())->method('setField')
            ->with('location_id')->willReturnSelf();
        $this->filterBuilderMock->expects($this->once())->method('setValue')
            ->with(['1', '2', '3'])->willReturnSelf();
        $this->filterBuilderMock->expects($this->once())->method('setConditionType')
            ->with('in')->willReturnSelf();
        $searchTermFilter = $this->getMockBuilder(Filter::class)->disableOriginalConstructor()->getMock();

        $this->filterBuilderMock->expects($this->once())->method('create')->willReturn($searchTermFilter);
        $this->filterGroupBuilderMock->expects($this->once())->method('addFilter')
            ->with($searchTermFilter)->willReturnSelf();
        $filterGroup = $this->getMockBuilder(FilterGroup::class)->disableOriginalConstructor()->getMock();
        $searchCriteria->expects($this->once())->method('getFilterGroups')->willReturn([]);
        $this->filterGroupBuilderMock->expects($this->once())->method('create')->willReturn($filterGroup);
        $searchCriteria->expects($this->once())->method('setFilterGroups')
            ->with([$filterGroup])->willReturnSelf();

        $this->locationsRepositoryMock->expects($this->once())->method('getList')
            ->with($searchCriteria)->willReturn([]);

        $this->object->getLocations($cartId);
    }

    protected function setUp()
    {
        $this->cartRepositoryMock        = $this->getMockForAbstractClass(CartRepositoryInterface::class);
        $this->locationsRepositoryMock   = $this->getMockBuilder(LocationsRepository::class)
            ->disableOriginalConstructor()->getMock();
        $this->searchCriteriaBuilderMock = $this->getMockBuilder(SearchCriteriaBuilder::class)
            ->disableOriginalConstructor()->getMock();
        $this->filterBuilderMock         = $this->getMockBuilder(FilterBuilder::class)
            ->disableOriginalConstructor()->getMock();
        $this->filterGroupBuilderMock    = $this->getMockBuilder(FilterGroupBuilder::class)
            ->disableOriginalConstructor()->getMock();
        $this->frontendMock              = $this->getMockBuilder(Frontend::class)
            ->disableOriginalConstructor()->getMock();
        $objectManagerHelper             = new ObjectManager($this);

        $this->object = $objectManagerHelper->getObject(
            Locations::class,
            [
                'cartRepository'        => $this->cartRepositoryMock,
                'locationsRepository'   => $this->locationsRepositoryMock,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock,
                'filterBuilder'         => $this->filterBuilderMock,
                'filterGroupBuilder'    => $this->filterGroupBuilderMock,
                'frontend'              => $this->frontendMock,
            ]
        );
    }
}
