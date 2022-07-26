<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://aheadworks.com/end-user-license-agreement/
 *
 * @package    Nmi
 * @version    1.3.1
 * @copyright  Copyright (c) 2022 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Nmi\Model;

use Aheadworks\Nmi\Model\Config;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Url
 * @package Aheadworks\Nmi\Model
 */
class Url
{
    /**
     * Api relative path
     */
    const API_RELATIVE_PATH = '/api/transact.php';

    /**
     * Collect js relative path
     */
    const COLLECT_JS_RELATIVE_PATH = '/token/Collect.js';

    /**
     * Gateway js relative path
     */
    const GATEWAY_JS_RELATIVE_PATH = '/js/v1/Gateway.js';

    /**
     * @var Config
     */
    private $config;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager
    ) {
        $this->config = $config;
        $this->storeManager = $storeManager;
    }

    /**
     * Returns api base url
     *
     * @param int|null $websiteId
     * @return string
     */
    public function getApiBaseUrl($websiteId = null)
    {
        return $this->config->getApiEndpointUrl($websiteId) . self::API_RELATIVE_PATH;
    }

    /**
     * Returns collect js base url
     *
     * @param int|null $websiteId
     * @return string
     */
    public function getCollectJsBaseUrl($websiteId = null)
    {
        return $this->config->getApiEndpointUrl($websiteId) . self::COLLECT_JS_RELATIVE_PATH;
    }

    /**
     * Returns gateway js base url
     *
     * @param int|null $websiteId
     * @return string
     */
    public function getGatewayJsBaseUrl($websiteId = null)
    {
        return $this->config->getApiEndpointUrl($websiteId) . self::GATEWAY_JS_RELATIVE_PATH;
    }
}
