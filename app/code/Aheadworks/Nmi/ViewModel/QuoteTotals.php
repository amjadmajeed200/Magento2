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

use Magento\Backend\Model\Session\Quote;

/**
 * Class QuoteTotals
 */
class QuoteTotals
{
    /**
     * @var Quote
     */
    private $sessionQuote;

    /**
     * QuoteTotals constructor.
     * @param Quote $sessionQuote
     */
    public function __construct(
        Quote $sessionQuote
    ) {
        $this->sessionQuote = $sessionQuote;
    }

    /**
     * Retrieve quote model object
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        return $this->sessionQuote->getQuote();
    }

    /**
     * Get totals
     *
     * @return array
     */
    public function getGrandTotal()
    {
        $this->getQuote()->collectTotals();
        if ($this->getQuote()->isVirtual()) {
            $totals = $this->getQuote()->getBillingAddress()->getTotals();
        } else {
            $totals = $this->getQuote()->getShippingAddress()->getTotals();
        }

        return $totals['grand_total']->getValue();
    }
}
