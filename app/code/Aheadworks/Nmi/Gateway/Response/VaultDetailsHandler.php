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
namespace Aheadworks\Nmi\Gateway\Response;

use Aheadworks\Nmi\Gateway\Config\Config;
use Aheadworks\Nmi\Gateway\SubjectReader;
use Aheadworks\Nmi\Model\Api\Result\Response;
use Aheadworks\Nmi\Observer\DataAssignObserver;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Api\Data\OrderPaymentExtensionInterface;
use Magento\Sales\Api\Data\OrderPaymentExtensionInterfaceFactory;
use Magento\Vault\Api\Data\PaymentTokenFactoryInterface;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use Magento\Vault\Model\CreditCardTokenFactory;

/**
 * Class VaultDetailsHandler
 * @package Aheadworks\Nmi\Gateway\Response
 */
class VaultDetailsHandler implements HandlerInterface
{
    /**
     * @var CreditCardTokenFactory
     */
    private $paymentTokenFactory;

    /**
     * @var OrderPaymentExtensionInterfaceFactory
     */
    private $paymentExtensionFactory;

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param CreditCardTokenFactory $paymentTokenFactory
     * @param OrderPaymentExtensionInterfaceFactory $paymentExtensionFactory
     * @param SubjectReader $subjectReader
     * @param Json|null $serializer
     * @param Config $config
     * @throws \RuntimeException
     */
    public function __construct(
        CreditCardTokenFactory $paymentTokenFactory,
        OrderPaymentExtensionInterfaceFactory $paymentExtensionFactory,
        SubjectReader $subjectReader,
        Json $serializer,
        Config $config
    ) {
        $this->paymentTokenFactory = $paymentTokenFactory;
        $this->paymentExtensionFactory = $paymentExtensionFactory;
        $this->subjectReader = $subjectReader;
        $this->serializer = $serializer;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(array $handlingSubject, array $response)
    {
        $paymentDO = $this->subjectReader->readPayment($handlingSubject);
        $transactionResponse = $this->subjectReader->readTransactionResponse($response);
        $payment = $paymentDO->getPayment();

        // add vault payment token entity to extension attributes
        $paymentToken = $this->getVaultPaymentToken($transactionResponse, $payment);
        if (null !== $paymentToken) {
            $extensionAttributes = $this->getExtensionAttributes($payment);
            $extensionAttributes->setVaultPaymentToken($paymentToken);
        }
    }

    /**
     * Get vault payment token entity
     *
     * @param Response $transactionResponse
     * @param InfoInterface $payment
     * @return PaymentTokenInterface|null
     * @throws \Exception
     */
    protected function getVaultPaymentToken($transactionResponse, $payment)
    {
        $token = $transactionResponse->getCustomerVaultId();
        if (empty($token)) {
            return null;
        }

        $cardType = $payment->getAdditionalInformation(DataAssignObserver::CARD_TYPE) ? : '';
        $truncatedCard = $payment->getAdditionalInformation(DataAssignObserver::CARD_NUMBER) ? : '';
        $expDate = $payment->getAdditionalInformation(DataAssignObserver::CARD_EXPIRATION);
        if ($expDate) {
            $expiredInMonth = substr($expDate, 0, 2);
            $currentYear = (new \DateTime())->format('Y');
            $expiredInYear = substr($currentYear, 0, 2) . substr($expDate, 2, 4);
        } else {
            $expiredInMonth = '01';
            $expiredInYear = (new \DateTime())->modify('+1 year')->format('Y');
        }

        /** @var PaymentTokenInterface $paymentToken */
        $paymentToken = $this->paymentTokenFactory->create(PaymentTokenFactoryInterface::TOKEN_TYPE_CREDIT_CARD);
        $paymentToken->setGatewayToken($token);
        $paymentToken->setExpiresAt($this->getExpirationDate($expiredInMonth, $expiredInYear));

        $paymentToken->setTokenDetails($this->convertDetailsToJSON([
            'type' => $cardType,
            'typeCode' => $this->getCreditCardType($cardType),
            'lastCcNumber' => substr($truncatedCard, -4),
            'expirationDate' => $expiredInMonth . '/' . $expiredInYear
        ]));

        return $paymentToken;
    }

    /**
     * Calculate expiration date
     *
     * @param string $expiredInMonth
     * @param string $expiredInYear
     * @return string
     * @throws \Exception
     */
    private function getExpirationDate($expiredInMonth, $expiredInYear)
    {
        $expDate = new \DateTime(
            $expiredInYear
            . '-'
            . $expiredInMonth
            . '-'
            . '01'
            . ' '
            . '00:00:00',
            new \DateTimeZone('UTC')
        );
        $expDate->add(new \DateInterval('P1M'));
        return $expDate->format('Y-m-d 00:00:00');
    }

    /**
     * Convert payment token details to JSON
     * @param array $details
     * @return string
     */
    private function convertDetailsToJSON($details)
    {
        $json = $this->serializer->serialize($details);
        return $json ? $json : '{}';
    }

    /**
     * Retrieve type of credit card mapped from Nmi
     *
     * @param string $type
     * @return array
     */
    private function getCreditCardType($type)
    {
        $replaced = str_replace(' ', '-', strtolower((string)$type));
        $mapper = $this->config->getCctypesMapper();

        return $mapper[$replaced];
    }

    /**
     * Get payment extension attributes
     * @param InfoInterface $payment
     * @return OrderPaymentExtensionInterface
     */
    private function getExtensionAttributes(InfoInterface $payment)
    {
        $extensionAttributes = $payment->getExtensionAttributes();
        if (null === $extensionAttributes) {
            $extensionAttributes = $this->paymentExtensionFactory->create();
            $payment->setExtensionAttributes($extensionAttributes);
        }
        return $extensionAttributes;
    }
}
