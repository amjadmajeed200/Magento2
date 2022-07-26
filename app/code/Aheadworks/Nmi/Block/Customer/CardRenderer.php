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
namespace Aheadworks\Nmi\Block\Customer;

use Magento\Vault\Block\AbstractCardRenderer;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use Aheadworks\Nmi\Model\Ui\ConfigProvider;

/**
 * Class CardRenderer
 * @package Aheadworks\Nmi\Block\Customer
 */
class CardRenderer extends AbstractCardRenderer
{
    /**
     * Can render specified token
     *
     * @param PaymentTokenInterface $token
     * @return boolean
     */
    public function canRender(PaymentTokenInterface $token)
    {
        return $token->getPaymentMethodCode() === ConfigProvider::CODE;
    }

    /**
     * Retrieve last 4 digits
     *
     * @return string
     */
    public function getNumberLast4Digits()
    {
        return $this->getTokenDetails()['lastCcNumber'];
    }

    /**
     * Retrieve expiration date
     *
     * @return string
     */
    public function getExpDate()
    {
        return $this->getTokenDetails()['expirationDate'];
    }

    /**
     * Retrieve icon url
     *
     * @return string
     */
    public function getIconUrl()
    {
        return $this->getIconForType($this->getTokenDetails()['typeCode'])['url'];
    }

    /**
     * Retrieve icon height
     *
     * @return int
     */
    public function getIconHeight()
    {
        return $this->getIconForType($this->getTokenDetails()['typeCode'])['height'];
    }

    /**
     * Retrieve icon width
     *
     * @return int
     */
    public function getIconWidth()
    {
        return $this->getIconForType($this->getTokenDetails()['typeCode'])['width'];
    }
}
