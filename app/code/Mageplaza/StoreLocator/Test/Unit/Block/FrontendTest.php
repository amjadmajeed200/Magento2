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

namespace Mageplaza\StoreLocator\Test\Unit\Block;

use Exception;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Model\Product;
use Magento\Checkout\Model\Cart;
use Magento\Cms\Model\BlockFactory;
use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Title;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\StoreLocator\Block\Frontend;
use Mageplaza\StoreLocator\Helper\Data as HelperData;
use Mageplaza\StoreLocator\Helper\Image as HelperImage;
use Mageplaza\StoreLocator\Model\Config\Source\System\MapStyle;
use Mageplaza\StoreLocator\Model\Holiday;
use Mageplaza\StoreLocator\Model\HolidayFactory;
use Mageplaza\StoreLocator\Model\Location;
use Mageplaza\StoreLocator\Model\LocationFactory;
use Mageplaza\StoreLocator\Model\ResourceModel\Holiday as HolidayResource;
use Mageplaza\StoreLocator\Model\ResourceModel\Location as LocationResource;
use Mageplaza\StoreLocator\Model\ResourceModel\Location\Collection;
use Mageplaza\StoreLocator\Model\ResourceModel\Location\CollectionFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use ReflectionClass;
use ReflectionException;

/**
 * Class FrontendTest
 * @package Mageplaza\StoreLocator\Test\Unit\Block
 */
class FrontendTest extends TestCase
{
    /**
     * @var Context|PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var DateTime|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dateTime;

    /**
     * @var TimezoneInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_timeZone;

    /**
     * @var HelperData|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperData;

    /**
     * @var LocationFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $locationFactory;

    /**
     * @var HolidayFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $holidayFactory;

    /**
     * @var HelperImage|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperImage;

    /**
     * @var BlockFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_blockFactory;

    /**
     * @var Cart|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cart;

    /**
     * @var ProductRepositoryInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $productRepository;

    /**
     * @var CollectionFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_locationColFactory;

    /**
     * @var LocationResource|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_locationResource;

    /**
     * @var HolidayResource|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_holidayResource;

    /**
     * @var ManagerInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageManager;

    /**
     * @var StoreManagerInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManager;

    /**
     * @var Frontend|PHPUnit_Framework_MockObject_MockObject
     */
    private $object;

    /**
     * @var Config|MockObject
     */
    private $pageConfig;

    /**
     * @var RequestInterface|MockObject
     */
    private $request;

    /**
     * @var Repository|MockObject
     */
    private $assetRepoMock;

    /**
     * @var UrlInterface|MockObject
     */
    private $urlBuilderMock;

    /**
     * @var Escaper|MockObject
     */
    private $escaperMock;

    public function testAdminInstance()
    {
        $this->assertInstanceOf(Frontend::class, $this->object);
    }

    /**
     * @return array
     */
    public function providerSeoCode()
    {
        return [
            [
                'Meta Title',
                'Meta Title'
            ],
            [
                null,
                'Find a store'
            ],
        ];
    }

    /**
     * @param $metaTitle
     * @param $pageTitle
     *
     * @dataProvider providerSeoCode
     */
    public function testApplySeoCode($metaTitle, $pageTitle)
    {
        $description = 'Meta description';
        $keywords    = 'Meta keywords';
        $title       = $this->getMockBuilder(Title::class)->disableOriginalConstructor()->getMock();
        $this->pageConfig->expects($this->once())->method('getTitle')->willReturn($title);

        $this->_helperData->expects($this->once())->method('getConfigValue')
            ->with('catalog/seo/title_separator')->willReturn('-');
        $this->_helperData->expects($this->once())->method('getPageTitle')
            ->willReturn('Find a store');
        $this->_helperData->expects($this->exactly(3))->method('getSeoSetting')
            ->withConsecutive(['meta_title'], ['meta_description'], ['meta_keywords'])
            ->willReturnOnConsecutiveCalls($metaTitle, $description, $keywords);

        $title->expects($this->once())->method('set')->with($pageTitle)->willReturnSelf();

        $this->pageConfig->expects($this->once())->method('setDescription')
            ->with($description)->willReturnSelf();
        $this->pageConfig->expects($this->once())->method('setKeywords')
            ->with($keywords)->willReturnSelf();

        $this->object->applySeoCode();
    }

    public function testGetTitleSeparator()
    {
        $this->_helperData->expects($this->once())->method('getConfigValue')
            ->with('catalog/seo/title_separator')->willReturn('-');
        $this->assertEquals(' - ', $this->object->getTitleSeparator());
    }

    /**
     * @return array[]
     */
    public function providerStoreLocatorTitle()
    {
        return [
            [true, 'Page Title', 'Meta Title', ['Meta Title']],
            [true, 'Page Title', '', ['Page Title']],
            [false, 'Page Title', 'Meta Title', 'Page Title'],
        ];
    }

    /**
     * @param $meta
     * @param $pageTitle
     * @param $metaTitle
     * @param $result
     *
     * @dataProvider providerStoreLocatorTitle
     */
    public function testGetStoreLocatorTitle($meta, $pageTitle, $metaTitle, $result)
    {
        $this->_helperData->expects($this->once())->method('getPageTitle')
            ->willReturn($pageTitle);

        if ($meta) {
            $this->_helperData->expects($this->once())->method('getSeoSetting')
                ->with('meta_title')->willReturn($metaTitle);
        }

        $this->assertEquals($result, $this->object->getStoreLocatorTitle($meta));
    }

    /**
     * @return array[]
     */
    public function providerBreadcrumbs()
    {
        return [
            [
                'mpstorelocator_storelocator_store',
                [
                    'label' => 'Find a store',
                    'title' => 'Find a store'
                ]
            ],
            [
                'mpstorelocator_storelocator_view',
                [
                    'label' => 'Find a store',
                    'title' => 'Find a store'
                ]
            ],
            [
                'test',
                [
                    'label' => 'Find a store',
                    'title' => 'Find a store',
                    'link'  => 'store-locator-url'
                ]
            ]
        ];
    }

    /**
     * @param $fullActionName
     * @param $result
     *
     * @dataProvider providerBreadcrumbs
     * @throws ReflectionException
     */
    public function testGetBreadcrumbsData($fullActionName, $result)
    {
        $additional = new ReflectionClass(Frontend::class);
        $method     = $additional->getMethod('getBreadcrumbsData');
        $method->setAccessible(true);
        $this->_helperData->expects($this->once())->method('getPageTitle')
            ->willReturn('Find a store');
        $this->request->expects($this->once())->method('getFullActionName')->willReturn($fullActionName);
        if ($fullActionName !== 'mpstorelocator_storelocator_store' &&
            $fullActionName !== 'mpstorelocator_storelocator_view'
        ) {
            $this->_helperData->expects($this->once())->method('getStoreLocatorUrl')
                ->willReturn('store-locator-url');
        }

        $this->assertEquals($result, $method->invokeArgs($this->object, []));
    }

    public function testGetLocationList()
    {
        $locations = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()->getMock();
        $this->_locationColFactory->expects($this->once())->method('create')->willReturn($locations);
        $locations->expects($this->once())->method('addFieldToFilter')->with('status', 1)->willReturnSelf();
        $store = $this->getMockBuilder(StoreInterface::class)
            ->getMockForAbstractClass();
        $this->_storeManager->expects($this->once())->method('getStore')->willReturn($store);
        $storeId = '1';
        $store->expects($this->once())->method('getId')->willReturn($storeId);
        $location = $this->getMockBuilder(Location::class)
            ->disableOriginalConstructor()->getMock();
        $locations->expects($this->once())->method('getItems')->willReturn([$location]);
        $location->expects($this->atLeastOnce())->method('getStoreIds')->willReturn('1,2,3');

        $this->assertEquals([$location], $this->object->getLocationList());
    }

    public function testGetLocationListThrowException()
    {
        $locations = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()->getMock();
        $this->_locationColFactory->expects($this->once())->method('create')->willReturn($locations);
        $locations->expects($this->once())->method('addFieldToFilter')->with('status', 1)->willReturnSelf();
        $this->_storeManager->expects($this->once())
            ->method('getStore')
            ->willThrowException(new NoSuchEntityException(__('No such entity')));
        $this->messageManager->expects($this->once())
            ->method('addErrorMessage')
            ->with(__('No such entity'))
            ->willReturnSelf();

        $this->assertEquals([], $this->object->getLocationList());
    }

    public function testGetLocationIdsPickup()
    {
        $quote = $this->getMockBuilder(Quote::class)->disableOriginalConstructor()->getMock();
        $this->_cart->expects($this->once())->method('getQuote')->willReturn($quote);
        $quoteItemMethods = array_merge(
            get_class_methods(Item::class),
            [
                'getHasChildren'
            ]
        );
        $quoteItem        = $this->getMockBuilder(Item::class)
            ->setMethods($quoteItemMethods)
            ->disableOriginalConstructor()->getMock();
        $quote->expects($this->once())->method('getAllItems')->willReturn([$quoteItem, $quoteItem]);
        $quoteItem->expects($this->atLeastOnce())->method('getHasChildren')->willReturn(1);
        $product = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()->getMock();
        $quoteItem->expects($this->atLeastOnce())->method('getProduct')->willReturn($product);
        $product->expects($this->atLeastOnce())->method('getTypeId')->willReturn('configurable');
        $childMethods = array_unique(array_merge(
            get_class_methods(AbstractItem::class),
            [
                'getProduct'
            ]
        ));
        $child        = $this->getMockForAbstractClass(
            AbstractItem::class,
            [],
            '',
            false,
            false,
            false,
            $childMethods
        );
        $quoteItem->expects($this->atLeastOnce())->method('getChildren')->willReturn([$child, $child]);
        $child->expects($this->atLeastOnce())->method('getProduct')->willReturn($product);
        $product->expects($this->exactly(6))->method('getId')
            ->willReturnOnConsecutiveCalls(11, 12, 21, 22, 1, 11);
        $this->productRepository->expects($this->atLeastOnce())->method('getById')->with(1)->willReturn($product);
        $customAttr = $this->getMockForAbstractClass(
            AttributeInterface::class
        );
        $product->expects($this->atLeastOnce())->method('getCustomAttribute')->with('mp_pickup_locations')
            ->willReturn($customAttr);
        $customAttr->expects($this->atLeastOnce())->method('getValue')
            ->willReturn('home-1,mageplaza-2');

        $this->assertEquals(['1', '2'], $this->object->getLocationIdsPickup());
    }

    /**
     * @return array[]
     */
    public function providerFilterLocation()
    {
        $location = $this->getMockBuilder(Location::class)
            ->disableOriginalConstructor()->getMock();

        return [
            ['1', '0', [$location]],
            ['1', '1,2,3', [$location]],
            ['1', '2,3', []],
            ['1', '', []],
        ];
    }

    /**
     * @param $storeId
     * @param $storeIds
     * @param $result
     *
     * @dataProvider providerFilterLocation
     * @throws NoSuchEntityException
     */
    public function testFilterLocation($storeId, $storeIds, $result)
    {
        /** @var Collection|MockObject $locationCollection */
        $locationCollection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()->getMock();
        $store              = $this->getMockBuilder(StoreInterface::class)
            ->getMockForAbstractClass();
        $this->_storeManager->expects($this->once())->method('getStore')->willReturn($store);
        $store->expects($this->once())->method('getId')->willReturn($storeId);
        $location = $this->getMockBuilder(Location::class)
            ->disableOriginalConstructor()->getMock();
        $locationCollection->expects($this->once())->method('getItems')->willReturn([$location]);
        $location->method('getStoreIds')->willReturn($storeIds);

        $this->assertEquals($result, $this->object->filterLocation($locationCollection));
    }

    /**
     * @return array
     */
    public function providerZoom()
    {
        return [
            ['1', '1'],
            ['', 12],
            [null, 12]
        ];
    }

    /**
     * @param $default
     * @param $result
     *
     * @dataProvider providerZoom
     */
    public function testGetZoom($default, $result)
    {
        $this->_helperData->expects($this->once())->method('getMapSetting')->with('zoom_default')
            ->willReturn($default);

        $this->assertEquals($result, $this->object->getZoom());
    }

    /**
     * @return array[]
     */
    public function providerFilterRadius()
    {
        return [
            ['10,20', ['10', '20']],
            ['', false],
            [null, false]
        ];
    }

    /**
     * @param $config
     * @param $result
     *
     * @dataProvider providerFilterRadius
     */
    public function testGetFilterRadius($config, $result)
    {
        $this->_helperData->expects($this->once())->method('getMapSetting')->with('filter_radius')
            ->willReturn($config);

        $this->assertEquals($result, $this->object->getFilterRadius());
    }

    /**
     * @return array[]
     */
    public function providerKmToMiles()
    {
        return [
            ['1', '1', 1],
            ['0', '1', 0.621371],
        ];
    }

    /**
     * @param $config
     * @param $distance
     * @param $result
     *
     * @dataProvider providerKmToMiles
     */
    public function testConvertKmToMiles($config, $distance, $result)
    {
        $this->_helperData->expects($this->once())->method('getMapSetting')->with('distance_unit')
            ->willReturn($config);

        $this->assertEquals($result, $this->object->convertKmToMiles($distance));
    }

    /**
     * @return string[][]
     */
    public function providerUnitText()
    {
        return [
            ['1', 'Miles'],
            ['0', 'Km'],
        ];
    }

    /**
     * @param $config
     * @param $result
     *
     * @dataProvider providerUnitText
     */
    public function testGetUnitText($config, $result)
    {
        $this->_helperData->expects($this->once())->method('getMapSetting')->with('distance_unit')
            ->willReturn($config);

        $this->assertEquals($result, $this->object->getUnitText());
    }

    /**
     * @return array
     */
    public function providerDefaultRadius()
    {
        return [
            ['1', '1'],
            ['0', 10000],
        ];
    }

    /**
     * @param $config
     * @param $result
     *
     * @dataProvider providerDefaultRadius
     */
    public function testGetDefaultRadius($config, $result)
    {
        $this->_helperData->expects($this->once())->method('getMapSetting')->with('default_radius')
            ->willReturn($config);

        $this->assertEquals($result, $this->object->getDefaultRadius());
    }

    /**
     * @return array[]
     */
    public function providerStoreImages()
    {
        return [
            ['[]', []],
            ['', []]
        ];
    }

    /**
     * @param $imageJson
     * @param $result
     *
     * @dataProvider providerStoreImages
     */
    public function testGetStoreImages($imageJson, $result)
    {
        $location = $this->getMockBuilder(Location::class)
            ->disableOriginalConstructor()->getMock();

        $location->expects($this->once())->method('getImages')->willReturn($imageJson);

        if ($imageJson) {
            $this->_helperData->expects($this->once())->method('jsDecode')->with($imageJson)
                ->willReturn($result);
        }

        $this->assertEquals($result, $this->object->getStoreImages($location));
    }

    /**
     * @param $imageJson
     * @param $storeImages
     * @param $result
     *
     * @dataProvider providerStoreMainImages
     */
    public function testGetStoreMainImage($imageJson, $storeImages, $result)
    {
        $location = $this->getMockBuilder(Location::class)
            ->disableOriginalConstructor()->getMock();

        $location->expects($this->once())->method('getImages')->willReturn($imageJson);

        if ($imageJson) {
            $this->_helperData->expects($this->once())->method('jsDecode')->with($imageJson)
                ->willReturn($storeImages);
        }

        $this->assertEquals($result, $this->object->getStoreMainImage($location));
    }

    /**
     * @return array[]
     */
    public function providerStoreMainImageUrl()
    {
        $storeImages       = $this->providerStoreMainImages();
        $storeImages[0][2] = 'resized-image-url';
        $storeImages[1][2] = 'default_image_url';

        return $storeImages;
    }

    /**
     * @return array[]
     */
    public function providerStoreMainImages()
    {
        return [
            [
                '[{"position":"1","file":"\/i\/m\/image2.png","label":"image-alt"},{"position":"2","file":"\/i\/m\/image1.png","label":""}]',
                [
                    [
                        'position' => '1',
                        'file'     => '/i/m/image1.png',
                        'label'    => 'image-alt'
                    ],
                    [
                        'position' => '2',
                        'file'     => '/i/m/image2.png',
                        'label'    => ''
                    ]
                ],
                [
                    'position' => '1',
                    'file'     => '/i/m/image1.png',
                    'label'    => 'image-alt'
                ]
            ],
            ['', [], []]
        ];
    }

    /**
     * @param $imageJson
     * @param $storeImages
     * @param $result
     *
     * @dataProvider providerStoreMainImageUrl
     * @throws NoSuchEntityException
     */
    public function testGetStoreMainImageUrl($imageJson, $storeImages, $result)
    {
        $location = $this->getMockBuilder(Location::class)
            ->disableOriginalConstructor()->getMock();
        $location->expects($this->once())->method('getImages')->willReturn($imageJson);
        if ($imageJson) {
            $this->_helperData->expects($this->once())->method('jsDecode')->with($imageJson)
                ->willReturn($storeImages);
        }
        if (isset($storeImages[0]['file'])) {
            $this->_helperImage->expects($this->once())->method('resizeImage')
                ->with($storeImages[0]['file'], '100x', '')
                ->willReturn('resized-image-url');
        }

        $this->assertEquals($result, $this->object->getStoreMainImageUrl($location));
    }

    /**
     * @return array[]
     */
    public function providerStoreMainImageAlt()
    {
        $storeImages       = $this->providerStoreMainImages();
        $storeImages[0][2] = 'image-alt';
        $storeImages[1][2] = __('Store Image');

        return $storeImages;
    }

    /**
     * @param $imageJson
     * @param $storeImages
     * @param $result
     *
     * @dataProvider providerStoreMainImageAlt
     */
    public function testGetStoreMainImageAlt($imageJson, $storeImages, $result)
    {
        $location = $this->getMockBuilder(Location::class)
            ->disableOriginalConstructor()->getMock();
        $location->expects($this->once())->method('getImages')->willReturn($imageJson);
        if ($imageJson) {
            $this->_helperData->expects($this->once())->method('jsDecode')->with($imageJson)
                ->willReturn($storeImages);
        }

        $this->assertEquals($result, $this->object->getStoreMainImageAlt($location));
    }

    /**
     * @return array[]
     */
    public function providerCheckHoliday()
    {
        $time = strtotime('10:00');

        return [
            [$time, '0', '10:00', '10:00', false],
            [$time, '1', '10:00', '10:00', true],
            [$time, '1', '00:00', '09:00', false],
            [$time, '1', '00:00', '11:00', true],
            [$time, '1', '11:00', '12:00', false],
        ];
    }

    /**
     * @param $currentTime
     * @param $status
     * @param $from
     * @param $to
     * @param $result
     *
     * @dataProvider providerCheckHoliday
     */
    public function testCheckHoliday($currentTime, $status, $from, $to, $result)
    {
        $holidayIds     = [1];
        $holidayMethods = array_unique(array_merge(
            get_class_methods(Holiday::class),
            [
                'getStatus',
                'getFrom',
                'getTo',
            ]
        ));
        $holiday        = $this->getMockBuilder(Holiday::class)
            ->setMethods($holidayMethods)
            ->disableOriginalConstructor()->getMock();
        $this->holidayFactory->expects($this->once())->method('create')->willReturn($holiday);
        $holiday->expects($this->once())->method('load')->willReturnSelf();
        $holiday->expects($this->once())->method('getStatus')->willReturn($status);
        $holiday->method('getFrom')->willReturn($from);
        $holiday->method('getTo')->willReturn($to);

        $this->assertEquals($result, $this->object->checkHoliday($holidayIds, $currentTime));
    }

    public function testGetOpenCloseNotifyWithHoliday()
    {
        /** @var Location|MockObject $location */
        $location = $this->getMockBuilder(Location::class)
            ->disableOriginalConstructor()->getMock();
        $this->_dateTime->expects($this->once())->method('date')->willReturn('2020-06-20 10:00:00');
        $location->expects($this->once())->method('getTimeZone')->willReturn('UTC');
        $location->expects($this->once())->method('getLocationId')->willReturn(1);
        $this->_locationResource->expects($this->once())->method('getHolidayIdsByLocation')
            ->with(1)->willReturn([1]);

        $holidayMethods = array_unique(array_merge(
            get_class_methods(Holiday::class),
            [
                'getStatus',
                'getFrom',
                'getTo',
            ]
        ));
        $holiday        = $this->getMockBuilder(Holiday::class)
            ->setMethods($holidayMethods)
            ->disableOriginalConstructor()->getMock();
        $this->holidayFactory->expects($this->once())->method('create')->willReturn($holiday);
        $holiday->expects($this->once())->method('load')->willReturnSelf();
        $holiday->expects($this->once())->method('getStatus')->willReturn(1);
        $holiday->method('getFrom')->willReturn('10:00');
        $holiday->method('getTo')->willReturn('10:00');

        $this->assertEquals(__('Closed'), $this->object->getOpenCloseNotify($location));
    }

    /**
     * @return array[]
     */
    public function providerOpenCloseNotify()
    {
        return [
            [
                '2020-01-26 10:00:00',
                HelperData::SUNDAY,
                [
                    'value' => '1',
                    'from'  => ['08', '00'],
                    'to'    => ['17', '30'],
                ],
                __('Open now: %1 - %2', '08:00', '17:30')
            ],
            [
                '2020-01-25 10:00:00',
                HelperData::SATURDAY,
                [
                    'value' => '1',
                    'from'  => ['08', '00'],
                    'to'    => ['17', '30'],
                ],
                __('Open now: %1 - %2', '08:00', '17:30')
            ],
            [
                '2020-01-24 10:00:00',
                HelperData::FRIDAY,
                [
                    'value' => '0',
                    'from'  => ['08', '00'],
                    'to'    => ['17', '30'],
                ],
                __('Closed')
            ],
            [
                '2020-01-23 10:00:00',
                HelperData::THURSDAY,
                [
                    'value' => '1',
                    'from'  => ['12', '00'],
                    'to'    => ['17', '30'],
                ],
                __('Open at %1 %2', '12:00', 'AM')
            ],
            [
                '2020-01-22 10:00:00',
                HelperData::WEDNESDAY,
                [
                    'value' => '1',
                    'from'  => ['12', '00'],
                    'to'    => ['17', '30'],
                ],
                __('Open at %1 %2', '12:00', 'AM')
            ],
            [
                '2020-01-21 10:00:00',
                HelperData::TUESDAY,
                [
                    'value' => '1',
                    'from'  => ['12', '00'],
                    'to'    => ['17', '30'],
                ],
                __('Open at %1 %2', '12:00', 'AM')
            ],
            [
                '2020-01-20 10:00:00',
                HelperData::MONDAY,
                [
                    'value' => '1',
                    'from'  => ['12', '00'],
                    'to'    => ['17', '30'],
                ],
                __('Open at %1 %2', '12:00', 'AM')
            ],
        ];
    }

    /**
     * @param $nowDateTime
     * @param $day
     * @param $openTime
     * @param $result
     *
     * @throws Exception
     * @dataProvider providerOpenCloseNotify
     *
     */
    public function testGetOpenCloseNotify($nowDateTime, $day, $openTime, $result)
    {
        /** @var Location|MockObject $location */
        $location = $this->getMockBuilder(Location::class)
            ->disableOriginalConstructor()->getMock();

        $this->_dateTime->expects($this->once())->method('date')->willReturn($nowDateTime);
        $location->expects($this->once())->method('getTimeZone')->willReturn('UTC');
        $location->expects($this->once())->method('getLocationId')->willReturn(1);
        $this->_locationResource->expects($this->once())->method('getHolidayIdsByLocation')
            ->with(1)->willReturn([]);

        switch ($day) {
            case HelperData::MONDAY:
                $location->expects($this->atLeastOnce())->method('getOperationMon')->willReturn('open-time');
                break;
            case HelperData::TUESDAY:
                $location->expects($this->atLeastOnce())->method('getOperationTue')->willReturn('open-time');
                break;
            case HelperData::WEDNESDAY:
                $location->expects($this->atLeastOnce())->method('getOperationWed')->willReturn('open-time');
                break;
            case HelperData::THURSDAY:
                $location->expects($this->atLeastOnce())->method('getOperationThu')->willReturn('open-time');
                break;
            case HelperData::FRIDAY:
                $location->expects($this->atLeastOnce())->method('getOperationFri')->willReturn('open-time');
                break;
            case HelperData::SATURDAY:
                $location->expects($this->atLeastOnce())->method('getOperationSat')->willReturn('open-time');
                break;
            default:
                $location->expects($this->atLeastOnce())->method('getOperationSun')->willReturn('open-time');
                break;
        }

        $this->_helperData->expects($this->once())->method('jsDecode')->with('open-time')
            ->willReturn($openTime);

        $this->assertEquals($result, $this->object->getOpenCloseNotify($location));
    }

    /**
     * @return array[]
     */
    public function providerGetOpenCloseTime()
    {
        return [
            [
                [
                    'value' => '1',
                    'from'  => ['08', '00'],
                    'to'    => ['17', '30'],
                ],
                __('Open now: %1 - %2', '08:00', '17:30')
            ],
            [
                [
                    'value' => '0',
                    'from'  => ['08', '00'],
                    'to'    => ['17', '30'],
                ],
                __('Closed')
            ],
            [
                [
                    'value' => '1',
                    'from'  => ['12', '00'],
                    'to'    => ['17', '30'],
                ],
                __('Open at %1 %2', '12:00', 'AM')
            ],
        ];
    }

    /**
     * @param $openTime
     * @param $result
     *
     * @dataProvider providerGetOpenCloseTime
     */
    public function testGetOpenCloseTime($openTime, $result)
    {
        $this->_helperData->expects($this->once())->method('jsDecode')->with('open-time')
            ->willReturn($openTime);

        $this->assertEquals($result, $this->object->getOpenCloseTime('open-time', strtotime('10:00')));
    }

    /**
     * @return string[][]
     */
    public function providerResizeImage()
    {
        return [
            ['/i/m/image.png', 'resized-image-url'],
            ['', 'default_image_url']
        ];
    }

    /**
     * @param $image
     * @param $result
     *
     * @dataProvider providerResizeImage
     * @throws NoSuchEntityException
     */
    public function testResizeImage($image, $result)
    {
        if ($image) {
            $this->_helperImage->expects($this->once())->method('resizeImage')
                ->with($image, null, '')->willReturn($result);
        }

        $this->assertEquals($result, $this->object->resizeImage($image));
    }

    public function testGetDataLocations()
    {
        $location = $this->getMockBuilder(Location::class)->disableOriginalConstructor()->getMock();

        $locations = [$location];

        $data = [
            'id'          => '1',
            'lat'         => '123',
            'lng'         => '123',
            'name'        => 'name',
            'street'      => 'street',
            'state'       => 'state',
            'city'        => 'city',
            'country'     => 'country',
            'postal'      => 'postal',
            'phone1'      => 'phone1',
            'phone2'      => 'phone2',
            'web'         => 'web',
            'facebook'    => 'facebook',
            'twitter'     => 'twitter',
            'time'        => __('Open now: %1 - %2', '08:00', '17:30'),
            'image'       => 'default_image_url',
            'fax'         => 'fax',
            'mail'        => 'mail',
            'description' => 'description',
            'markerUrl'   => 'markerUrl',
            'category'    => 'Restaurant',
            'address'     => 'street',
            'address2'    => '',
            'url'         => 'url',
            'details'     => __('Details +')
        ];

        $location->method('getLocationId')->willReturn($data['id']);
        $location->method('getLatitude')->willReturn($data['lat']);
        $location->method('getLongitude')->willReturn($data['lng']);
        $location->method('getName')->willReturn($data['name']);
        $location->method('getStreet')->willReturn($data['street']);
        $location->method('getStateProvince')->willReturn($data['state']);
        $location->method('getCity')->willReturn($data['city']);
        $location->method('getCountry')->willReturn($data['country']);
        $location->method('getPostalCode')->willReturn($data['postal']);
        $location->method('getPhoneOne')->willReturn($data['phone1']);
        $location->method('getPhoneTwo')->willReturn($data['phone2']);
        $location->method('getWebsite')->willReturn($data['web']);
        $location->method('getFacebook')->willReturn($data['facebook']);
        $location->method('getTwitter')->willReturn($data['getTwitter']);
        $location->method('getFax')->willReturn($data['fax']);
        $location->method('getEmail')->willReturn($data['mail']);
        $location->method('getDescription')->willReturn($data['description']);
        $location->method('getUrlKey')->willReturn($data['url']);

        $this->_dateTime->expects($this->once())->method('date')->willReturn('2020-06-20 10:00:00');
        $location->expects($this->once())->method('getTimeZone')->willReturn('UTC');
        $this->_locationResource->expects($this->once())->method('getHolidayIdsByLocation')
            ->with(1)->willReturn([]);
        $location->expects($this->once())->method('getOperationSat')->willReturn('open-time');
        $this->_helperData->expects($this->once())->method('jsDecode')->with('open-time')
            ->willReturn([
                'value' => '1',
                'from'  => ['08', '00'],
                'to'    => ['17', '30'],
            ]);

        $location->expects($this->once())->method('getImages')->willReturn('');
        $this->_helperData->expects($this->once())->method('getMapSetting')->with('marker_icon')
            ->willReturn(0);

        $fileId = 'Mageplaza_StoreLocator::media/marker.png';
        $this->request->expects($this->once())->method('isSecure')->willReturn('false');
        $this->assetRepoMock->expects($this->once())
            ->method('getUrlWithParams')
            ->with($fileId, ['_secure' => 'false'])
            ->willReturn($data['markerUrl']);

        $this->_helperData->expects($this->once())->method('jsEncode')->with([$data])
            ->willReturn('json_data');

        $this->assertEquals('json_data', $this->object->getDataLocations($locations));
    }

    /**
     * @return array[]
     */
    public function providerDataConfigLocation()
    {
        $operationData = [
            'value'             => '1',
            'from'              => ['08', '00'],
            'to'                => ['17', '30'],
            'use_system_config' => 1
        ];

        $timeData = [
            0 => $operationData,
            1 => $operationData,
            2 => $operationData,
            3 => $operationData,
            4 => $operationData,
            5 => $operationData,
            6 => $operationData,
        ];

        $holidayData = [
            1 => [
                'from' => '2020-05-27 07:00:00',
                'to'   => '2020-05-27 07:00:00',
            ]
        ];

        $locationsData = [
            'name'        => 'Location',
            'countryId'   => 'Country',
            'regionId'    => '0',
            'region'      => 'State Province',
            'street'      => '123',
            'telephone'   => '123',
            'postcode'    => '123',
            'city'        => 'City',
            'timeData'    => $timeData,
            'holidayData' => $holidayData
        ];

        return [
            [
                null,
                [
                    'zoom'                      => '1',
                    'markerIcon'                => 'markerIconUrl',
                    'dataLocations'             => 'dataLocationsUrl',
                    'infowindowTemplatePath'    => 'infowindow-description-path',
                    'listTemplatePath'          => 'location-list-description-path',
                    'KMLinfowindowTemplatePath' => 'kml-infowindow-description-path',
                    'KMLlistTemplatePath'       => 'kml-location-list-description-path',
                    'isFilter'                  => true,
                    'isFilterRadius'            => true,
                    'locationIdDetail'          => '1',
                    'urlSuffix'                 => 'html',
                    'keyMap'                    => '123',
                    'router'                    => 'find-a-store',
                    'isDefaultStore'            => false,
                    'defaultLat'                => null,
                    'defaultLng'                => null,
                    'locationsData'             => [1 => $locationsData],
                ]
            ],
            [
                1,
                [
                    'zoom'                      => '1',
                    'markerIcon'                => 'markerIconUrl',
                    'dataLocations'             => 'dataLocationsUrl',
                    'infowindowTemplatePath'    => 'infowindow-description-path',
                    'listTemplatePath'          => 'location-list-description-path',
                    'KMLinfowindowTemplatePath' => 'kml-infowindow-description-path',
                    'KMLlistTemplatePath'       => 'kml-location-list-description-path',
                    'isFilter'                  => true,
                    'isFilterRadius'            => true,
                    'locationIdDetail'          => '1',
                    'urlSuffix'                 => 'html',
                    'keyMap'                    => '123',
                    'router'                    => 'find-a-store',
                    'isDefaultStore'            => true,
                    'defaultLat'                => 'default-lat',
                    'defaultLng'                => 'default-lng',
                    'locationsData'             => [1 => $locationsData],
                ]
            ]
        ];
    }

    /**
     * @param $defaultLocationId
     * @param $data
     *
     * @throws NoSuchEntityException
     * @dataProvider providerDataConfigLocation
     */
    public function testGetDataConfigLocation($defaultLocationId, $data)
    {
        $location = $this->getMockBuilder(Location::class)
            ->disableOriginalConstructor()->getMock();
        $this->_helperData->expects($this->exactly(2))->method('getDefaultStoreLocation')
            ->willReturn($location);

        $location->expects($this->once())->method('getLatitude')->willReturn($data['defaultLat']);
        $location->expects($this->once())->method('getLongitude')->willReturn($data['defaultLng']);

        $this->_helperData->expects($this->exactly(3))->method('getMapSetting')
            ->withConsecutive(
                ['zoom_default'],
                ['marker_icon'],
                ['api_key']
            )
            ->willReturnOnConsecutiveCalls(
                $data['zoom'],
                0,
                $data['keyMap']
            );

        $this->request->method('isSecure')->willReturn('false');
        $this->assetRepoMock->expects($this->exactly(5))
            ->method('getUrlWithParams')
            ->withConsecutive(
                ['Mageplaza_StoreLocator::media/marker.png', ['_secure' => 'false']],
                ['Mageplaza_StoreLocator::templates/infowindow-description.html', ['_secure' => 'false']],
                ['Mageplaza_StoreLocator::templates/location-list-description.html', ['_secure' => 'false']],
                ['Mageplaza_StoreLocator::templates/kml-infowindow-description.html', ['_secure' => 'false']],
                ['Mageplaza_StoreLocator::templates/kml-location-list-description.html', ['_secure' => 'false']]
            )
            ->willReturn(
                $data['markerIcon'],
                $data['infowindowTemplatePath'],
                $data['listTemplatePath'],
                $data['KMLinfowindowTemplatePath'],
                $data['KMLlistTemplatePath']
            );

        $this->request->expects($this->once())->method('getParam')->with('isPickup')->willReturn(1);
        $this->urlBuilderMock->expects($this->once())->method('getUrl')
            ->withConsecutive(['mpstorelocator/storelocator/pickuplocationsdata'])
            ->willReturnOnConsecutiveCalls($data['dataLocations']);

        $this->_helperData->expects($this->exactly(2))->method('getConfigGeneral')
            ->withConsecutive(
                ['filter_store/enabled'],
                ['filter_store/current_position']
            )
            ->willReturnOnConsecutiveCalls('1', '1');

        $this->_helperData->expects($this->once())->method('getLocationIdFromRouter')
            ->willReturn($data['locationIdDetail']);
        $this->_helperData->expects($this->once())->method('getUrlSuffix')->willReturn($data['urlSuffix']);
        $this->_helperData->expects($this->once())->method('getRoute')->willReturn($data['router']);
        $locationCollection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()->getMock();
        $this->_locationColFactory->method('create')->willReturn($locationCollection);

        $locationCollection->expects($this->once())->method('addFieldToFilter')
            ->with('status', 1)
            ->willReturnSelf();

        $store = $this->getMockBuilder(StoreInterface::class)
            ->getMockForAbstractClass();

        $this->_storeManager->expects($this->once())->method('getStore')->willReturn($store);

        $storeId = '1';
        $store->expects($this->once())->method('getId')->willReturn($storeId);

        $locationCollection->expects($this->once())->method('getItems')->willReturn([$location]);

        $location->method('getStoreIds')->willReturn('1,2,3');

        $locationId    = 1;
        $locationsData = $data['locationsData'][1];

        $location->expects($this->exactly(3))->method('getId')
            ->willReturnOnConsecutiveCalls($defaultLocationId, $locationId, $locationId);
        $location->method('getName')->willReturn('Location');
        $this->escaperMock->expects($this->once())->method('escapeHtml')
            ->with('Location')->willReturn($locationsData['name']);
        $location->method('getCountry')->willReturn($locationsData['countryId']);
        $location->method('getStateProvince')->willReturn($locationsData['region']);
        $location->method('getStreet')->willReturn($locationsData['street']);
        $location->method('getPhoneOne')->willReturn($locationsData['telephone']);
        $location->method('getPostalCode')->willReturn($locationsData['postcode']);
        $location->method('getCity')->willReturn($locationsData['city']);

        $operation = '{"value":"1","from":["08","00"],"to":["17","30"],"use_system_config":1}';

        $location->method('getOperationSun')->willReturn($operation);
        $location->method('getOperationMon')->willReturn($operation);
        $location->method('getOperationTue')->willReturn($operation);
        $location->method('getOperationWed')->willReturn($operation);
        $location->method('getOperationThu')->willReturn($operation);
        $location->method('getOperationFri')->willReturn($operation);
        $location->method('getOperationSat')->willReturn($operation);
        $this->_helperData
            ->method('jsDecode')
            ->with($operation)
            ->willReturn($locationsData['timeData'][0]);

        $holidayIds = [1];

        $this->_locationResource->expects($this->once())
            ->method('getHolidayIdsByLocation')
            ->with($locationId)->willReturn($holidayIds);

        $holidayMethods = array_merge(
            get_class_methods(Holiday::class),
            [
                'getStatus',
                'getFrom',
                'getTo'
            ]
        );
        $holiday        = $this->getMockBuilder(Holiday::class)
            ->setMethods($holidayMethods)
            ->disableOriginalConstructor()->getMock();
        $this->holidayFactory->method('create')->willReturn($holiday);

        $this->_holidayResource->method('load')->with($holiday, 1)->willReturn($holiday);

        $holidayData = $locationsData['holidayData'][1];

        $holiday->method('getStatus')->willReturn(1);
        $holiday->method('getFrom')->willReturn($holidayData['from']);
        $holiday->method('getTo')->willReturn($holidayData['from']);

        $this->_helperData->expects($this->once())->method('jsEncode')
            ->with($data)->willReturn('encoded_data');

        $this->object->getDataConfigLocation();
    }

    public function testGetHolidayData()
    {
        $locationId = 1;
        $location   = $this->getMockBuilder(Location::class)
            ->disableOriginalConstructor()->getMock();
        $location->expects($this->once())->method('getId')->willReturn($locationId);

        $holidayIds = [1];

        $this->_locationResource->expects($this->once())
            ->method('getHolidayIdsByLocation')
            ->with($locationId)->willReturn($holidayIds);

        $holidayMethods = array_merge(
            get_class_methods(Holiday::class),
            [
                'getStatus',
                'getFrom',
                'getTo'
            ]
        );
        $holiday        = $this->getMockBuilder(Holiday::class)
            ->setMethods($holidayMethods)
            ->disableOriginalConstructor()->getMock();
        $this->holidayFactory->method('create')->willReturn($holiday);

        $this->_holidayResource->method('load')->with($holiday, 1)->willReturn($holiday);

        $from = '2020-05-27 07:00:00';
        $to   = '2020-05-27 07:00:00';
        $holiday->method('getStatus')->willReturn(1);
        $holiday->method('getFrom')->willReturn($from);
        $holiday->method('getTo')->willReturn($to);

        $holidayData = [
            1 => [
                'from' => $from,
                'to'   => $to,
            ]
        ];

        $this->assertEquals($holidayData, $this->object->getHolidayData($location));
    }

    public function testGetTimeData()
    {
        $location  = $this->getMockBuilder(Location::class)
            ->disableOriginalConstructor()->getMock();
        $operation = '{"value":"1","from":["08","00"],"to":["17","30"],"use_system_config":1}';
        $location->method('getOperationSun')->willReturn($operation);
        $location->method('getOperationMon')->willReturn($operation);
        $location->method('getOperationTue')->willReturn($operation);
        $location->method('getOperationWed')->willReturn($operation);
        $location->method('getOperationThu')->willReturn($operation);
        $location->method('getOperationFri')->willReturn($operation);
        $location->method('getOperationSat')->willReturn($operation);
        $operationData = [
            'value'             => '1',
            'from'              => ['08', '00'],
            'to'                => ['17', '30'],
            'use_system_config' => 1
        ];
        $this->_helperData
            ->method('jsDecode')
            ->with($operation)
            ->willReturn($operationData);

        $timeData = [
            0 => $operationData,
            1 => $operationData,
            2 => $operationData,
            3 => $operationData,
            4 => $operationData,
            5 => $operationData,
            6 => $operationData,
        ];

        $this->assertEquals($timeData, $this->object->getTimeData($location));
    }

    /**
     * @return array[]
     */
    public function providerLocationsDataUrl()
    {
        return [
            [1, 'mpstorelocator/storelocator/pickuplocationsdata'],
            [0, 'mpstorelocator/storelocator/locationsdata'],
        ];
    }

    /**
     * @param $isPickup
     * @param $url
     *
     * @dataProvider providerLocationsDataUrl
     */
    public function testGetLocationsDataUrl($isPickup, $url)
    {
        $this->request->expects($this->once())->method('getParam')->with('isPickup')->willReturn($isPickup);
        $this->urlBuilderMock->expects($this->once())->method('getUrl')
            ->with($url)
            ->willReturn('location-url');

        $this->object->getLocationsDataUrl();
    }

    /**
     * @return array[]
     */
    public function providerCheckIsDefaultStore()
    {
        return [
            [1, true],
            [null, false]
        ];
    }

    /**
     * @param $locationId
     * @param $result
     *
     * @dataProvider providerCheckIsDefaultStore
     */
    public function testCheckIsDefaultStore($locationId, $result)
    {
        $location = $this->getMockBuilder(Location::class)
            ->disableOriginalConstructor()->getMock();
        $this->_helperData->expects($this->once())->method('getDefaultStoreLocation')->willReturn($location);
        $location->expects($this->once())->method('getId')->willReturn($locationId);

        $this->assertEquals($result, $this->object->checkIsDefaultStore());
    }

    /**
     * @return string[][]
     */
    public function providerMakerIconUrl()
    {
        return [
            ['', 'marker-url'],
            ['/m/a/marker-icon', 'base-url/marker-icon-path'],
        ];
    }

    /**
     * @param $markerIcon
     * @param $result
     *
     * @dataProvider providerMakerIconUrl
     *
     * @throws NoSuchEntityException
     */
    public function testGetMakerIconUrl($markerIcon, $result)
    {
        $this->_helperData->expects($this->once())->method('getMapSetting')
            ->with('marker_icon')->willReturn($markerIcon);
        if ($markerIcon) {
            $this->_helperImage->expects($this->once())->method('getBaseMediaUrl')
                ->willReturn('base-url');
            $this->_helperImage->expects($this->once())->method('getMediaPath')
                ->with($markerIcon, 'marker_icon')->willReturn('marker-icon-path');
        } else {
            $fileId = 'Mageplaza_StoreLocator::media/marker.png';
            $this->request->expects($this->once())->method('isSecure')->willReturn('false');
            $this->assetRepoMock->expects($this->once())
                ->method('getUrlWithParams')
                ->with($fileId, ['_secure' => 'false'])
                ->willReturn($result);
        }

        $this->assertEquals($result, $this->object->getMakerIconUrl());
    }

    /**
     * @return string[][]
     */
    public function providerDefaultImgUrl()
    {
        return [
            ['', 'default-image-url'],
            ['/m/a/marker-icon', 'base-url/default-image-path'],
        ];
    }

    /**
     * @param $defaultImage
     * @param $result
     *
     * @dataProvider providerDefaultImgUrl
     *
     * @throws NoSuchEntityException
     */
    public function testGetDefaultImgUrl($defaultImage, $result)
    {
        $this->_helperData->expects($this->once())->method('getConfigGeneral')
            ->with('upload_default_image')->willReturn($defaultImage);

        if ($defaultImage) {
            $this->_helperImage->expects($this->once())->method('getBaseMediaUrl')
                ->willReturn('base-url');
            $this->_helperImage->expects($this->once())->method('getMediaPath')
                ->with($defaultImage, 'image')->willReturn('default-image-path');
        } else {
            $fileId = 'Mageplaza_StoreLocator::media/defaultImg.png';
            $this->request->expects($this->once())->method('isSecure')->willReturn('false');
            $this->assetRepoMock->expects($this->once())
                ->method('getUrlWithParams')
                ->with($fileId, ['_secure' => 'false'])
                ->willReturn($result);
        }

        $this->assertEquals($result, $this->object->getDefaultImgUrl());
    }

    /**
     * @return array[]
     */
    public function providerDayTime()
    {
        return [
            [
                [
                    'value'             => '1',
                    'from'              => ['08', '00'],
                    'to'                => ['17', '30'],
                    'use_system_config' => 1
                ],
                '08:00 - 17:30'
            ],
            [
                [
                    'value'             => '0',
                    'from'              => ['08', '00'],
                    'to'                => ['17', '30'],
                    'use_system_config' => 1
                ],
                '<span style="color:red">Closed</span>'
            ]
        ];
    }

    /**
     * @param $storeDay
     * @param $result
     *
     * @dataProvider providerDayTime
     */
    public function testGetDayTime($storeDay, $result)
    {
        $this->_helperData->expects($this->once())->method('jsDecode')->with('json-string')
            ->willReturn($storeDay);

        $this->assertEquals($result, $this->object->getDayTime('json-string'));
    }

    /**
     * @return string[][]
     */
    public function providerCurrentDay()
    {
        return [
            ['2020-12-06 23:00:00', 'UTC', 'sunday'],
            ['2020-12-06 23:00:00', 'Asia/Qatar', 'monday']
        ];
    }

    /**
     * @param $date
     * @param $timeZone
     * @param $result
     *
     * @dataProvider providerCurrentDay
     *
     * @throws Exception
     */
    public function testGetCurrentDay($date, $timeZone, $result)
    {
        $location = $this->getMockBuilder(Location::class)
            ->disableOriginalConstructor()->getMock();
        $this->_dateTime->expects($this->once())->method('date')->willReturn($date);
        $location->expects($this->once())->method('getTimeZone')->willReturn($timeZone);

        $this->assertEquals($result, $this->object->getCurrentDay($location));
    }

    public function testGetMapTemplateWithDefaultStyle()
    {
        $this->_helperData->expects($this->once())->method('getMapSetting')->with('style')
            ->willReturn(MapStyle::STYLE_DEFAULT);

        $this->assertEquals('[]', $this->object->getMapTemplate());
    }

    public function testGetMapTemplateWithCustomStyle()
    {
        $this->_helperData->expects($this->exactly(2))->method('getMapSetting')
            ->withConsecutive(['style'], ['custom_style'])
            ->willReturn(MapStyle::STYLE_CUSTOM, 'custom_style_json_data');

        $this->assertEquals('custom_style_json_data', $this->object->getMapTemplate());
    }

    public function testGetMapTemplateWithAnotherStyleExceptCustomAndDefault()
    {
        $this->_helperData->expects($this->once())->method('getMapSetting')->with('style')
            ->willReturn(MapStyle::STYLE_BLUE_ESSENCE);
        $this->_helperData->expects($this->once())->method('getMapTheme')->with(MapStyle::STYLE_BLUE_ESSENCE)
            ->willReturn('blue_essence_json_data');

        $this->assertEquals('blue_essence_json_data', $this->object->getMapTemplate());
    }

    /**
     * @return array[]
     */
    public function providerIsEnableFilterRadius()
    {
        return [
            ['1', true],
            ['0', false],
            ['', false],
            [null, false],
            [1, false],
        ];
    }

    /**
     * @param $currentPosition
     * @param $result
     *
     * @dataProvider providerIsEnableFilterRadius
     */
    public function testIsEnableFilterRadius($currentPosition, $result)
    {
        $this->_helperData->expects($this->once())->method('getConfigGeneral')->with('filter_store/current_position')
            ->willReturn($currentPosition);

        $this->assertEquals($result, $this->object->isEnableFilterRadius());
    }

    /**
     * @param $enabled
     * @param $result
     *
     * @dataProvider providerIsEnableFilterRadius
     */
    public function testIsFilter($enabled, $result)
    {
        $this->_helperData->expects($this->once())->method('getConfigGeneral')->with('filter_store/enabled')
            ->willReturn($enabled);

        $this->assertEquals($result, $this->object->isFilter());
    }

    public function testGetAvailableProduct()
    {
        $this->_helperData->expects($this->once())->method('getAvailableProduct')
            ->willReturn('1');

        $this->object->getAvailableProduct();
    }

    public function testGetPickupConfig()
    {
        $this->_helperData->expects($this->once())->method('getPickupConfig')
            ->with('title')
            ->willReturn('Find a store');

        $this->object->getPickupConfig();
    }

    public function testGetApiKey()
    {
        $this->_helperData->expects($this->once())->method('getMapSetting')
            ->with('api_key')
            ->willReturn('123');

        $this->object->getApiKey();
    }

    public function testGetHelper()
    {
        $this->assertEquals($this->_helperData, $this->object->getHelper());
    }

    public function testGetPickupLocationList()
    {
        $productId = 1;

        $location = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->_locationColFactory->method('create')->willReturn($location);
        $location->method('addFieldToFilter')
            ->willReturnSelf();

        $item     = $this->getMockBuilder(Item::class)
            ->setMethods(['getProduct'])
            ->disableOriginalConstructor()
            ->getMock();
        $allItems = [$item];

        $quote = $this->getMockBuilder(Quote::class)
            ->setMethods(['getAllItems'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->_cart->method('getQuote')->willReturn($quote);
        $quote->method('getAllItems')->willReturn($allItems);

        $productItem = $this->getMockBuilder(Product::class)
            ->setMethods(['getId'])
            ->disableOriginalConstructor()
            ->getMock();
        $item->method('getProduct')->willReturn($productItem);
        $productItem->method('getId')->willReturn($productId);

        $product = $this->getMockBuilder(AbstractProduct::class)
            ->setMethods(['getCustomAttribute'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->productRepository->method('getById')->with($productId)->willReturn($product);
        $customAttribute = $this->getMockBuilder(AttributeInterface::class)
            ->setMethods(['getValue'])
            ->getMockForAbstractClass();
        $product->method('getCustomAttribute')->with('mp_pickup_locations')->willReturn($customAttribute);

        $value = 1;
        $customAttribute->method('getValue')->willReturn($value);

        $location->method('addFieldToFilter')
            ->willReturnSelf();

        $storeId = 0;
        $store   = $this->getMockBuilder(StoreManagerInterface::class)
            ->setMethods(['getId'])
            ->getMockForAbstractClass();
        $this->_storeManager->method('getStore')->willReturn($store);
        $store->method('getId')->willReturn($storeId);

        /** \Mageplaza\StoreLocator\Model\Location */
        $loc = $this->getMockBuilder(Location::class)
            ->setMethods(['getStoreIds'])
            ->disableOriginalConstructor()
            ->getMock();

        $location->method('getItems')->willReturn([$loc]);

        $loc->method('getStoreIds')->willReturn(0);
        $result = [$loc];

        $this->assertEquals($result, $this->object->getPickupLocationList());
    }

    protected function setUp()
    {
        $this->context             = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()->getMock();
        $this->_dateTime           = $this->getMockBuilder(DateTime::class)
            ->disableOriginalConstructor()->getMock();
        $this->_helperData         = $this->getMockBuilder(HelperData::class)
            ->disableOriginalConstructor()->getMock();
        $this->locationFactory     = $this->getMockBuilder(LocationFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->holidayFactory      = $this->getMockBuilder(HolidayFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->_helperImage        = $this->getMockBuilder(HelperImage::class)
            ->disableOriginalConstructor()->getMock();
        $this->_blockFactory       = $this->getMockBuilder(BlockFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->_cart               = $this->getMockBuilder(Cart::class)
            ->disableOriginalConstructor()->getMock();
        $this->productRepository   = $this->getMockForAbstractClass(ProductRepositoryInterface::class);
        $this->_locationColFactory = $this->getMockBuilder(CollectionFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->_locationResource   = $this->getMockBuilder(LocationResource::class)
            ->disableOriginalConstructor()->getMock();
        $this->_holidayResource    = $this->getMockBuilder(HolidayResource::class)
            ->disableOriginalConstructor()->getMock();
        $this->messageManager      = $this->getMockForAbstractClass(ManagerInterface::class);
        $this->_storeManager       = $this->getMockForAbstractClass(StoreManagerInterface::class);
        $this->urlBuilderMock      = $this->getMockForAbstractClass(UrlInterface::class);
        $this->assetRepoMock       = $this->getMockBuilder(Repository::class)
            ->disableOriginalConstructor()->getMock();

        $requestMethod     = array_merge(get_class_methods(RequestInterface::class), ['getFullActionName']);
        $this->request     = $this->getMockForAbstractClass(
            RequestInterface::class,
            [],
            '',
            true,
            true,
            true,
            $requestMethod
        );
        $this->pageConfig  = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
        $this->escaperMock = $this->getMockBuilder(Escaper::class)
            ->disableOriginalConstructor()->getMock();

        $this->context->method('getStoreManager')->willReturn($this->_storeManager);
        $this->context->method('getPageConfig')->willReturn($this->pageConfig);
        $this->context->method('getRequest')->willReturn($this->request);
        $this->context->method('getAssetRepository')->willReturn($this->assetRepoMock);
        $this->context->method('getUrlBuilder')->willReturn($this->urlBuilderMock);
        $this->context->method('getEscaper')->willReturn($this->escaperMock);

        $this->object = new Frontend(
            $this->context,
            $this->_dateTime,
            $this->_helperData,
            $this->locationFactory,
            $this->holidayFactory,
            $this->_helperImage,
            $this->_blockFactory,
            $this->_cart,
            $this->productRepository,
            $this->_locationColFactory,
            $this->_locationResource,
            $this->_holidayResource,
            $this->messageManager,
            [
                'default_image_url' => 'default_image_url'
            ]
        );
    }
}
