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
namespace Aheadworks\Nmi\Model\Multishipping;

use Aheadworks\Nmi\Model\Ui\ConfigProvider;
use Aheadworks\Nmi\Observer\DataAssignObserver;
use Magento\Multishipping\Model\Checkout\Type\Multishipping\PlaceOrderInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderPaymentExtensionInterface;
use Magento\Sales\Api\Data\OrderPaymentExtensionInterfaceFactory;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Vault\Api\Data\PaymentTokenInterface;

/**
 * Class PlaceOrder
 *
 * @package Aheadworks\Nmi\Model\Multishipping
 */
class PlaceOrder implements PlaceOrderInterface
{
    /**
     * @var OrderManagementInterface
     */
    private $orderManagement;

    /**
     * @var OrderPaymentExtensionInterfaceFactory
     */
    private $paymentExtensionFactory;

    /**
     * @param OrderManagementInterface $orderManagement
     * @param OrderPaymentExtensionInterfaceFactory $paymentExtensionFactory
     */
    public function __construct(
        OrderManagementInterface $orderManagement,
        OrderPaymentExtensionInterfaceFactory $paymentExtensionFactory
    ) {
        $this->orderManagement = $orderManagement;
        $this->paymentExtensionFactory = $paymentExtensionFactory;
    }

    /**
     * @inheritdoc
     */
    public function place(array $orderList): array
    {
        if (empty($orderList)) {
            return [];
        }

        $errorList = [];
        $firstOrder = $this->orderManagement->place(array_shift($orderList));
        $paymentToken = $this->getPaymentToken($firstOrder);

        foreach ($orderList as $order) {
            try {
                /** @var OrderInterface $order */
                $orderPayment = $order->getPayment();
                $this->setVaultPayment($orderPayment, $paymentToken);
                $this->orderManagement->place($order);
            } catch (\Exception $e) {
                $incrementId = $order->getIncrementId();
                $errorList[$incrementId] = $e;
            }
        }

        return $errorList;
    }

    /**
     * Sets vault payment method
     *
     * @param OrderPaymentInterface $orderPayment
     * @param PaymentTokenInterface $paymentToken
     */
    private function setVaultPayment(OrderPaymentInterface $orderPayment, PaymentTokenInterface $paymentToken)
    {
        $vaultMethod = $this->getVaultPaymentMethod(
            $orderPayment->getMethod()
        );
        $orderPayment->setMethod($vaultMethod);
        $orderPayment->setAdditionalInformation(
            DataAssignObserver::PAYMENT_METHOD_TOKEN,
            $paymentToken->getPublicHash()
        );
        $orderPayment->setAdditionalInformation(
            PaymentTokenInterface::PUBLIC_HASH,
            $paymentToken->getPublicHash()
        );
        $orderPayment->setAdditionalInformation(
            DataAssignObserver::IS_VAULT,
            1
        );
        $orderPayment->setAdditionalInformation(
            PaymentTokenInterface::CUSTOMER_ID,
            $paymentToken->getCustomerId()
        );
    }

    /**
     * Returns vault payment method
     *
     * For placing sequence of orders, we need to replace the original method on the vault method.
     *
     * @param string $method
     * @return string
     */
    private function getVaultPaymentMethod(string $method)
    {
        $vaultPaymentMap = [
            ConfigProvider::CODE => ConfigProvider::CC_VAULT_CODE
        ];

        return $vaultPaymentMap[$method] ?? $method;
    }

    /**
     * Returns payment token
     *
     * @param OrderInterface $order
     * @return PaymentTokenInterface
     * @throws \BadMethodCallException
     */
    private function getPaymentToken(OrderInterface $order)
    {
        $orderPayment = $order->getPayment();
        $extensionAttributes = $this->getExtensionAttributes($orderPayment);
        $paymentToken = $extensionAttributes->getVaultPaymentToken();

        if ($paymentToken === null) {
            throw new \BadMethodCallException('Vault Payment Token should be defined for placed order payment.');
        }

        return $paymentToken;
    }

    /**
     * Gets payment extension attributes
     *
     * @param OrderPaymentInterface $payment
     * @return OrderPaymentExtensionInterface
     */
    private function getExtensionAttributes(OrderPaymentInterface $payment)
    {
        $extensionAttributes = $payment->getExtensionAttributes();
        if (null === $extensionAttributes) {
            $extensionAttributes = $this->paymentExtensionFactory->create();
            $payment->setExtensionAttributes($extensionAttributes);
        }

        return $extensionAttributes;
    }
}
