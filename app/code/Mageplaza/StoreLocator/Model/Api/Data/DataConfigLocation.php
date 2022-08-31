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

namespace Mageplaza\StoreLocator\Model\Api\Data;

use Magento\Framework\DataObject;
use Mageplaza\StoreLocator\Api\Data\DataConfigLocationInterface;

/**
 * Class DataConfigLocation
 * @package Mageplaza\StoreLocator\Model\Api\Data
 */
class DataConfigLocation extends DataObject implements DataConfigLocationInterface
{
    /**
     * @inheritDoc
     */
    public function getZoom()
    {
        return $this->getData(self::ZOOM);
    }

    /**
     * @inheritDoc
     */
    public function setZoom($value)
    {
        return $this->setData(self::ZOOM, $value);
    }

    /**
     * @inheritDoc
     */
    public function getStyle()
    {
        return $this->getData(self::STYLE);
    }

    /**
     * @inheritDoc
     */
    public function setStyle($value)
    {
        return $this->setData(self::STYLE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCustomStyle()
    {
        return $this->getData(self::CUSTOM_STYLE);
    }

    /**
     * @inheritDoc
     */
    public function setCustomStyle($value)
    {
        return $this->setData(self::CUSTOM_STYLE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getFilterRadius()
    {
        return $this->getData(self::FILTER_RADIUS);
    }

    /**
     * @inheritDoc
     */
    public function setFilterRadius($value)
    {
        return $this->setData(self::FILTER_RADIUS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getDefaultRadius()
    {
        return $this->getData(self::DEFAULT_RADIUS);
    }

    /**
     * @inheritDoc
     */
    public function setDefaultRadius($value)
    {
        return $this->setData(self::DEFAULT_RADIUS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getDistanceUnit()
    {
        return $this->getData(self::DISTANCE_UNIT);
    }

    /**
     * @inheritDoc
     */
    public function setDistanceUnit($value)
    {
        return $this->setData(self::DISTANCE_UNIT, $value);
    }

    /**
     * @inheritDoc
     */
    public function getMarkerIcon()
    {
        return $this->getData(self::MARKER_ICON);
    }

    /**
     * @inheritDoc
     */
    public function setMarkerIcon($value)
    {
        return $this->setData(self::MARKER_ICON, $value);
    }

    /**
     * @inheritDoc
     */
    public function getDataLocations()
    {
        return $this->getData(self::DATA_LOCATIONS);
    }

    /**
     * @inheritDoc
     */
    public function setDataLocations($value)
    {
        return $this->setData(self::DATA_LOCATIONS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getInfowindowTemplatePath()
    {
        return $this->getData(self::INFO_WINDOW_TEMPLATE_PATH);
    }

    /**
     * @inheritDoc
     */
    public function setInfowindowTemplatePath($value)
    {
        return $this->setData(self::INFO_WINDOW_TEMPLATE_PATH, $value);
    }

    /**
     * @inheritDoc
     */
    public function getListTemplatePath()
    {
        return $this->getData(self::LIST_TEMPLATE_PATH);
    }

    /**
     * @inheritDoc
     */
    public function setListTemplatePath($value)
    {
        return $this->setData(self::LIST_TEMPLATE_PATH, $value);
    }

    /**
     * @inheritDoc
     */
    public function getKmlInfowindowTemplatePath()
    {
        return $this->getData(self::KML_INFO_WINDOW_TEMPLATE_PATH);
    }

    /**
     * @inheritDoc
     */
    public function setKmlInfowindowTemplatePath($value)
    {
        return $this->setData(self::KML_INFO_WINDOW_TEMPLATE_PATH, $value);
    }

    /**
     * @inheritDoc
     */
    public function getKmlListTemplatePath()
    {
        return $this->getData(self::KML_LIST_TEMPLATE_PATH);
    }

    /**
     * @inheritDoc
     */
    public function setKmlListTemplatePath($value)
    {
        return $this->setData(self::KML_LIST_TEMPLATE_PATH, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIsFilter()
    {
        return $this->getData(self::IS_FILTER);
    }

    /**
     * @inheritDoc
     */
    public function setIsFilter($value)
    {
        return $this->setData(self::IS_FILTER, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIsFilterRadius()
    {
        return $this->getData(self::IS_FILTER_RADIUS);
    }

    /**
     * @inheritDoc
     */
    public function setIsFilterRadius($value)
    {
        return $this->setData(self::IS_FILTER_RADIUS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getLocationIdDetail()
    {
        return $this->getData(self::LOCATION_ID_DETAIL);
    }

    /**
     * @inheritDoc
     */
    public function setLocationIdDetail($value)
    {
        return $this->setData(self::LOCATION_ID_DETAIL, $value);
    }

    /**
     * @inheritDoc
     */
    public function getUrlSuffix()
    {
        return $this->getData(self::URL_SUFFIX);
    }

    /**
     * @inheritDoc
     */
    public function setUrlSuffix($value)
    {
        return $this->setData(self::URL_SUFFIX, $value);
    }

    /**
     * @inheritDoc
     */
    public function getKeyMap()
    {
        return $this->getData(self::KEY_MAP);
    }

    /**
     * @inheritDoc
     */
    public function setKeyMap($value)
    {
        return $this->setData(self::KEY_MAP, $value);
    }

    /**
     * @inheritDoc
     */
    public function getRouter()
    {
        return $this->getData(self::ROUTER);
    }

    /**
     * @inheritDoc
     */
    public function setRouter($value)
    {
        return $this->setData(self::ROUTER, $value);
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
    public function getDefaultLat()
    {
        return $this->getData(self::DEFAULT_LAT);
    }

    /**
     * @inheritDoc
     */
    public function setDefaultLat($value)
    {
        return $this->setData(self::DEFAULT_LAT, $value);
    }

    /**
     * @inheritDoc
     */
    public function getDefaultLng()
    {
        return $this->getData(self::DEFAULT_LNG);
    }

    /**
     * @inheritDoc
     */
    public function setDefaultLng($value)
    {
        return $this->setData(self::DEFAULT_LNG, $value);
    }

    /**
     * @inheritDoc
     */
    public function getLocationsData()
    {
        return $this->getData(self::LOCATIONS_DATA);
    }

    /**
     * @inheritDoc
     */
    public function setLocationsData($value)
    {
        return $this->setData(self::LOCATIONS_DATA, $value);
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * @inheritDoc
     */
    public function setTitle($value)
    {
        return $this->setData(self::TITLE, $value);
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
    public function getUploadDefaultImage()
    {
        return $this->getData(self::UPLOAD_DEFAULT_IMAGE);
    }

    /**
     * @inheritDoc
     */
    public function setUploadDefaultImage($value)
    {
        return $this->setData(self::UPLOAD_DEFAULT_IMAGE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getUploadHeadImage()
    {
        return $this->getData(self::UPLOAD_HEAD_IMAGE);
    }

    /**
     * @inheritDoc
     */
    public function setUploadHeadImage($value)
    {
        return $this->setData(self::UPLOAD_HEAD_IMAGE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getUploadHeadIcon()
    {
        return $this->getData(self::UPLOAD_HEAD_ICON);
    }

    /**
     * @inheritDoc
     */
    public function setUploadHeadIcon($value)
    {
        return $this->setData(self::UPLOAD_HEAD_ICON, $value);
    }

    /**
     * @inheritDoc
     */
    public function getBottomStaticBlock()
    {
        return $this->getData(self::BOTTOM_STATIC_BLOCK);
    }

    /**
     * @inheritDoc
     */
    public function setBottomStaticBlock($value)
    {
        return $this->setData(self::BOTTOM_STATIC_BLOCK, $value);
    }

    /**
     * @inheritDoc
     */
    public function getShowOn()
    {
        return $this->getData(self::SHOW_ON);
    }

    /**
     * @inheritDoc
     */
    public function setShowOn($value)
    {
        return $this->setData(self::SHOW_ON, $value);
    }

    /**
     * @inheritDoc
     */
    public function getEnableDirection()
    {
        return $this->getData(self::ENABLE_DIRECTION);
    }

    /**
     * @inheritDoc
     */
    public function setEnableDirection($value)
    {
        return $this->setData(self::ENABLE_DIRECTION, $value);
    }

    /**
     * @inheritDoc
     */
    public function getPagination()
    {
        return $this->getData(self::PAGINATION);
    }

    /**
     * @inheritDoc
     */
    public function setPagination($value)
    {
        return $this->setData(self::PAGINATION, $value);
    }

    /**
     * @inheritDoc
     */
    public function getMetaTitle()
    {
        return $this->getData(self::META_TITLE);
    }

    /**
     * @inheritDoc
     */
    public function setMetaTitle($value)
    {
        return $this->setData(self::META_TITLE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getMetaDescription()
    {
        return $this->getData(self::META_DESCRIPTION);
    }

    /**
     * @inheritDoc
     */
    public function setMetaDescription($value)
    {
        return $this->setData(self::META_DESCRIPTION, $value);
    }

    /**
     * @inheritDoc
     */
    public function getMetaKeywords()
    {
        return $this->getData(self::META_KEYWORDS);
    }

    /**
     * @inheritDoc
     */
    public function setMetaKeywords($value)
    {
        return $this->setData(self::META_KEYWORDS, $value);
    }
}
