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

namespace Mageplaza\StoreLocator\Model;

use Magento\Checkout\Model\Session;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\App\Area;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Url;
use Magento\Framework\View\Asset\Repository;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Swatches\Helper\Media;
use Mageplaza\StoreLocator\Api\LocationsRepositoryInterface;
use Mageplaza\StoreLocator\Block\Frontend;
use Mageplaza\StoreLocator\Block\Store\Head;
use Mageplaza\StoreLocator\Helper\Data;
use Mageplaza\StoreLocator\Helper\Image as HelperImage;
use Mageplaza\StoreLocator\Model\Api\Data\DataConfigLocation;
use Mageplaza\StoreLocator\Model\Api\Data\LocationData;
use Mageplaza\StoreLocator\Model\Api\Data\PickupConfig;
use Mageplaza\StoreLocator\Model\Config\Source\System\MapStyle;
use Mageplaza\StoreLocator\Model\ResourceModel\Location\Collection;
use Mageplaza\StoreLocator\Model\ResourceModel\Location\CollectionFactory;

/**
 * Class LocationsRepository
 * @package Mageplaza\StoreLocator\Model
 */
class LocationsRepository implements LocationsRepositoryInterface
{

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var SearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * @var Media
     */
    protected $swatchHelper;

    /**
     * @var HelperImage
     */
    private $helperImage;

    /**
     * @var Repository
     */
    private $assetRepo;

    /**
     * @var Url
     */
    private $frontendUrl;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @var Frontend
     */
    private $frontend;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var FilterGroupBuilder
     */
    private $filterGroupBuilder;

    /**
     * @var Head
     */
    protected $head;

    /**
     * LocationsRepository constructor.
     *
     * @param CollectionFactory $collectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param StoreManagerInterface $storeManager
     * @param Data $helperData
     * @param HelperImage $helperImage
     * @param Repository $assetRepo
     * @param Url $frontendUrl
     * @param RequestInterface $request
     * @param Escaper $escaper
     * @param Session $checkoutSession
     * @param TimezoneInterface $timezone
     * @param Frontend $frontend
     * @param Head $head
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        SearchResultsInterfaceFactory $searchResultsFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        FilterGroupBuilder $filterGroupBuilder,
        StoreManagerInterface $storeManager,
        Data $helperData,
        HelperImage $helperImage,
        Repository $assetRepo,
        Url $frontendUrl,
        RequestInterface $request,
        Escaper $escaper,
        Session $checkoutSession,
        TimezoneInterface $timezone,
        Frontend $frontend,
        Head $head
    ) {
        $this->collectionFactory     = $collectionFactory;
        $this->collectionProcessor   = $collectionProcessor;
        $this->searchResultsFactory  = $searchResultsFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder         = $filterBuilder;
        $this->filterGroupBuilder    = $filterGroupBuilder;
        $this->storeManager          = $storeManager;
        $this->helperData            = $helperData;
        $this->helperImage           = $helperImage;
        $this->assetRepo             = $assetRepo;
        $this->frontendUrl           = $frontendUrl;
        $this->request               = $request;
        $this->escaper               = $escaper;
        $this->checkoutSession       = $checkoutSession;
        $this->timezone              = $timezone;
        $this->frontend              = $frontend;
        $this->head                  = $head;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null)
    {
        if (!$this->helperData->isEnabled()) {
            throw new LocalizedException(__('The module is disabled'));
        }

        if ($searchCriteria === null) {
            $searchCriteria = $this->searchCriteriaBuilder->create();
        }
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);
        foreach ($collection->getItems() as $item) {
            $item->setCreatedAt($this->timezone->date($item->getCreatedAt())->format('Y-m-d H:i:s'));
            $item->setUpdatedAt($this->timezone->date($item->getUpdatedAt())->format('Y-m-d H:i:s'));

            $images = $this->helperData->jsDecode($item->getImages());
            foreach ($images as &$image) {
                $image['file'] = $this->helperImage->getMediaUrl(HelperImage::TEMPLATE_MEDIA_PATH . $image['file']);
            }
            unset($image);
            $item->setImages($this->helperData->jsEncode($images));
        }

        /** @var SearchResultsInterface $searchResult */
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());

        return $searchResult;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function getLocations(SearchCriteriaInterface $searchCriteria = null)
    {
        if (!$searchCriteria) {
            $searchCriteria = $this->searchCriteriaBuilder->create();
        }

        $storeId           = $this->request->getParam('storeId');
        $searchTermFilter1 = $this->filterBuilder
            ->setField('store_ids')
            ->setValue($storeId)
            ->setConditionType('fin')->create();
        $searchTermFilter2 = $this->filterBuilder
            ->setField('store_ids')
            ->setValue('0')
            ->setConditionType('fin')->create();
        $this->filterGroupBuilder->addFilter($searchTermFilter1)->addFilter($searchTermFilter2);
        $filterGroups   = $searchCriteria->getFilterGroups();
        $filterGroups[] = $this->filterGroupBuilder->create();
        $searchCriteria->setFilterGroups($filterGroups);

        return $this->getList($searchCriteria);
    }

    /**
     * @inheritDoc
     * @throws NoSuchEntityException
     */
    public function getDataConfigLocation()
    {
        $defaultStore = $this->helperData->getDefaultStoreLocation();
        $defaultLat   = $defaultStore->getLatitude();
        $defaultLng   = $defaultStore->getLongitude();
        $storeId      = $this->request->getParam('storeId');
        $dataConfig   = new DataConfigLocation(
            [
                'zoom'                      => $this->frontend->getZoom(),
                'style'                     => $this->helperData->getMapSetting('style', $storeId),
                'custom_style'              => $this->helperData->getMapSetting('custom_style', $storeId),
                'filter_radius'             => $this->helperData->getMapSetting('filter_radius', $storeId),
                'default_radius'            => $this->helperData->getMapSetting('default_radius', $storeId),
                'distance_unit'             => $this->helperData->getMapSetting('distance_unit', $storeId),
                'markerIcon'                => $this->getMakerIconUrl($storeId),
                'dataLocations'             => $this->getLocationsDataUrl($storeId),
                'infowindowTemplatePath'    => $this->assetRepo->getUrlWithParams(
                    'Mageplaza_StoreLocator::templates/infowindow-description.html',
                    ['area' => Area::AREA_FRONTEND]
                ),
                'listTemplatePath'          => $this->assetRepo->getUrlWithParams(
                    'Mageplaza_StoreLocator::templates/location-list-description.html',
                    ['area' => Area::AREA_FRONTEND]
                ),
                'KMLinfowindowTemplatePath' => $this->assetRepo->getUrlWithParams(
                    'Mageplaza_StoreLocator::templates/kml-infowindow-description.html',
                    ['area' => Area::AREA_FRONTEND]
                ),
                'KMLlistTemplatePath'       => $this->assetRepo->getUrlWithParams(
                    'Mageplaza_StoreLocator::templates/kml-location-list-description.html',
                    ['area' => Area::AREA_FRONTEND]
                ),
                'isFilter'                  => $this->helperData->getConfigGeneral('filter_store/enabled', $storeId) === '1',
                'isFilterRadius'            => $this->helperData->getConfigGeneral('filter_store/current_position', $storeId) === '1',
                'locationIdDetail'          => $this->getLocationIdFromName($this->request->getParam('locationName', false)),
                'urlSuffix'                 => $this->helperData->getUrlSuffix($storeId),
                'keyMap'                    => $this->helperData->getMapSetting('api_key', $storeId),
                'router'                    => $this->helperData->getConfigGeneral('url_key', $storeId) ?: 'find-a-store',
                'isDefaultStore'            => $defaultStore->getId() ? true : false,
                'defaultLat'                => $defaultLat,
                'defaultLng'                => $defaultLng,
                'locationsData'             => $this->getLocationsData($storeId),
                'title'                     => $this->head->getStoreTitle(),
                'description'               => $this->head->getStoreDescription(),
                'upload_default_image'      => $this->frontend->getDefaultImgUrl(),
                'upload_head_image'         => $this->head->getBackgroundImage(),
                'upload_head_icon'          => $this->head->getLogoImage(),
                'bottom_static_block'       => $this->helperData->getConfigGeneral('bottom_static_block'),
                'show_on'                   => $this->helperData->getConfigGeneral('show_on'),
                'enable_direction'          => $this->helperData->getConfigGeneral('enable_direction'),
                'pagination'                => $this->helperData->getConfigGeneral('pagination'),
                'meta_title'                => $this->helperData->getSeoSetting('meta_title'),
                'meta_description'          => $this->helperData->getSeoSetting('meta_description'),
                'meta_keywords'             => $this->helperData->getSeoSetting('meta_keywords')
            ]
        );

        return $dataConfig;
    }

    /**
     * @param $url
     *
     * @return mixed
     */
    public function getLocationIdFromName($url)
    {
        return $this->helperData->getLocationByUrl($url)->getId();
    }

    /**
     * @param $storeId
     *
     * @return array
     */
    public function getLocationsData($storeId)
    {
        $locationCollection = $this->collectionFactory->create()
            ->addFieldToFilter(['store_ids', 'store_ids'], [['finset' => $storeId], ['finset' => '0']])
            ->addFieldToFilter('status', 1);
        $data               = [];

        /** @var Location $location */
        foreach ($locationCollection->getItems() as $location) {
            $id        = $location->getId();
            $data[$id] = new  LocationData([
                'name'        => $this->escaper->escapeHtml($location->getName()),
                'locationId'  => $id,
                'countryId'   => $location->getCountry(),
                'regionId'    => '0',
                'region'      => $location->getStateProvince(),
                'street'      => $location->getStreet(),
                'telephone'   => $location->getPhoneOne() ?: '0',
                'postcode'    => $location->getPostalCode(),
                'city'        => $location->getCity(),
                'timeData'    => $this->frontend->getTimeData($location),
                'holidayData' => $this->frontend->getHolidayData($location)
            ]);
        }

        return $data;
    }

    /**
     * @param $storeId
     *
     * @return string
     * @throws NoSuchEntityException
     */
    private function getMakerIconUrl($storeId)
    {
        if ($markerIcon = $this->helperData->getMapSetting('marker_icon', $storeId)) {
            return $this->helperImage->getBaseMediaUrl() . '/' .
                $this->helperImage->getMediaPath(
                    $markerIcon,
                    'marker_icon'
                );
        }

        return $this->assetRepo->getUrlWithParams(
            'Mageplaza_StoreLocator::media/marker.png',
            ['area' => Area::AREA_FRONTEND]
        );
    }

    /**
     * @param $storeId
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getLocationsDataUrl($storeId)
    {
        if ($this->request->getParam('isPickup')) {
            return $this->getUrl('mpstorelocator/storelocator/pickuplocationsdata', $storeId);
        }

        return $this->getUrl('mpstorelocator/storelocator/locationsdata', $storeId);
    }

    /**
     * @param string $storeId
     *
     * @return mixed|string
     * @throws FileSystemException
     */
    public function getMapTemplate($storeId = null)
    {
        $mapType = $this->helperData->getMapSetting('style', $storeId);

        if ($mapType === MapStyle::STYLE_DEFAULT) {
            return '[]';
        }
        if ($mapType === MapStyle::STYLE_CUSTOM) {
            return $this->helperData->getMapSetting('custom_style');
        }

        return $this->helperData->jsEncode($this->helperData->jsDecode($this->helperData->getMapTheme($mapType)));
    }

    /**
     * @inheritDoc
     * @throws NoSuchEntityException
     */
    public function getPickupData($storeId = null)
    {
        return new PickupConfig([
            'stores_map_url'       => $this->getUrl('mpstorelocator/storelocator/store', $storeId),
            'location_session_url' => $this->getUrl('mpstorelocator/storepickup/saveLocationData', $storeId),
            'locationsData'        => $this->getLocationsData($storeId),
            'pickupAfterDays'      => $this->helperData->getConfigValue(
                'carriers/mpstorepickup/available_after',
                $storeId
            ) ?: 0
        ]);
    }

    /**
     * @param $routePath
     * @param $storeId
     *
     * @return string|null
     * @throws NoSuchEntityException
     */
    public function getUrl($routePath, $storeId)
    {
        $storeCode = $this->storeManager->getStore($storeId)->getCode();

        return $this->frontendUrl->getUrl($routePath, ['_query' => ['___store' => $storeCode], '_nosid' => true]);
    }

    /**
     * @inheritDoc
     */
    public function saveLocation($location)
    {
        $this->checkoutSession->setLocationIdSelected($location->getLocationId());
        $this->checkoutSession->setPickupTime($location->getTimePickup());

        return true;
    }

    /**
     * @inheritDoc
     */
    public function getLocationId()
    {
        return $this->checkoutSession->getLocationIdSelected();
    }
}
