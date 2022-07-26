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
namespace Aheadworks\Nmi\Model\Api\Result;

use Magento\Framework\ObjectManagerInterface;

/**
 * Class ResponseFactory
 * @package Aheadworks\Nmi\Model\Api\Result
 */
class ResponseFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * Create response object
     *
     * @param string $responseAsString
     * @return Response
     * @throws \Exception
     */
    public function create($responseAsString)
    {
        $response = $this->prepareResponseData($responseAsString);

        return $this->objectManager->create(Response::class, ['data' => $response]);
    }

    /**
     * Prepare response data
     *
     * @param string $response
     * @return array
     * @throws \Exception
     */
    private function prepareResponseData($response)
    {
        $response = explode('&', (string)$response);
        $preparedResponseData = [];
        foreach ($response as $value) {
            $keyValue = explode('=', $value);
            $preparedResponseData[$keyValue[0]] = $keyValue[1];
        }

        return $preparedResponseData;
    }
}
