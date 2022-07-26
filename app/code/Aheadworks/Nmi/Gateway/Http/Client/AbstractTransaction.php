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
namespace Aheadworks\Nmi\Gateway\Http\Client;

use Aheadworks\Nmi\Model\Api\Result\Response;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\ClientException;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Payment\Model\Method\Logger;
use Aheadworks\Nmi\Model\Api\RequestManager;
use Psr\Log\LoggerInterface;

/**
 * Class AbstractTransaction
 * @package Aheadworks\Nmi\Gateway\Http\Client
 */
abstract class AbstractTransaction implements ClientInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Logger
     */
    protected $customLogger;

    /**
     * @var RequestManager
     */
    protected $requestManager;

    /**
     * @param LoggerInterface $logger
     * @param Logger $customLogger
     * @param RequestManager $requestManager
     */
    public function __construct(
        LoggerInterface $logger,
        Logger $customLogger,
        RequestManager $requestManager
    ) {
        $this->logger = $logger;
        $this->customLogger = $customLogger;
        $this->requestManager = $requestManager;
    }

    /**
     * {@inheritdoc}
     */
    public function placeRequest(TransferInterface $transferObject)
    {
        $data = $transferObject->getBody();
        $log = [
            'request' => $data,
            'client' => static::class
        ];
        $response['object'] = [];

        try {
            $storeId = $data['store_id'] ?? null;
            unset($data['store_id']);

            $response['object'] = $this->process($data, $storeId);
        } catch (\Exception $e) {
            $message = __($e->getMessage() ?: 'Sorry, but something went wrong');
            $this->logger->critical($message);
            throw new ClientException($message);
        } finally {
            $log['response'] = (array) $response['object'];
            $this->customLogger->debug($log);
        }

        return $response;
    }

    /**
     * Process http request
     *
     * @param array $data
     * @param int|null $storeId
     * @return Response
     */
    abstract protected function process(array $data, $storeId = null);
}
