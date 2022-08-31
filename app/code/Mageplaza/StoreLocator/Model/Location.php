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
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\StoreLocator\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Mageplaza\StoreLocator\Api\Data\LocationsInterface;
use Mageplaza\StoreLocator\Helper\Data;
use Mageplaza\StoreLocator\Model\ResourceModel\Location as ResourceLocation;

/**
 * Class Location
 * @package Mageplaza\StoreLocator\Model
 */
class Location extends AbstractModel implements LocationsInterface
{
    const CACHE_TAG = 'mageplaza_storelocator_location';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'mageplaza_storelocator_location';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mageplaza_storelocator_location';

    /**
     * @var string
     */
    protected $_idFieldName = 'location_id';

    /**
     * @var Data
     */
    private $helperData;

    /**
     * Location constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param Data $helperData
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Data $helperData
    ) {
        $this->helperData = $helperData;
        parent::__construct($context, $registry);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceLocation::class);
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function getHolidayIds()
    {
        if (!$this->hasData('holiday_ids')) {
            $ids = $this->_getResource()->getHolidayIds($this);
            $this->setData('holiday_ids', $ids);
        }

        return (array) $this->_getData('holiday_ids');
    }

    /**
     * @inheritDoc
     */
    public function getLocationId()
    {
        return $this->getData(self::LOCATION_ID);
    }

    /**
     * @inheritDoc
     */
    public function setLocationId($value)
    {
        return $this->setData(self::LOCATION_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName($value)
    {
        return $this->setData(self::NAME, $value);
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus($value)
    {
        return $this->setData(self::STATUS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * @inheritDoc
     */
    public function setDescription($value)
    {
        return $this->setData(self::DESCRIPTION, $value);
    }

    /**
     * @inheritDoc
     */
    public function getStoreIds()
    {
        return $this->getData(self::STORE_IDS);
    }

    /**
     * @inheritDoc
     */
    public function setStoreIds($value)
    {
        return $this->setData(self::STORE_IDS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCity()
    {
        return $this->getData(self::CITY);
    }

    /**
     * @inheritDoc
     */
    public function setCity($value)
    {
        return $this->setData(self::CITY, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCountry()
    {
        return $this->getData(self::COUNTRY);
    }

    /**
     * @inheritDoc
     */
    public function setCountry($value)
    {
        return $this->setData(self::COUNTRY, $value);
    }

    /**
     * @inheritDoc
     */
    public function getStreet()
    {
        return $this->getData(self::STREET);
    }

    /**
     * @inheritDoc
     */
    public function setStreet($value)
    {
        return $this->setData(self::STREET, $value);
    }

    /**
     * @inheritDoc
     */
    public function getStateProvince()
    {
        return $this->getData(self::STATE_PROVINCE);
    }

    /**
     * @inheritDoc
     */
    public function setStateProvince($value)
    {
        return $this->setData(self::STATE_PROVINCE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getPostalCode()
    {
        return $this->getData(self::POSTAL_CODE);
    }

    /**
     * @inheritDoc
     */
    public function setPostalCode($value)
    {
        return $this->setData(self::POSTAL_CODE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getUrlKey()
    {
        return $this->getData(self::URL_KEY);
    }

    /**
     * @inheritDoc
     */
    public function setUrlKey($value)
    {
        return $this->setData(self::URL_KEY, $value);
    }

    /**
     * @inheritDoc
     */
    public function getLatitude()
    {
        return $this->getData(self::LATITUDE);
    }

    /**
     * @inheritDoc
     */
    public function setLatitude($value)
    {
        return $this->setData(self::LATITUDE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getLongitude()
    {
        return $this->getData(self::LONGITUDE);
    }

    /**
     * @inheritDoc
     */
    public function setLongitude($value)
    {
        return $this->setData(self::LONGITUDE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getPhoneOne()
    {
        return $this->getData(self::PHONE_ONE);
    }

    /**
     * @inheritDoc
     */
    public function setPhoneOne($value)
    {
        return $this->setData(self::PHONE_ONE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getPhoneTwo()
    {
        return $this->getData(self::PHONE_TWO);
    }

    /**
     * @inheritDoc
     */
    public function setPhoneTwo($value)
    {
        return $this->setData(self::PHONE_TWO, $value);
    }

    /**
     * @inheritDoc
     */
    public function getWebsite()
    {
        return $this->getData(self::WEBSITE);
    }

    /**
     * @inheritDoc
     */
    public function setWebsite($value)
    {
        return $this->setData(self::WEBSITE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getFacebook()
    {
        return $this->getData(self::FACEBOOK);
    }

    /**
     * @inheritDoc
     */
    public function setFacebook($value)
    {
        return $this->setData(self::FACEBOOK, $value);
    }

    /**
     * @inheritDoc
     */
    public function getTwitter()
    {
        return $this->getData(self::TWITTER);
    }

    /**
     * @inheritDoc
     */
    public function setTwitter($value)
    {
        return $this->setData(self::TWITTER, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIsConfigWebsite()
    {
        return $this->getData(self::IS_CONFIG_WEBSITE);
    }

    /**
     * @inheritDoc
     */
    public function setIsConfigWebsite($value)
    {
        return $this->setData(self::IS_CONFIG_WEBSITE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getFax()
    {
        return $this->getData(self::FAX);
    }

    /**
     * @inheritDoc
     */
    public function setFax($value)
    {
        return $this->setData(self::FAX, $value);
    }

    /**
     * @inheritDoc
     */
    public function getEmail()
    {
        return $this->getData(self::EMAIL);
    }

    /**
     * @inheritDoc
     */
    public function setEmail($value)
    {
        return $this->setData(self::EMAIL, $value);
    }

    /**
     * @inheritDoc
     */
    public function getImages()
    {
        return $this->getData(self::IMAGES);
    }

    /**
     * @inheritDoc
     */
    public function setImages($value)
    {
        return $this->setData(self::IMAGES, $value);
    }

    /**
     * @inheritDoc
     */
    public function getOperationMon()
    {
        $operation = $this->getData(self::OPERATION_MON);
        if ($operation === 'use_config') {
            return $this->helperData->getConfigOpenTime(Data::MONDAY);
        }

        return $operation;
    }

    /**
     * @inheritDoc
     */
    public function setOperationMon($value)
    {
        return $this->setData(self::OPERATION_MON, $value);
    }

    /**
     * @inheritDoc
     */
    public function getOperationTue()
    {
        $operation = $this->getData(self::OPERATION_TUE);
        if ($operation === 'use_config') {
            return $this->helperData->getConfigOpenTime(Data::TUESDAY);
        }

        return $operation;
    }

    /**
     * @inheritDoc
     */
    public function setOperationTue($value)
    {
        return $this->setData(self::OPERATION_TUE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getOperationWed()
    {
        $operation = $this->getData(self::OPERATION_WED);
        if ($operation === 'use_config') {
            return $this->helperData->getConfigOpenTime(Data::WEDNESDAY);
        }

        return $operation;
    }

    /**
     * @inheritDoc
     */
    public function setOperationWed($value)
    {
        return $this->setData(self::OPERATION_WED, $value);
    }

    /**
     * @inheritDoc
     */
    public function getOperationThu()
    {
        $operation = $this->getData(self::OPERATION_THU);
        if ($operation === 'use_config') {
            return $this->helperData->getConfigOpenTime(Data::THURSDAY);
        }

        return $operation;
    }

    /**
     * @inheritDoc
     */
    public function setOperationThu($value)
    {
        return $this->setData(self::OPERATION_THU, $value);
    }

    /**
     * @inheritDoc
     */
    public function getOperationFri()
    {
        $operation = $this->getData(self::OPERATION_FRI);
        if ($operation === 'use_config') {
            return $this->helperData->getConfigOpenTime(Data::FRIDAY);
        }

        return $operation;
    }

    /**
     * @inheritDoc
     */
    public function setOperationFri($value)
    {
        return $this->setData(self::OPERATION_FRI, $value);
    }

    /**
     * @inheritDoc
     */
    public function getOperationSat()
    {
        $operation = $this->getData(self::OPERATION_SAT);
        if ($operation === 'use_config') {
            return $this->helperData->getConfigOpenTime(Data::SATURDAY);
        }

        return $operation;
    }

    /**
     * @inheritDoc
     */
    public function setOperationSat($value)
    {
        return $this->setData(self::OPERATION_SAT, $value);
    }

    /**
     * @inheritDoc
     */
    public function getOperationSun()
    {
        $operation = $this->getData(self::OPERATION_SUN);
        if ($operation === 'use_config') {
            return $this->helperData->getConfigOpenTime(Data::SUNDAY);
        }

        return $operation;
    }

    /**
     * @inheritDoc
     */
    public function setOperationSun($value)
    {
        return $this->setData(self::OPERATION_SUN, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIsDefaultStore()
    {
        return $this->getData(self::IS_DEFAULT_STORE);
    }

    /**
     * @inheritDoc
     */
    public function setIsDefaultStore($value)
    {
        return $this->setData(self::IS_DEFAULT_STORE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getTimeZone()
    {
        $timeZone = $this->getData(self::TIME_ZONE);
        if ($timeZone === 'use_config') {
            return $this->helperData->getStoreTimeSetting('time_zone');
        }

        return $timeZone;
    }

    /**
     * @inheritDoc
     */
    public function setTimeZone($value)
    {
        return $this->setData(self::TIME_ZONE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIsConfigTimezone()
    {
        return $this->getData(self::IS_CONFIG_TIME_ZONE);
    }

    /**
     * @inheritDoc
     */
    public function setIsConfigTimezone($value)
    {
        return $this->setData(self::IS_CONFIG_TIME_ZONE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * @inheritDoc
     */
    public function setSortOrder($value)
    {
        return $this->setData(self::SORT_ORDER, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt($value)
    {
        return $this->setData(self::CREATED_AT, $value);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt($value)
    {
        return $this->setData(self::UPDATED_AT, $value);
    }

    /**
     * @inheritDoc
     */
    public function getProductIds()
    {
        return $this->getData(self::PRODUCT_IDS);
    }

    /**
     * @inheritDoc
     */
    public function setProductIds($value)
    {
        return $this->setData(self::PRODUCT_IDS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIsShowProductPage()
    {
        return $this->getData(self::IS_SHOW_PRODUCT_PAGE);
    }

    /**
     * @inheritDoc
     */
    public function setIsShowProductPage($value)
    {
        return $this->setData(self::IS_SHOW_PRODUCT_PAGE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIsSelectedAllProduct()
    {
        return $this->getData(self::IS_SELECTED_ALL_PRODUCT);
    }

    /**
     * @inheritDoc
     */
    public function setIsSelectedAllProduct($value)
    {
        return $this->setData(self::IS_SELECTED_ALL_PRODUCT, $value);
    }

    /**
     * @return array|null
     */
    public function getHolidaysIds()
    {
        return $this->getData('holidays_ids');
    }

    /**
     * @param array $value
     *
     * @return Location
     */
    public function setHolidaysIds($value): Location
    {
        return $this->setData('holidays_ids', $value);
    }

    /**
     * @return bool|null
     */
    public function getIsHolidayGrid()
    {
        return $this->getData('is_holiday_grid');
    }

    /**
     * @param bool $value
     *
     * @return Location
     */
    public function setIsHolidayGrid($value): Location
    {
        return $this->setData('is_holiday_grid', $value);
    }

    /**
     * @param bool $value
     *
     * @return Location
     */
    public function setIsChangedHolidayList($value): Location
    {
        return $this->setData('is_changed_holiday_list', $value);
    }

    /**
     * @param array $value
     *
     * @return Location
     */
    public function setAffectedHolidayIds($value): Location
    {
        return $this->setData('affected_holiday_ids', $value);
    }
}
