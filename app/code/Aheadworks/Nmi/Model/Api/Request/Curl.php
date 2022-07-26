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
namespace Aheadworks\Nmi\Model\Api\Request;

use Aheadworks\Nmi\Model\Api\Result\Response;
use Aheadworks\Nmi\Model\Api\Result\ResponseFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\Adapter\CurlFactory as FrameworkCurlFactory;

/**
 * Class Curl
 * @package Aheadworks\Nmi\Model\Api
 */
class Curl
{
    /**
     * Connection timeout
     */
    const CONNECTION_TIMEOUT = 60;

    /**
     * @var FrameworkCurlFactory
     */
    private $curlFactory;

    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    /**
     * @param FrameworkCurlFactory $curlFactory
     * @param ResponseFactory $responseFactory
     */
    public function __construct(
        FrameworkCurlFactory $curlFactory,
        ResponseFactory $responseFactory
    ) {
        $this->curlFactory = $curlFactory;
        $this->responseFactory = $responseFactory;
    }

    /**
     * Perform api request
     *
     * @param string $url
     * @param array $params
     * @param string $method
     * @return Response
     * @throws LocalizedException
     * @throws \Exception
     */
    public function request($url, array $params, $method = \Zend_Http_Client::POST)
    {
        $curl = $this->curlFactory->create();
        $curl->setConfig(['timeout' => self::CONNECTION_TIMEOUT, 'header' => false, 'verifypeer' => false]);
        if ($method == \Zend_Http_Client::DELETE) {
            $curl->addOption(CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        $curl->write(
            $method,
            $url,
            '1.1',
            $this->getHeaders(),
            $params
        );

        try {
            $responseData = $curl->read();
        } catch (\Exception $e) {
            $curl->close();
            throw new LocalizedException(__('Unable to perform request.'));
        }
        $curl->close();

        $responseData = $this->responseFactory->create($responseData);

        return $responseData;
    }

    /**
     * Get http headers
     *
     * @return array
     */
    private function getHeaders()
    {
        $headers = [];
        $headersData = [];

        foreach ($headersData as $name => $value) {
            $headers[] = $name . ': ' . $value;
        }

        return $headers;
    }
}
