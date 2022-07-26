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
namespace Aheadworks\Nmi\Plugin\Sales\Block\Adminhtml\Order\Invoice\Create;

use Magento\Sales\Block\Adminhtml\Order\Invoice\Create\Items;
use Aheadworks\Nmi\Block\Modal\ConfirmClickAction;
use Aheadworks\Nmi\Helper\NmiPayment as PaymentHelper;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\UrlInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Aheadworks\Nmi\Model\Ui\Modal\ConfirmClickAction as ConfirmClickActionModel;
use Aheadworks\Nmi\Model\Ui\Modal\ConfirmClickActionFactory as ConfirmClickActionModelFactory;

/**
 * Class ItemsPlugin
 * @package Aheadworks\Nmi\Plugin\Sales\Block\Adminhtml\Order\Invoice\Create
 */
class ItemsPlugin
{
    /**
     * @var PaymentHelper
     */
    private $paymentHelper;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var ConfirmClickActionModelFactory
     */
    private $confirmClickActionModelFactory;

    /**
     * @param PaymentHelper $paymentHelper
     * @param UrlInterface $urlBuilder
     * @param ConfirmClickActionModelFactory $confirmClickActionModelFactory
     */
    public function __construct(
        PaymentHelper $paymentHelper,
        UrlInterface $urlBuilder,
        ConfirmClickActionModelFactory $confirmClickActionModelFactory
    ) {
        $this->paymentHelper = $paymentHelper;
        $this->urlBuilder = $urlBuilder;
        $this->confirmClickActionModelFactory = $confirmClickActionModelFactory;
    }

    /**
     * Add confirmation popup to invoice update qty button for nmi payment without vault token
     *
     * @param Items $subject
     * @param \Closure $proceed
     * @return string
     */
    public function aroundGetUpdateButtonHtml(
        Items $subject,
        \Closure $proceed
    ) {
        /** @var Order $order */
        $order = $subject->getOrder();
        /** @var OrderPaymentInterface $payment */
        $payment = $order->getPayment();

        if ($payment
            && $this->paymentHelper->isNmiPayment($payment)
            && empty($this->paymentHelper->getVaultPaymentToken($payment))
        ) {
            return $this->getUpdateButtonWithConfirmationHtml($subject);
        }

        return $proceed();
    }

    /**
     * Returns update qty button with confirmation
     *
     * @param Items $subject
     * @return string|null
     */
    private function getUpdateButtonWithConfirmationHtml($subject)
    {
        $result = null;

        $updateButtonAlias = 'update_button';
        $updateButtonConfirmationAlias = 'update_button_confirmation';

        /** @var AbstractBlock|bool $updateButton */
        $updateButton = $subject->getChildBlock($updateButtonAlias);

        if ($updateButton) {
            $onclick = $updateButton->getOnclick();
            $updateButton->setOnclick(null);

            $buttonIdentifierClass = 'aw-update-qty-with-confirmation';
            $updateButton->setClass($updateButton->getClass() . ' ' . $buttonIdentifierClass);

            /** @var ConfirmClickActionModel $confirmClickActionModel */
            $confirmClickActionModel = $this->confirmClickActionModelFactory->create();

            $confirmClickActionModel
                ->setDomElementIdentifier('.' . $buttonIdentifierClass)
                ->setTitle(__('WARNING!'))
                ->setActionAcceptText(__('Return And Edit'))
                ->setActionDismiss($onclick)
                ->setActionDismissText(__('Create Partial Invoice Anyway'))
                ->setContent($this->getModalContent());

            $subject->addChild(
                $updateButtonConfirmationAlias,
                ConfirmClickAction::class,
                [
                    'modal_data' => $confirmClickActionModel,
                ]
            );

            $result = $subject->getChildHtml($updateButtonAlias)
                . $subject->getChildHtml($updateButtonConfirmationAlias);
        }

        return $result;
    }

    /**
     * Returns modal content
     *
     * @return \Magento\Framework\Phrase
     */
    private function getModalContent()
    {
        $enableVaultUrl = $this->urlBuilder->getUrl(
            'adminhtml/system_config/edit',
            [
                'section' => 'payment',
                'group' => 'aw_nmi',
                'field' => 'active'
            ]
        );
        $enableVaultLink = '<a target="_blank" href="' . $enableVaultUrl . '">'
            . __('Enable Customer Vault And Partial Invoices')
            . '</a>';
        $modalContent = __('Not possible to invoice this order online partially. If you continue, you won’t be able '
            . 'to create another online invoice for this order later. To enable partial invoices for '
            . 'the future orders, set the option %1 to Yes. <br>Note, if you proceed, you still will '
            . 'be able to invoice the remainder of this order offline.', $enableVaultLink);

        return $modalContent;
    }
}
