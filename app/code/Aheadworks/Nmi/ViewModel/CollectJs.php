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
namespace Aheadworks\Nmi\ViewModel;

use Aheadworks\Nmi\Model\Config;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\Nmi\Model\Url;
use Magento\Framework\Escaper;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class CollectJs
 * @package Aheadworks\Nmi\ViewModel
 */
class CollectJs implements ArgumentInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Url
     */
    private $url;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param Config $config
     * @param Url $url
     * @param Escaper $escaper
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Config $config,
        Url $url,
        Escaper $escaper,
        StoreManagerInterface $storeManager
    ) {
        $this->config = $config;
        $this->url = $url;
        $this->escaper = $escaper;
        $this->storeManager = $storeManager;
    }

    /**
     * Retrieve tokenization key
     *
     * @return string
     */
    public function getTokenizationKey()
    {
        return $this->config->getTokenizationKey($this->storeManager->getWebsite()->getId());
    }

    /**
     * Retrieve collect js base url
     *
     * @return string
     */
    public function getCollectJsBaseUrl()
    {
        return $this->escaper->escapeUrl($this->url->getCollectJsBaseUrl($this->storeManager->getWebsite()->getId()));
    }

    /**
     * Retrieve gateway js base url
     *
     * @return string
     */
    public function getGatewayJsBaseUrl()
    {
        return $this->escaper->escapeUrl($this->url->getGatewayJsBaseUrl($this->storeManager->getWebsite()->getId()));
    }
}
