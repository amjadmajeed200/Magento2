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

namespace Mageplaza\StoreLocator\Test\Unit\Model;

use DateTime;
use Magento\Checkout\Model\Session;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\Search\SearchCriteria;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\App\Area;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Url;
use Magento\Framework\View\Asset\Repository;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\StoreLocator\Api\Data\LocationInterface;
use Mageplaza\StoreLocator\Block\Frontend;
use Mageplaza\StoreLocator\Helper\Data;
use Mageplaza\StoreLocator\Helper\Image as HelperImage;
use Mageplaza\StoreLocator\Model\Api\Data\DataConfigLocation;
use Mageplaza\StoreLocator\Model\Api\Data\LocationData;
use Mageplaza\StoreLocator\Model\Api\Data\PickupConfig;
use Mageplaza\StoreLocator\Model\Config\Source\System\MapStyle;
use Mageplaza\StoreLocator\Model\Location;
use Mageplaza\StoreLocator\Model\LocationsRepository;
use Mageplaza\StoreLocator\Model\ResourceModel\Location\Collection;
use Mageplaza\StoreLocator\Model\ResourceModel\Location\CollectionFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

/**
 * Class LocationsRepositoryTest
 * @package Mageplaza\StoreLocator\Test\Unit\Model
 */
class LocationsRepositoryTest extends TestCase
{
    /**
     * @var Location
     */
    private $object;

    /**
     * @var CollectionFactory|MockObject
     */
    private $collectionFactoryMock;

    /**
     * @var CollectionProcessorInterface|MockObject
     */
    private $collectionProcessorMock;

    /**
     * @var SearchResultsInterfaceFactory|MockObject
     */
    private $searchResultsFactoryMock;

    /**
     * @var SearchCriteriaBuilder|MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * @var StoreManagerInterface|MockObject
     */
    private $storeManagerMock;

    /**
     * @var Data|MockObject
     */
    private $helperDataMock;

    /**
     * @var HelperImage|MockObject
     */
    private $helperImageMock;

    /**
     * @var Repository|MockObject
     */
    private $assetRepoMock;

    /**
     * @var Url|MockObject
     */
    private $frontendUrlMock;

    /**
     * @var RequestInterface|MockObject
     */
    private $requestMock;

    /**
     * @var Escaper|MockObject
     */
    private $escaperMock;

    /**
     * @var Session|MockObject
     */
    private $checkoutSessionMock;

    /**
     * @var TimezoneInterface|MockObject
     */
    private $timezoneMock;

    /**
     * @var Frontend|MockObject
     */
    private $frontendMock;

    /**
     * @var FilterBuilder|MockObject
     */
    private $filterBuilderMock;

    /**
     * @var FilterGroupBuilder|MockObject
     */
    private $filterGroupBuilderMock;

    public function testGetListWithModuleNotEnable()
    {
        $this->helperDataMock->expects($this->once())->method('isEnabled')->willReturn(false);

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('The module is disabled');

        $this->object->getList();
    }

    public function testGetList()
    {
        $searchResult = $this->getList();

        $this->assertEquals($searchResult, $this->object->getList());
    }

    /**
     * @return MockObject
     */
    public function getList()
    {
        $this->helperDataMock->expects($this->once())->method('isEnabled')->willReturn(true);
        $searchCriteria = $this->getMockBuilder(SearchCriteria::class)
            ->disableOriginalConstructor()->getMock();
        $this->searchCriteriaBuilderMock->expects($this->once())->method('create')->willReturn($searchCriteria);
        $collection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()->getMock();
        $this->collectionFactoryMock->expects($this->once())->method('create')->willReturn($collection);

        $this->collectionProcessorMock->expects($this->once())->method('process')
            ->with($searchCriteria, $collection)->willReturnSelf();
        $item = $this->getMockBuilder(Location::class)
            ->disableOriginalConstructor()->getMock();
        $collection->expects($this->exactly(2))->method('getItems')->willReturn([$item]);

        $item->expects($this->once())->method('getCreatedAt')->willReturn('2020-06-12 00:00:00');
        $item->expects($this->once())->method('getUpdatedAt')->willReturn('2020-06-12 00:00:00');
        $date = $this->getMockBuilder(DateTime::class)->disableOriginalConstructor()->getMock();
        $this->timezoneMock->expects($this->exactly(2))->method('date')
            ->with('2020-06-12 00:00:00')->willReturn($date);
        $date->expects($this->exactly(2))->method('format')
            ->with('Y-m-d H:i:s')->willReturn('2020-06-12 00:00:00');
        $item->expects($this->once())->method('setCreatedAt')->with('2020-06-12 00:00:00')->willReturnSelf();
        $item->expects($this->once())->method('setUpdatedAt')->with('2020-06-12 00:00:00')->willReturnSelf();

        $item->expects($this->once())->method('getImages')->willReturn('images_data_json_string');
        $this->helperDataMock->expects($this->once())->method('jsDecode')->with('images_data_json_string')
            ->willReturn([['file' => '/i/m/image.png']]);
        $this->helperImageMock->expects($this->once())->method('getMediaUrl')
            ->with(HelperImage::TEMPLATE_MEDIA_PATH . '/i/m/image.png')->willReturn('image_url');
        $this->helperDataMock->expects($this->once())->method('jsEncode')->with([['file' => 'image_url']])
            ->willReturn('images_data_json_string');
        $item->expects($this->once())->method('setImages')
            ->with('images_data_json_string')
            ->willReturnSelf();

        $searchResult = $this->getMockForAbstractClass(SearchResultsInterface::class);
        $this->searchResultsFactoryMock->expects($this->once())->method('create')->willReturn($searchResult);
        $searchResult->expects($this->once())->method('setSearchCriteria')->with($searchCriteria)->willReturnSelf();
        $searchResult->expects($this->once())->method('setItems')->with([$item])->willReturnSelf();
        $collection->expects($this->once())->method('getSize')->willReturn(1);
        $searchResult->expects($this->once())->method('setTotalCount')->with(1)->willReturnSelf();

        return $searchResult;
    }

    /**
     * @throws LocalizedException
     */
    public function testGetLocations()
    {
        $this->requestMock->expects($this->once())->method('getParam')->with('storeId')->willReturn('1');

        $searchCriteria = $this->getMockBuilder(SearchCriteria::class)->disableOriginalConstructor()->getMock();
        $this->searchCriteriaBuilderMock->expects($this->once())->method('create')->willReturn($searchCriteria);
        $this->filterBuilderMock->expects($this->exactly(2))->method('setField')
            ->with('store_ids')->willReturnSelf();
        $this->filterBuilderMock->expects($this->exactly(2))->method('setValue')
            ->withConsecutive(['1'], ['0'])->willReturnSelf();
        $this->filterBuilderMock->expects($this->exactly(2))->method('setConditionType')
            ->with('fin')->willReturnSelf();
        $searchTermFilter = $this->getMockBuilder(Filter::class)->disableOriginalConstructor()->getMock();

        $this->filterBuilderMock->expects($this->exactly(2))->method('create')->willReturn($searchTermFilter);
        $this->filterGroupBuilderMock->expects($this->exactly(2))->method('addFilter')
            ->with($searchTermFilter)->willReturnSelf();
        $filterGroup = $this->getMockBuilder(FilterGroup::class)->disableOriginalConstructor()->getMock();
        $searchCriteria->expects($this->once())->method('getFilterGroups')->willReturn([]);
        $this->filterGroupBuilderMock->expects($this->once())->method('create')->willReturn($filterGroup);
        $searchCriteria->expects($this->once())->method('setFilterGroups')
            ->with([$filterGroup])->willReturnSelf();

        $searchResult = $this->getList();

        $this->assertEquals($searchResult, $this->object->getLocations());
    }

    public function testGetLocationsData()
    {
        $storeId = 1;
        $data    = $this->getLocationsData($storeId);

        $this->assertEquals($data, $this->object->getLocationsData($storeId));
    }

    /**
     * @param $storeId
     *
     * @return array
     */
    public function getLocationsData($storeId)
    {
        $locationCollection = $this->getMockBuilder(Collection::class)->disableOriginalConstructor()->getMock();
        $this->collectionFactoryMock->expects($this->once())->method('create')->willReturn($locationCollection);

        $locationCollection->expects($this->exactly(2))->method('addFieldToFilter')
            ->withConsecutive(
                [['store_ids', 'store_ids'], [['finset' => $storeId], ['finset' => '0']]],
                ['status', 1]
            )->willReturnSelf();
        $location = $this->getMockBuilder(Location::class)->disableOriginalConstructor()->getMock();
        $locationCollection->expects($this->once())->method('getItems')->willReturn([$location]);
        $location->expects($this->once())->method('getId')->willReturn(1);
        $location->expects($this->once())->method('getName')->willReturn('name');
        $this->escaperMock->expects($this->once())->method('escapeHtml')->with('name')->willReturn('name');
        $location->expects($this->once())->method('getCountry')->willReturn('countryId');
        $location->expects($this->once())->method('getStateProvince')->willReturn('region');
        $location->expects($this->once())->method('getStreet')->willReturn('street');
        $location->expects($this->once())->method('getPhoneOne')->willReturn('telephone');
        $location->expects($this->once())->method('getPostalCode')->willReturn('postcode');
        $location->expects($this->once())->method('getCity')->willReturn('city');

        $this->frontendMock->expects($this->once())->method('getTimeData')
            ->with($location)->willReturn(['timeData']);
        $this->frontendMock->expects($this->once())->method('getHolidayData')
            ->with($location)->willReturn(['holidayData']);
        $data    = [];
        $data[1] = new  LocationData([
            'name'        => 'name',
            'locationId'  => 1,
            'countryId'   => 'countryId',
            'regionId'    => '0',
            'region'      => 'region',
            'street'      => 'street',
            'telephone'   => 'telephone',
            'postcode'    => 'postcode',
            'city'        => 'city',
            'timeData'    => ['timeData'],
            'holidayData' => ['holidayData']
        ]);

        return $data;
    }

    public function testGetDataConfigLocation()
    {
        $storeId      = 1;
        $storeCode    = 'na';
        $defaultStore = $this->getMockBuilder(Location::class)->disableOriginalConstructor()->getMock();
        $this->helperDataMock->expects($this->once())->method('getDefaultStoreLocation')->willReturn($defaultStore);
        $defaultStore->expects($this->once())->method('getLatitude')->willReturn('default_latitude');
        $defaultStore->expects($this->once())->method('getLongitude')->willReturn('default_longitude');
        $this->requestMock->expects($this->exactly(3))->method('getParam')
            ->withConsecutive(['storeId'], ['isPickup'], ['locationName'])
            ->willReturn($storeId, false, 'home');
        $this->helperDataMock->expects($this->exactly(3))->method('getMapSetting')
            ->withConsecutive(
                ['zoom_default', $storeId],
                ['marker_icon', $storeId],
                ['api_key', $storeId]
            )
            ->willReturnOnConsecutiveCalls('zoom_default', 'marker_icon.png', 'api_key');
        $this->helperImageMock->expects($this->once())->method('getBaseMediaUrl')->willReturn('base_media_url');
        $this->helperImageMock->expects($this->once())->method('getMediaPath')
            ->with('marker_icon.png', 'marker_icon')
            ->willReturn('mageplaza/store-locator/marker_icon.png');
        $store = $this->getMockForAbstractClass(StoreInterface::class);
        $this->storeManagerMock->method('getStore')->with($storeId)->willReturn($store);
        $store->method('getCode')->willReturn($storeCode);
        $query = ['_query' => ['___store' => $storeCode], '_nosid' => true];
        $this->frontendUrlMock->expects($this->once())->method('getUrl')
            ->with('mpstorelocator/storelocator/locationsdata', $query)
            ->willReturn('dataLocationsUrl');
        $this->assetRepoMock->expects($this->exactly(4))->method('getUrlWithParams')
            ->withConsecutive(
                [
                    'Mageplaza_StoreLocator::templates/infowindow-description.html',
                    ['area' => Area::AREA_FRONTEND]
                ],
                [
                    'Mageplaza_StoreLocator::templates/location-list-description.html',
                    ['area' => Area::AREA_FRONTEND]
                ],
                [
                    'Mageplaza_StoreLocator::templates/kml-infowindow-description.html',
                    ['area' => Area::AREA_FRONTEND]
                ],
                [
                    'Mageplaza_StoreLocator::templates/kml-location-list-description.html',
                    ['area' => Area::AREA_FRONTEND]
                ]
            )
            ->willReturnOnConsecutiveCalls(
                'infowindowTemplatePath',
                'listTemplatePath',
                'KMLinfowindowTemplatePath',
                'KMLlistTemplatePath'
            );

        $this->helperDataMock->expects($this->exactly(3))->method('getConfigGeneral')
            ->withConsecutive(
                ['filter_store/enabled', $storeId],
                ['filter_store/current_position', $storeId],
                ['url_key', $storeId]
            )
            ->willReturnOnConsecutiveCalls('1', '1', 'find-a-store');
        $defaultStore->expects($this->exactly(2))->method('getId')->willReturn(1);
        $this->helperDataMock->expects($this->once())->method('getLocationByUrl')
            ->with('home')->willReturn($defaultStore);
        $this->helperDataMock->expects($this->once())->method('getUrlSuffix')->with($storeId)->willReturn('.html');
        $data = $this->getLocationsData($storeId);

        $dataConfig = new DataConfigLocation(
            [
                'zoom'                      => 'zoom_default',
                'markerIcon'                => 'base_media_url/mageplaza/store-locator/marker_icon.png',
                'dataLocations'             => 'dataLocationsUrl',
                'infowindowTemplatePath'    => 'infowindowTemplatePath',
                'listTemplatePath'          => 'listTemplatePath',
                'KMLinfowindowTemplatePath' => 'KMLinfowindowTemplatePath',
                'KMLlistTemplatePath'       => 'KMLlistTemplatePath',
                'isFilter'                  => true,
                'isFilterRadius'            => true,
                'locationIdDetail'          => 1,
                'urlSuffix'                 => '.html',
                'keyMap'                    => 'api_key',
                'router'                    => 'find-a-store',
                'isDefaultStore'            => true,
                'defaultLat'                => 'default_latitude',
                'defaultLng'                => 'default_longitude',
                'locationsData'             => $data,
            ]
        );

        $this->assertEquals($dataConfig, $this->object->getDataConfigLocation());
    }

    /**
     * @return string[][]
     */
    public function providerMakerIconUrl()
    {
        return [
            ['marker_icon', 'base-media-url/mageplaza/store-locator/marker-icon.png'],
            ['', 'default-marker-icon-url'],
        ];
    }

    /**
     * @param $markerIcon
     * @param $result
     *
     * @dataProvider providerMakerIconUrl
     *
     * @throws ReflectionException
     */
    public function testGetMakerIconUrl($markerIcon, $result)
    {
        $storeId    = 1;
        $additional = new ReflectionClass(LocationsRepository::class);
        $method     = $additional->getMethod('getMakerIconUrl');
        $method->setAccessible(true);

        $this->helperDataMock->expects($this->once())->method('getMapSetting')
            ->with('marker_icon', $storeId)
            ->willReturn($markerIcon);

        if ($markerIcon) {
            $this->helperImageMock->expects($this->once())->method('getBaseMediaUrl')->willReturn('base-media-url');
            $this->helperImageMock->expects($this->once())->method('getMediaPath')
                ->with('marker_icon', 'marker_icon')
                ->willReturn('mageplaza/store-locator/marker-icon.png');
        } else {
            $this->assetRepoMock->expects($this->once())->method('getUrlWithParams')
                ->with(
                    'Mageplaza_StoreLocator::media/marker.png',
                    ['area' => Area::AREA_FRONTEND]
                )->willReturn($result);
        }

        $this->assertEquals($result, $method->invokeArgs($this->object, [$storeId]));
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
     * @param $routePath
     *
     * @dataProvider providerLocationsDataUrl
     *
     * @throws NoSuchEntityException
     */
    public function testGetLocationsDataUrl($isPickup, $routePath)
    {
        $storeId   = 1;
        $storeCode = 'na';
        $this->requestMock->expects($this->once())->method('getParam')->with('isPickup')->willReturn($isPickup);
        $store = $this->getMockForAbstractClass(StoreInterface::class);
        $this->storeManagerMock->expects($this->once())->method('getStore')->with($storeId)->willReturn($store);
        $store->expects($this->once())->method('getCode')->willReturn($storeCode);
        $query = ['_query' => ['___store' => $storeCode], '_nosid' => true];
        $this->frontendUrlMock->expects($this->once())->method('getUrl')
            ->with($routePath, $query)
            ->willReturn('dataLocationsUrl');

        $this->object->getLocationsDataUrl($storeId);
    }

    public function testGetMapTemplateWithDefaultStyle()
    {
        $this->helperDataMock->expects($this->once())->method('getMapSetting')->with('style')
            ->willReturn(MapStyle::STYLE_DEFAULT);

        $this->assertEquals('[]', $this->object->getMapTemplate());
    }

    public function testGetMapTemplateWithCustomStyle()
    {
        $this->helperDataMock->expects($this->exactly(2))->method('getMapSetting')
            ->withConsecutive(['style'], ['custom_style'])
            ->willReturn(MapStyle::STYLE_CUSTOM, 'custom_style_json_data');

        $this->assertEquals('custom_style_json_data', $this->object->getMapTemplate());
    }

    public function testGetMapTemplateWithAnotherStyleExceptCustomAndDefault()
    {
        $this->helperDataMock->expects($this->once())->method('getMapSetting')->with('style')
            ->willReturn(MapStyle::STYLE_BLUE_ESSENCE);
        $this->helperDataMock->expects($this->once())->method('getMapTheme')->with(MapStyle::STYLE_BLUE_ESSENCE)
            ->willReturn('blue_essence_json_data');
        $this->helperDataMock->expects($this->once())->method('jsDecode')->with('blue_essence_json_data')
            ->willReturn([]);
        $this->helperDataMock->expects($this->once())->method('jsEncode')->with([])
            ->willReturn('blue_essence_json_data');

        $this->assertEquals('blue_essence_json_data', $this->object->getMapTemplate());
    }

    public function testGetPickupData()
    {
        $storeId   = 1;
        $storeCode = 'na';
        $store     = $this->getMockForAbstractClass(StoreInterface::class);
        $this->storeManagerMock->expects($this->exactly(2))->method('getStore')->with($storeId)->willReturn($store);
        $store->expects($this->exactly(2))->method('getCode')->willReturn($storeCode);
        $query = ['_query' => ['___store' => $storeCode], '_nosid' => true];
        $this->frontendUrlMock->expects($this->exactly(2))->method('getUrl')
            ->withConsecutive(
                ['mpstorelocator/storelocator/store', $query],
                ['mpstorelocator/storepickup/saveLocationData', $query]
            )
            ->willReturn('stores_map_url', 'location_session_url');

        $locationsData = $this->getLocationsData($storeId);
        $this->helperDataMock->expects($this->once())->method('getConfigValue')
            ->with('carriers/mpstorepickup/available_after', $storeId)
            ->willReturn(1);
        $data = new PickupConfig([
            'stores_map_url'       => 'stores_map_url',
            'location_session_url' => 'location_session_url',
            'locationsData'        => $locationsData,
            'pickupAfterDays'      => 1
        ]);

        $this->assertEquals($data, $this->object->getPickupData($storeId));
    }

    public function testSaveLocation()
    {
        /** @var LocationInterface|MockObject $location */
        $location = $this->getMockForAbstractClass(LocationInterface::class);
        $location->expects($this->once())->method('getLocationId')->willReturn(1);
        $this->checkoutSessionMock->expects($this->once())->method('setLocationIdSelected')
            ->with(1)->willReturnSelf();
        $location->expects($this->once())->method('getTimePickup')->willReturn('time_pickup_data');

        $this->checkoutSessionMock->expects($this->once())->method('setPickupTime')
            ->with('time_pickup_data')->willReturnSelf();

        $this->assertTrue($this->object->saveLocation($location));
    }

    public function testGetLocationId()
    {
        $this->checkoutSessionMock->expects($this->once())->method('getLocationIdSelected')->willReturn(1);

        $this->assertEquals(1, $this->object->getLocationId());
    }

    protected function setUp()
    {
        $this->collectionFactoryMock     = $this->getMockBuilder(CollectionFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->collectionProcessorMock   = $this->getMockBuilder(CollectionProcessorInterface::class)
            ->disableOriginalConstructor()->getMock();
        $this->searchResultsFactoryMock  = $this->getMockBuilder(SearchResultsInterfaceFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->searchCriteriaBuilderMock = $this->getMockBuilder(SearchCriteriaBuilder::class)
            ->disableOriginalConstructor()->getMock();
        $this->filterBuilderMock         = $this->getMockBuilder(FilterBuilder::class)
            ->disableOriginalConstructor()->getMock();
        $this->filterGroupBuilderMock    = $this->getMockBuilder(FilterGroupBuilder::class)
            ->disableOriginalConstructor()->getMock();
        $this->storeManagerMock          = $this->getMockForAbstractClass(StoreManagerInterface::class);
        $this->helperDataMock            = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()->getMock();
        $this->helperImageMock           = $this->getMockBuilder(HelperImage::class)
            ->disableOriginalConstructor()->getMock();
        $this->assetRepoMock             = $this->getMockBuilder(Repository::class)
            ->disableOriginalConstructor()->getMock();
        $this->frontendUrlMock           = $this->getMockBuilder(Url::class)
            ->disableOriginalConstructor()->getMock();
        $this->requestMock               = $this->getMockForAbstractClass(RequestInterface::class);
        $this->escaperMock               = $this->getMockBuilder(Escaper::class)
            ->disableOriginalConstructor()->getMock();
        $checkoutSessionMethods          = array_unique(
            array_merge(
                get_class_methods(Session::class),
                ['setLocationIdSelected', 'setPickupTime', 'getLocationIdSelected']
            )
        );
        $this->checkoutSessionMock       = $this->getMockBuilder(Session::class)
            ->setMethods($checkoutSessionMethods)
            ->disableOriginalConstructor()->getMock();
        $this->timezoneMock              = $this->getMockForAbstractClass(TimezoneInterface::class);
        $this->frontendMock              = $this->getMockBuilder(Frontend::class)
            ->disableOriginalConstructor()->getMock();

        $this->object = new LocationsRepository(
            $this->collectionFactoryMock,
            $this->collectionProcessorMock,
            $this->searchResultsFactoryMock,
            $this->searchCriteriaBuilderMock,
            $this->filterBuilderMock,
            $this->filterGroupBuilderMock,
            $this->storeManagerMock,
            $this->helperDataMock,
            $this->helperImageMock,
            $this->assetRepoMock,
            $this->frontendUrlMock,
            $this->requestMock,
            $this->escaperMock,
            $this->checkoutSessionMock,
            $this->timezoneMock,
            $this->frontendMock
        );
    }
}
