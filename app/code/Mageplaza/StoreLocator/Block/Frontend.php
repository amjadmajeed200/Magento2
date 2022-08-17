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

namespace Mageplaza\StoreLocator\Block;

use DateTimeZone as DateTimeZoneAlias;
use Exception as ExceptionAlias;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart;
use Magento\Cms\Model\BlockFactory;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Quote\Model\Quote\Item;
use Magento\Widget\Block\BlockInterface;
use Mageplaza\StoreLocator\Helper\Data as HelperData;
use Mageplaza\StoreLocator\Helper\Image as HelperImage;
use Mageplaza\StoreLocator\Model\Config\Source\Country;
use Mageplaza\StoreLocator\Model\Config\Source\System\MapStyle;
use Mageplaza\StoreLocator\Model\Holiday;
use Mageplaza\StoreLocator\Model\HolidayFactory;
use Mageplaza\StoreLocator\Model\Location;
use Mageplaza\StoreLocator\Model\LocationFactory;
use Mageplaza\StoreLocator\Model\ResourceModel\Holiday as HolidayResource;
use Mageplaza\StoreLocator\Model\ResourceModel\Location as LocationResource;
use Mageplaza\StoreLocator\Model\ResourceModel\Location\Collection;
use Mageplaza\StoreLocator\Model\ResourceModel\Location\CollectionFactory;

/**
 * Class Frontend
 * @package Mageplaza\StoreLocator\Block
 */
class Frontend extends Template implements BlockInterface
{
    /**
     * @var DateTime
     */
    protected $_dateTime;

    /**
     * @var TimezoneInterface
     */
    protected $_timeZone;

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * @var LocationFactory
     */
    protected $locationFactory;

    /**
     * @var HolidayFactory
     */
    protected $holidayFactory;

    /**
     * @var HelperImage
     */
    protected $_helperImage;

    /**
     * @var BlockFactory
     */
    protected $_blockFactory;

    /**
     * @var Cart
     */
    protected $_cart;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var CollectionFactory
     */
    protected $_locationColFactory;

    /**
     * @var LocationResource
     */
    protected $_locationResource;

    /**
     * @var LocationResource
     */
    protected $_holidayResource;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var Country
     */
    protected $country;

    /**
     * Frontend constructor.
     *
     * @param Context $context
     * @param DateTime $dateTime
     * @param HelperData $helperData
     * @param LocationFactory $locationFactory
     * @param HolidayFactory $holidayFactory
     * @param HelperImage $helperImage
     * @param BlockFactory $blockFactory
     * @param Cart $cart
     * @param ProductRepositoryInterface $productRepository
     * @param CollectionFactory $locationColFactory
     * @param LocationResource $locationResource
     * @param HolidayResource $holidayResource
     * @param ManagerInterface $messageManager
     * @param Country $country
     * @param array $data
     */
    public function __construct(
        Context $context,
        DateTime $dateTime,
        HelperData $helperData,
        LocationFactory $locationFactory,
        HolidayFactory $holidayFactory,
        HelperImage $helperImage,
        BlockFactory $blockFactory,
        Cart $cart,
        ProductRepositoryInterface $productRepository,
        CollectionFactory $locationColFactory,
        LocationResource $locationResource,
        HolidayResource $holidayResource,
        ManagerInterface $messageManager,
        Country $country,
        array $data = []
    ) {
        $this->_dateTime           = $dateTime;
        $this->_timeZone           = $context->getLocaleDate();
        $this->_helperData         = $helperData;
        $this->locationFactory     = $locationFactory;
        $this->holidayFactory      = $holidayFactory;
        $this->_helperImage        = $helperImage;
        $this->_blockFactory       = $blockFactory;
        $this->_cart               = $cart;
        $this->productRepository   = $productRepository;
        $this->_locationColFactory = $locationColFactory;
        $this->_locationResource   = $locationResource;
        $this->_holidayResource    = $holidayResource;
        $this->messageManager      = $messageManager;
        $this->country             = $country;

        parent::__construct($context, $data);
    }

    /**
     * @return Template
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    protected function _prepareLayout()
    {
        $fullActionName = $this->getRequest()->getFullActionName();
        if ($fullActionName !== 'mpstorelocator_storelocator_store' &&
            $fullActionName !== 'mpstorelocator_storelocator_view'
        ) {
            return parent::_prepareLayout();
        }

        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbs->addCrumb('home', [
                'label' => __('Home'),
                'title' => __('Go to Home Page'),
                'link'  => $this->_storeManager->getStore()->getBaseUrl()
            ])
                ->addCrumb($this->_helperData->getRoute(), $this->getBreadcrumbsData());
        }

        $this->applySeoCode();

        return parent::_prepareLayout();
    }

    /**
     * @return $this
     */
    public function applySeoCode()
    {
        $this->pageConfig->getTitle()->set(
            implode($this->getTitleSeparator(), array_reverse($this->getStoreLocatorTitle(true)))
        );

        $description = $this->_helperData->getSeoSetting('meta_description');
        $this->pageConfig->setDescription($description);

        $keywords = $this->_helperData->getSeoSetting('meta_keywords');
        $this->pageConfig->setKeywords($keywords);

        return $this;
    }

    /**
     * Retrieve HTML title value separator (with space)
     *
     * @return string
     */
    public function getTitleSeparator()
    {
        $separator = (string) $this->_helperData->getConfigValue('catalog/seo/title_separator');

        return ' ' . $separator . ' ';
    }

    /**
     * @param bool $meta
     *
     * @return array
     */
    public function getStoreLocatorTitle($meta = false)
    {
        $pageTitle = $this->_helperData->getPageTitle();
        if ($meta) {
            $title = $this->_helperData->getSeoSetting('meta_title') ?: $pageTitle;

            return [$title];
        }

        return $pageTitle;
    }

    /**
     * @return array
     */
    protected function getBreadcrumbsData()
    {
        $label = $this->_helperData->getPageTitle();

        $data = [
            'label' => $label,
            'title' => $label
        ];

        $fullActionName = $this->getRequest()->getFullActionName();
        if ($fullActionName !== 'mpstorelocator_storelocator_store' &&
            $fullActionName !== 'mpstorelocator_storelocator_view'
        ) {
            $data['link'] = $this->_helperData->getStoreLocatorUrl();
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getLocationList()
    {
        $list = [];

        try {
            $locations = $this->_locationColFactory->create()->addFieldToFilter('status', 1)
                ->setOrder('sort_order', 'ASC');
            $list      = $this->filterLocation($locations);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $list;
    }

    /**
     * @return array
     */
    public function getPickupLocationList()
    {
        try {
            $locations   = $this->_locationColFactory->create()->addFieldToFilter('status', 1);
            $locationIds = $this->getLocationIdsPickup();

            if (!empty($locationIds)) {
                $locations->addFieldToFilter('location_id', $locationIds);

                return $this->filterLocation($locations);
            }
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return [];
    }

    /**
     * Check to filter location pickup
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function getLocationIdsPickup()
    {
        $allItems = $this->_cart->getQuote()->getAllItems();

        return $this->getLocationIdsPickupByQuoteItems($allItems);
    }

    /**
     * @param array $allItems
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function getLocationIdsPickupByQuoteItems($allItems)
    {
        $locationIds     = [];
        $attributeValues = [];
        $childProIds     = [];

        /** @var $item Item */
        foreach ($allItems as $item) {
            if ($item->getHasChildren() && $item->getProduct()->getTypeId() === 'configurable') {
                /** get child product id configuration product **/
                foreach ($item->getChildren() as $child) {
                    $childProIds[] = $child->getProduct()->getId();
                }
            }
        }

        foreach ($allItems as $item) {
            $productId = $item->getProduct()->getId();
            if (in_array($productId, $childProIds, true)) {
                continue;
            }
            $product = $this->productRepository->getById($productId);
            if (!$product->getCustomAttribute('mp_pickup_locations')) {
                return [];
            }

            $attributeValues[] = explode(',', $product->getCustomAttribute('mp_pickup_locations')->getValue());
        }

        if (!empty($attributeValues)) {
            $intersect = $attributeValues[0];
            foreach ($attributeValues as $key => $value) {
                $intersect = array_intersect($intersect, $attributeValues[$key]);
            }

            foreach ($intersect as $value) {
                $data          = explode('-', $value);
                $locationIds[] = array_pop($data);
            }
        }

        return $locationIds;
    }

    /**
     * filter location by store
     *
     * @param Collection $locations
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function filterLocation($locations)
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $result  = [];
        foreach ($locations->getItems() as $location) {
            $locationStores = explode(',', $location->getStoreIds());
            if (in_array($storeId, $locationStores, true) || in_array('0', $locationStores, true)) {
                $result[] = $location;
            }
        }

        return $result;
    }

    /**
     * Get Zoom default in configuration
     *
     * @return int|mixed
     */
    public function getZoom()
    {
        $zoom = $this->_helperData->getMapSetting('zoom_default');

        return $zoom ?: 12;
    }

    /**
     * get filter radius config
     *
     * @return array|bool
     */
    public function getFilterRadius()
    {
        $config = $this->_helperData->getMapSetting('filter_radius');

        return $config ? explode(',', $config) : false;
    }

    /**
     * convert km to miles
     *
     * @param mixed $distance
     *
     * @return mixed
     */
    public function convertKmToMiles($distance)
    {
        $config = (int) $this->_helperData->getMapSetting('distance_unit');

        if ($config === 1) {
            return $distance;
        }

        return $distance * 0.621371;
    }

    /**
     * Get text Distance Unit
     *
     * @return string
     */
    public function getUnitText()
    {
        $config = (int) $this->_helperData->getMapSetting('distance_unit');

        if ($config === 1) {
            return 'Miles';
        }

        return 'Km';
    }

    /**
     * @param string $img
     *
     * @return string
     */
    public function getUrlImg($img)
    {
        return $this->getViewFileUrl('Mageplaza_StoreLocator::media/' . $img);
    }

    /**
     * get default radius filter config
     *
     * @return int|mixed
     */
    public function getDefaultRadius()
    {
        $config = $this->_helperData->getMapSetting('default_radius');

        return $config ?: 10000;
    }

    /**
     * @param Location $location
     *
     * @return array|mixed
     */
    public function getStoreImages($location)
    {
        $images    = [];
        $imageJson = $location->getImages();
        if ($imageJson) {
            $images = $this->_helperData->jsDecode($imageJson);
        }

        return $images;
    }

    /**
     * @param Location $location
     *
     * @return array|mixed
     */
    public function getStoreMainImage($location)
    {
        $images = $this->getStoreImages($location);

        return isset($images[0]) ? $images[0] : [];
    }

    /**
     * @param Location $location
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getStoreMainImageUrl($location)
    {
        $mainImage = $this->getStoreMainImage($location);
        $url       = $this->resizeImage(isset($mainImage['file']) ? $mainImage['file'] : null, '100x');

        return $url;
    }

    /**
     * @param Location $location
     *
     * @return Phrase|mixed
     */
    public function getStoreMainImageAlt($location)
    {
        $alt       = __('Store Image');
        $mainImage = $this->getStoreMainImage($location);
        if (isset($mainImage['label'])) {
            $alt = !empty($mainImage['label']) ? $mainImage['label'] : __('Store Image');
        }

        return $alt;
    }

    /**
     * Get Store Location by id
     *
     * @param int $locationId
     *
     * @return Location
     */
    public function getStoreLocation($locationId)
    {
        return $this->locationFactory->create()->load($locationId);
    }

    /**
     * Check holiday of store location
     *
     * @param array $holidayIds
     * @param string $currentTime
     *
     * @return bool
     */
    public function checkHoliday($holidayIds, $currentTime)
    {
        foreach ($holidayIds as $holidayId) {
            $holiday = $this->holidayFactory->create()->load($holidayId);

            if ($holiday->getStatus() &&
                $currentTime >= strtotime($holiday->getFrom()?:'') && $currentTime <= strtotime($holiday->getTo()?:'')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get open/close notify for each story
     *
     * @param Location $location
     *
     * @return Phrase
     * @throws ExceptionAlias
     */
    public function getOpenCloseNotify($location)
    {
        $dateTime = new \DateTime($this->_dateTime->date(), new DateTimeZoneAlias('UTC'));
        $dateTime->setTimezone(new DateTimeZoneAlias($location->getTimeZone()));
        $currentDayOfWeek = strtolower($dateTime->format('l'));
        $currentTime      = strtotime($dateTime->format('H:i'));
        $holidayIds       = $this->_locationResource->getHolidayIdsByLocation($location->getLocationId());

        if ($this->checkHoliday($holidayIds, $currentTime)) {
            return [
                'status'   => 'close',
                'label' => __('Closed')
            ];
        } else {
            switch ($currentDayOfWeek) {
                case HelperData::MONDAY:
                    $openTime = $location->getOperationMon();
                    $result   = $this->getOpenCloseTime($openTime, $currentTime);
                    break;
                case HelperData::TUESDAY:
                    $openTime = $location->getOperationTue();
                    $result   = $this->getOpenCloseTime($openTime, $currentTime);
                    break;
                case HelperData::WEDNESDAY:
                    $openTime = $location->getOperationWed();
                    $result   = $this->getOpenCloseTime($openTime, $currentTime);
                    break;
                case HelperData::THURSDAY:
                    $openTime = $location->getOperationThu();
                    $result   = $this->getOpenCloseTime($openTime, $currentTime);
                    break;
                case HelperData::FRIDAY:
                    $openTime = $location->getOperationFri();
                    $result   = $this->getOpenCloseTime($openTime, $currentTime);
                    break;
                case HelperData::SATURDAY:
                    $openTime = $location->getOperationSat();
                    $result   = $this->getOpenCloseTime($openTime, $currentTime);
                    break;
                default:
                    $openTime = $location->getOperationSun();
                    $result   = $this->getOpenCloseTime($openTime, $currentTime);
                    break;
            }
        }
        return [
            'status' => 'open',
            'label' => $result
        ];
    }

    /**
     * Get open/close time alert for each day in week
     *
     * @param string $dayOpenTime
     * @param string $currentTime
     *
     * @return Phrase
     */
    public function getOpenCloseTime($dayOpenTime, $currentTime)
    {
        $result   = __('Closed');
        $openTime = $this->_helperData->jsDecode($dayOpenTime);
        $unit     = ((float) $openTime['from'][0] > 12) ? 'PM' : 'AM';

        $fromTime = $openTime['from'][0] . ':' . $openTime['from'][1];
        $toTime   = $openTime['to'][0] . ':' . $openTime['to'][1];
        if (!$openTime['value']) {
            return $result;
        }

        if ($openTime['value']) {
            if ($currentTime >= strtotime($fromTime) && $currentTime <= strtotime($toTime)) {
                $result = __('Open now: %1 - %2', $fromTime, $toTime);
            } else {
                $result = __('Open at %1 %2', $fromTime, $unit);
            }
        } else {
            $result = __('Open at %1%2', $fromTime, $unit);
        }

        return $result;
    }

    /**
     * Resize Image Function
     *
     * @param string $image
     * @param null $size
     * @param string $type
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function resizeImage($image, $size = null, $type = '')
    {
        if (!$image) {
            return $this->getDefaultImageUrl();
        }

        return $this->_helperImage->resizeImage($image, $size, $type);
    }

    /**
     * @param Location $locations
     *
     * @return string
     * @throws ExceptionAlias
     */
    public function getDataLocations($locations)
    {
        $locationsData = [];
        foreach ($locations as $location) {
            $locationsData[] = [
                'id'          => $location->getLocationId(),
                'lat'         => $location->getLatitude(),
                'lng'         => $location->getLongitude(),
                'name'        => $location->getName(),
                'street'      => $location->getStreet(),
                'state'       => $location->getStateProvince(),
                'city'        => $location->getCity(),
                'country'     => $location->getCountry(),
                'postal'      => $location->getPostalCode(),
                'phone1'      => $location->getPhoneOne(),
                'phone2'      => $location->getPhoneTwo(),
                'web'         => $location->getWebsite(),
                'facebook'    => $location->getFacebook(),
                'twitter'     => $location->getTwitter(),
                'time'        => $this->getOpenCloseNotify($location)['label'],
                'image'       => $this->getStoreMainImageUrl($location) ?: $this->getDefaultImgUrl(),
                'fax'         => $location->getFax(),
                'mail'        => $location->getEmail(),
                'description' => $location->getDescription(),
                'markerUrl'   => $this->getMakerIconUrl(),
                'category'    => 'Restaurant',
                'address'     => $location->getStreet(),
                'address2'    => '',
                'url'         => $location->getUrlKey(),
                'details'     => __('Details +')
            ];
        }

        return $this->_helperData->jsEncode($locationsData);
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getDataConfigLocation()
    {
        $defaultStore = $this->_helperData->getDefaultStoreLocation();
        $defaultLat   = $defaultStore->getLatitude();
        $defaultLng   = $defaultStore->getLongitude();

        $data = [
            'zoom'                      => $this->getZoom(),
            'markerIcon'                => $this->getMakerIconUrl(),
            'dataLocations'             => $this->getLocationsDataUrl(),
            'infowindowTemplatePath'    => $this->getViewFileUrl(
                'Mageplaza_StoreLocator::templates/infowindow-description.html'
            ),
            'listTemplatePath'          => $this->getViewFileUrl(
                'Mageplaza_StoreLocator::templates/location-list-description.html'
            ),
            'KMLinfowindowTemplatePath' => $this->getViewFileUrl(
                'Mageplaza_StoreLocator::templates/kml-infowindow-description.html'
            ),
            'KMLlistTemplatePath'       => $this->getViewFileUrl(
                'Mageplaza_StoreLocator::templates/kml-location-list-description.html'
            ),
            'isFilter'                  => $this->isFilter(),
            'isFilterRadius'            => $this->isEnableFilterRadius(),
            'locationIdDetail'          => $this->_helperData->getLocationIdFromRouter(),
            'urlSuffix'                 => $this->_helperData->getUrlSuffix(),
            'keyMap'                    => $this->_helperData->getMapSetting('api_key'),
            'router'                    => $this->_helperData->getRoute(),
            'isDefaultStore'            => $this->checkIsDefaultStore(),
            'defaultLat'                => $defaultLat,
            'defaultLng'                => $defaultLng,
            'pagination'                => (bool) $this->_helperData->getConfigGeneral('pagination'),
            'locationsData'             => $this->getLocationsData(),
            'isSearchByArea'            => $this->isSearchByArea(),
        ];

        return $this->_helperData->jsEncode($data);
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    public function getLocationsData()
    {
        $locations = $this->_locationColFactory->create()->addFieldToFilter('status', 1);
        $list      = $this->filterLocation($locations);
        $data      = [];

        /** @var Location $location */
        foreach ($list as $location) {
            $data[$location->getId()] = [
                'name'        => $this->escapeHtml($location->getName()),
                'countryId'   => $location->getCountry(),
                'regionId'    => '0',
                'region'      => $this->escapeHtml($location->getStateProvince()),
                'street'      => $this->escapeHtml($location->getStreet()),
                'telephone'   => $location->getPhoneOne() ?: '0',
                'postcode'    => $location->getPostalCode(),
                'city'        => $this->escapeHtml($location->getCity()),
                'timeData'    => $this->getTimeData($location),
                'holidayData' => $this->getHolidayData($location)
            ];
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getLocationsDataUrl()
    {
        if ($this->getRequest()->getParam('isPickup')) {
            return $this->getUrl('mpstorelocator/storelocator/pickuplocationsdata');
        }

        return $this->getUrl('mpstorelocator/storelocator/locationsdata');
    }

    /**
     * @return bool
     */
    public function checkIsDefaultStore()
    {
        if ($this->_helperData->getDefaultStoreLocation()->getId()) {
            return true;
        }

        return false;
    }

    /**
     * Get Url marker Icon
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getMakerIconUrl()
    {
        if ($markerIcon = $this->_helperData->getMapSetting('marker_icon')) {
            return $this->_helperImage->getBaseMediaUrl() . '/' .
                $this->_helperImage->getMediaPath($markerIcon, 'marker_icon');
        }

        return $this->getUrlImg('marker.png');
    }

    /**
     * Get Default Img Url
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getDefaultImgUrl()
    {
        if ($defaultImage = $this->_helperData->getConfigGeneral('upload_default_image')) {
            return $this->_helperImage->getBaseMediaUrl() . '/' .
                $this->_helperImage->getMediaPath(
                    $defaultImage,
                    'image'
                );
        }

        return $this->getUrlImg('defaultImg.png');
    }

    /**
     * @param string $storeDay
     *
     * @return string
     */
    public function getDayTime($storeDay)
    {
        $openTime = $this->_helperData->jsDecode($storeDay);
        $fromTime = $openTime['from'][0] . ':' . $openTime['from'][1];
        $toTime   = $openTime['to'][0] . ':' . $openTime['to'][1];
        if ($openTime['value']) {
            $result = $fromTime . ' - ' . $toTime;
        } else {
            $result = '<span style="color:red">' . __('Closed') . '</span>';
        }

        return $result;
    }

    /**
     * @param $location
     *
     * @return string
     * @throws ExceptionAlias
     */
    public function getCurrentDay($location)
    {
        $dateTime = new \DateTime($this->_dateTime->date(), new DateTimeZoneAlias('UTC'));
        $dateTime->setTimezone(new DateTimeZoneAlias($location->getTimeZone()));

        return strtolower($dateTime->format('l'));
    }

    /**
     * @return string
     * @throws FileSystemException
     */
    public function getMapTemplate()
    {
        $mapType = $this->_helperData->getMapSetting('style');

        if ($mapType === MapStyle::STYLE_DEFAULT) {
            return '[]';
        }
        if ($mapType === MapStyle::STYLE_CUSTOM) {
            return $this->_helperData->getMapSetting('custom_style');
        }

        return $this->_helperData->getMapTheme($mapType);
    }

    /**
     * get config Filter by current position
     *
     * @return bool
     */
    public function isEnableFilterRadius()
    {
        return $this->_helperData->getConfigGeneral('filter_store/current_position') === '1';
    }

    /**
     * get config filter store
     *
     * @return bool
     */
    public function isFilter()
    {
        return $this->_helperData->getConfigGeneral('filter_store/enabled') === '1';
    }

    /**
     * @return false|string
     */
    public function getAvailableProduct()
    {
        return $this->_helperData->getAvailableProduct();
    }

    /**
     * @return false|string
     */
    public function getPickupConfig()
    {
        return $this->_helperData->getPickupConfig('title');
    }

    /**
     * @return mixed
     */
    public function getApiKey()
    {
        return $this->_helperData->getMapSetting('api_key');
    }

    /**
     * @return HelperData
     */
    public function getHelper()
    {
        return $this->_helperData;
    }

    /**
     * @param $location
     *
     * @return array
     */
    public function getHolidayData($location)
    {
        $data       = [];
        $holidayIds = $this->_locationResource->getHolidayIdsByLocation($location->getId());

        foreach ($holidayIds as $holidayId) {
            /** @var Holiday $holiday */
            $holiday = $this->holidayFactory->create();
            $this->_holidayResource->load($holiday, $holidayId);

            if ($holiday->getStatus()) {
                $data[$holidayId] = [
                    'from' => $holiday->getFrom(),
                    'to'   => $holiday->getTo()
                ];
            }
        }

        return $data;
    }

    /**
     * @param Location $location
     *
     * @return array
     */
    public function getTimeData($location)
    {
        return [
            0 => $this->_helperData->jsDecode($location->getOperationSun()),
            1 => $this->_helperData->jsDecode($location->getOperationMon()),
            2 => $this->_helperData->jsDecode($location->getOperationTue()),
            3 => $this->_helperData->jsDecode($location->getOperationWed()),
            4 => $this->_helperData->jsDecode($location->getOperationThu()),
            5 => $this->_helperData->jsDecode($location->getOperationFri()),
            6 => $this->_helperData->jsDecode($location->getOperationSat()),
        ];
    }

    /**
     * get config filter store
     *
     * @return bool
     */
    public function isSearchByArea()
    {
        return $this->_helperData->getConfigGeneral('search_by_area') === '1';
    }

    /**
     * @return array
     */
    public function getAllCountry()
    {
        return $this->country->toOptionArray();
    }

    /**
     * Check close notify for each story
     *
     * @param Location $location
     * @param $timeDay
     *
     * @return bool|string
     */
    public function checkCloseTime($location,$timeDay)
    {
        $currentDay = $this->getCurrentDay($location);
        if($currentDay == $timeDay)
        {
            $dateTime = new \DateTime($this->_dateTime->date(), new DateTimeZoneAlias('UTC'));
            $dateTime->setTimezone(new DateTimeZoneAlias($location->getTimeZone()));
            $currentTime      = strtotime($dateTime->format('H:i'));
            $holidayIds       = $this->_locationResource->getHolidayIdsByLocation($location->getLocationId());

            if ($this->checkHoliday($holidayIds, $currentTime)) {
                return __('Closed');
            }
        }
        return false;
    }
}
