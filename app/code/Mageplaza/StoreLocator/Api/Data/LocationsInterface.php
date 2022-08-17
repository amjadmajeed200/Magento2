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
 * Interface LocationsInterface
 * @package Mageplaza\StoreLocator\Api\Data
 */
interface LocationsInterface
{
    const LOCATION_ID             = 'location_id';
    const NAME                    = 'name';
    const STATUS                  = 'status';
    const DESCRIPTION             = 'description';
    const STORE_IDS               = 'store_ids';
    const CITY                    = 'city';
    const COUNTRY                 = 'country';
    const STREET                  = 'street';
    const STATE_PROVINCE          = 'state_province';
    const POSTAL_CODE             = 'postal_code';
    const URL_KEY                 = 'url_key';
    const LATITUDE                = 'latitude';
    const LONGITUDE               = 'longitude';
    const PHONE_ONE               = 'phone_one';
    const PHONE_TWO               = 'phone_two';
    const WEBSITE                 = 'website';
    const FACEBOOK                = 'facebook';
    const TWITTER                 = 'twitter';
    const IS_CONFIG_WEBSITE       = 'is_config_website';
    const FAX                     = 'fax';
    const EMAIL                   = 'email';
    const IMAGES                  = 'images';
    const OPERATION_MON           = 'operation_mon';
    const OPERATION_TUE           = 'operation_tue';
    const OPERATION_WED           = 'operation_wed';
    const OPERATION_THU           = 'operation_thu';
    const OPERATION_FRI           = 'operation_fri';
    const OPERATION_SAT           = 'operation_sat';
    const OPERATION_SUN           = 'operation_sun';
    const IS_DEFAULT_STORE        = 'is_default_store';
    const TIME_ZONE               = 'time_zone';
    const IS_CONFIG_TIME_ZONE     = 'is_config_time_zone';
    const SORT_ORDER              = 'sort_order';
    const UPDATED_AT              = 'updated_at';
    const CREATED_AT              = 'created_at';
    const PRODUCT_IDS             = 'product_ids';
    const IS_SHOW_PRODUCT_PAGE    = 'is_show_product_page';
    const IS_SELECTED_ALL_PRODUCT = 'is_selected_all_product';

    /**
     * @return int
     */
    public function getLocationId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setLocationId($value);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setName($value);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setStatus($value);

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
    public function getStoreIds();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setStoreIds($value);

    /**
     * @return string
     */
    public function getCity();

    /**
     * @param int $value
     *
     * @return string
     */
    public function setCity($value);

    /**
     * @return string
     */
    public function getCountry();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCountry($value);

    /**
     * @return string
     */
    public function getStreet();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setStreet($value);

    /**
     * @return string
     */
    public function getStateProvince();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setStateProvince($value);

    /**
     * @return int
     */
    public function getPostalCode();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setPostalCode($value);

    /**
     * @return string
     */
    public function getUrlKey();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setUrlKey($value);

    /**
     * @return string
     */
    public function getLatitude();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setLatitude($value);

    /**
     * @return string
     */
    public function getLongitude();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setLongitude($value);

    /**
     * @return string
     */
    public function getPhoneOne();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setPhoneOne($value);

    /**
     * @return string
     */
    public function getPhoneTwo();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setPhoneTwo($value);

    /**
     * @return string
     */
    public function getWebsite();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setWebsite($value);

    /**
     * @return string
     */
    public function getFacebook();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setFacebook($value);

    /**
     * @return string
     */
    public function getTwitter();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setTwitter($value);

    /**
     * @return int
     */
    public function getIsConfigWebsite();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setIsConfigWebsite($value);

    /**
     * @return string
     */
    public function getFax();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setFax($value);

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setEmail($value);

    /**
     * @return string
     */
    public function getImages();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setImages($value);

    /**
     * @return string
     */
    public function getOperationMon();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setOperationMon($value);

    /**
     * @return string
     */
    public function getOperationTue();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setOperationTue($value);

    /**
     * @return string
     */
    public function getOperationWed();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setOperationWed($value);

    /**
     * @return string
     */
    public function getOperationThu();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setOperationThu($value);

    /**
     * @return string
     */
    public function getOperationFri();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setOperationFri($value);

    /**
     * @return string
     */
    public function getOperationSat();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setOperationSat($value);

    /**
     * @return string
     */
    public function getOperationSun();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setOperationSun($value);

    /**
     * @return int
     */
    public function getIsDefaultStore();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setIsDefaultStore($value);

    /**
     * @return string
     */
    public function getTimeZone();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setTimeZone($value);

    /**
     * @return int
     */
    public function getIsConfigTimezone();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setIsConfigTimezone($value);

    /**
     * @return int
     */
    public function getSortOrder();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setSortOrder($value);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCreatedAt($value);

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setUpdatedAt($value);

    /**
     * @return string
     */
    public function getProductIds();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setProductIds($value);

    /**
     * @return int
     */
    public function getIsShowProductPage();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setIsShowProductPage($value);

    /**
     * @return int
     */
    public function getIsSelectedAllProduct();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setIsSelectedAllProduct($value);
}
