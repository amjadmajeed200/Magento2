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

namespace Mageplaza\StoreLocator\Api\Data;

/**
 * Interface DataConfigLocationInterface
 * @package Mageplaza\StoreLocator\Api\Data
 */
interface DataConfigLocationInterface
{
    const ZOOM                          = 'zoom';
    const STYLE                         = 'style';
    const CUSTOM_STYLE                  = 'custom_style';
    const FILTER_RADIUS                 = 'filter_radius';
    const DEFAULT_RADIUS                = 'default_radius';
    const DISTANCE_UNIT                 = 'distance_unit';
    const MARKER_ICON                   = 'markerIcon';
    const DATA_LOCATIONS                = 'dataLocations';
    const INFO_WINDOW_TEMPLATE_PATH     = 'infowindowTemplatePath';
    const LIST_TEMPLATE_PATH            = 'listTemplatePath';
    const KML_INFO_WINDOW_TEMPLATE_PATH = 'KMLinfowindowTemplatePath';
    const KML_LIST_TEMPLATE_PATH        = 'KMLlistTemplatePath';
    const IS_FILTER                     = 'isFilter';
    const IS_FILTER_RADIUS              = 'isFilterRadius';
    const LOCATION_ID_DETAIL            = 'locationIdDetail';
    const URL_SUFFIX                    = 'urlSuffix';
    const KEY_MAP                       = 'keyMap';
    const ROUTER                        = 'router';
    const IS_DEFAULT_STORE              = 'isDefaultStore';
    const DEFAULT_LAT                   = 'defaultLat';
    const DEFAULT_LNG                   = 'defaultLng';
    const LOCATIONS_DATA                = 'locationsData';
    const TITLE                         = 'title';
    const DESCRIPTION                   = 'description';
    const UPLOAD_DEFAULT_IMAGE          = 'upload_default_image';
    const UPLOAD_HEAD_IMAGE             = 'upload_head_image';
    const UPLOAD_HEAD_ICON              = 'upload_head_icon';
    const BOTTOM_STATIC_BLOCK           = 'bottom_static_block';
    const SHOW_ON                       = 'show_on';
    const ENABLE_DIRECTION              = 'enable_direction';
    const PAGINATION                    = 'pagination';
    const META_TITLE                    = 'meta_title';
    const META_DESCRIPTION              = 'meta_description';
    const META_KEYWORDS                 = 'meta_keywords';

    /**
     * @return string
     */
    public function getZoom();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setZoom($value);

    /**
     * @return string
     */
    public function getStyle();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setStyle($value);

    /**
     * @return string
     */
    public function getCustomStyle();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCustomStyle($value);

    /**
     * @return string
     */
    public function getFilterRadius();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setFilterRadius($value);

    /**
     * @return string
     */
    public function getDefaultRadius();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setDefaultRadius($value);

    /**
     * @return string
     */
    public function getDistanceUnit();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setDistanceUnit($value);

    /**
     * @return string
     */
    public function getMarkerIcon();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setMarkerIcon($value);

    /**
     * @return string
     */
    public function getDataLocations();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setDataLocations($value);

    /**
     * @return string
     */
    public function getInfowindowTemplatePath();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setInfowindowTemplatePath($value);

    /**
     * @return string
     */
    public function getListTemplatePath();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setListTemplatePath($value);

    /**
     * @return string
     */
    public function getKmlInfowindowTemplatePath();

    /**
     * @param string $value
     *
     * @return string
     */
    public function setKmlInfowindowTemplatePath($value);

    /**
     * @return string
     */
    public function getKmlListTemplatePath();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setKmlListTemplatePath($value);

    /**
     * @return string
     */
    public function getIsFilter();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setIsFilter($value);

    /**
     * @return string
     */
    public function getIsFilterRadius();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setIsFilterRadius($value);

    /**
     * @return string
     */
    public function getLocationIdDetail();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setLocationIdDetail($value);

    /**
     * @return string
     */
    public function getUrlSuffix();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setUrlSuffix($value);

    /**
     * @return string
     */
    public function getKeyMap();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setKeyMap($value);

    /**
     * @return string
     */
    public function getRouter();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setRouter($value);

    /**
     * @return string
     */
    public function getIsDefaultStore();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setIsDefaultStore($value);

    /**
     * @return string
     */
    public function getDefaultLat();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setDefaultLat($value);

    /**
     * @return string
     */
    public function getDefaultLng();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setDefaultLng($value);

    /**
     * @return \Mageplaza\StoreLocator\Api\Data\LocationDataInterface[]
     */
    public function getLocationsData();

    /**
     * @param \Mageplaza\StoreLocator\Api\Data\LocationDataInterface[] $value
     *
     * @return $this
     */
    public function setLocationsData($value);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setTitle($value);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setDescription($value);

    /**
     * @return string
     */
    public function getUploadDefaultImage();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setUploadDefaultImage($value);

    /**
     * @return string
     */
    public function getUploadHeadImage();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setUploadHeadImage($value);

    /**
     * @return string
     */
    public function getUploadHeadIcon();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setUploadHeadIcon($value);

    /**
     * @return string
     */
    public function getBottomStaticBlock();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setBottomStaticBlock($value);

    /**
     * @return string
     */
    public function getShowOn();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setShowOn($value);

    /**
     * @return boolean
     */
    public function getEnableDirection();

    /**
     * @param boolean $value
     *
     * @return $this
     */
    public function setEnableDirection($value);

    /**
     * @return boolean
     */
    public function getPagination();

    /**
     * @param boolean $value
     *
     * @return $this
     */
    public function setPagination($value);

    /**
     * @return string
     */
    public function getMetaTitle();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setMetaTitle($value);

    /**
     * @return string
     */
    public function getMetaDescription();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setMetaDescription($value);

    /**
     * @return string
     */
    public function getMetaKeywords();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setMetaKeywords($value);
}
