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
namespace Aheadworks\Nmi\Block;

use Aheadworks\Nmi\ViewModel\QuoteTotals;
use Magento\Backend\Model\Session\Quote;
use Aheadworks\Nmi\Model\Ui\ConfigProvider;
use Magento\Framework\View\Element\Template\Context;
use Magento\Payment\Block\Form\Cc;
use Magento\Payment\Helper\Data;
use Magento\Payment\Model\Config;
use Magento\Payment\Model\MethodInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Block for representing the payment form
 */
class Form extends Cc
{
    /**
     * @var Quote
     */
    private $sessionQuote;

    /**
     * @var Data
     */
    private $paymentDataHelper;

    /**
     * @param Context $context
     * @param Config $paymentConfig
     * @param Quote $sessionQuote
     * @param Data $paymentDataHelper
     * @param QuoteTotals $quoteTotalsViewModel
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $paymentConfig,
        Quote $sessionQuote,
        Data $paymentDataHelper,
        QuoteTotals $quoteTotalsViewModel,
        array $data = []
    ) {
        $data['quote_totals_view_model'] = $quoteTotalsViewModel;
        parent::__construct($context, $paymentConfig, $data);
        $this->sessionQuote = $sessionQuote;
        $this->paymentDataHelper = $paymentDataHelper;
    }

    /**
     * Check if vault is enabled
     *
     * @return bool
     * @throws LocalizedException
     */
    public function isVaultEnabled()
    {
        $vaultPayment = $this->getVaultPayment();
        return $vaultPayment->isActive($this->sessionQuote->getStoreId());
    }

    /**
     * Get configured vault payment
     *
     * @return MethodInterface
     * @throws LocalizedException
     */
    private function getVaultPayment()
    {
        return $this->paymentDataHelper->getMethodInstance(ConfigProvider::CC_VAULT_CODE);
    }
}
