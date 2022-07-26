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
namespace Aheadworks\Nmi\Model\Api;

use Aheadworks\Nmi\Model\Api\Request\Curl;
use Aheadworks\Nmi\Model\Api\Result\Response;
use Aheadworks\Nmi\Model\Url;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class RequestManager
 * @package Aheadworks\Nmi\Model\Api
 */
class RequestManager
{
    /**
     * @var Url
     */
    private $url;

    /**
     * @var Curl
     */
    private $curlRequest;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param Curl $curlRequest
     * @param Url $url
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Curl $curlRequest,
        Url $url,
        StoreManagerInterface $storeManager
    ) {
        $this->curlRequest = $curlRequest;
        $this->url= $url;
        $this->storeManager = $storeManager;
    }

    /**
     * Pay request
     *
     * @param array $data
     * @return Response
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function pay(array $data)
    {
        $apiBaseUrl = $this->url->getApiBaseUrl($this->storeManager->getWebsite()->getId());
        return $this->curlRequest->request($apiBaseUrl, $data);
    }
}
