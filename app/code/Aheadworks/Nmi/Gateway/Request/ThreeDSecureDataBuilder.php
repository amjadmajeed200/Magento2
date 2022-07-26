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
namespace Aheadworks\Nmi\Gateway\Request;

use Aheadworks\Nmi\Gateway\Request\ThreeDSecure\OrderChecker;
use Aheadworks\Nmi\Gateway\SubjectReader;
use Aheadworks\Nmi\Model\Config;
use Aheadworks\Nmi\Observer\DataAssignObserver;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class ThreeDSecureDataBuilder
 */
class ThreeDSecureDataBuilder implements BuilderInterface
{
    const CARD_HOLDER_AUTH = 'cardholder_auth';
    const INVALID_CARD_HOLDER_AUTH = 'invalid_card_holder_auth';
    const THREE_DS_VERSION = 'three_ds_version';
    const DIRECTORE_SERVER_ID = 'directory_server_id';

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @var OrderChecker
     */
    private $orderChecker;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * ThreeDSecureDataBuilder constructor.
     * @param SubjectReader $subjectReader
     * @param OrderChecker $orderChecker
     * @param StoreManagerInterface $storeManager
     * @param Config $config
     */
    public function __construct(
        SubjectReader $subjectReader,
        OrderChecker $orderChecker,
        StoreManagerInterface $storeManager,
        Config $config
    ) {
        $this->subjectReader = $subjectReader;
        $this->orderChecker = $orderChecker;
        $this->storeManager = $storeManager;
        $this->config = $config;
    }

    /**
     * Builds ENV request
     *
     * @param array $buildSubject
     * @return array|string[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function build(array $buildSubject)
    {
        $paymentDO = $this->subjectReader->readPayment($buildSubject);
        $additionalData = $paymentDO->getPayment()->getAdditionalInformation();
        $storeId = $paymentDO->getOrder()->getStoreId();
        $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();
        $orderIncrementId = $paymentDO->getOrder()->getOrderIncrementId();

        if ($this->config->is3DSecureEnabled($websiteId)) {
            if ($this->orderChecker->isFirstOrder($orderIncrementId)) {
                $result = [
                    DataAssignObserver::CAVV => $additionalData[DataAssignObserver::CAVV],
                    DataAssignObserver::XID => $additionalData[DataAssignObserver::XID] ?? null,
                    DataAssignObserver::ECI => $additionalData[DataAssignObserver::ECI],
                    self::CARD_HOLDER_AUTH => $additionalData[DataAssignObserver::CARD_HOLDER_AUTH],
                    self::THREE_DS_VERSION => $additionalData[DataAssignObserver::THREE_DS_VERSION],
                    self::DIRECTORE_SERVER_ID => $additionalData[DataAssignObserver::DIRECTORY_SERVER_ID]
                ];
                $this->orderChecker->setFirstOrderIncrementId($orderIncrementId);
            } else {
                $result = [
                    self::CARD_HOLDER_AUTH => self::INVALID_CARD_HOLDER_AUTH,
                    self::THREE_DS_VERSION => '0.0.0'
                ];
            }
        }

        return $result ?? [];
    }
}
