<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_StoreLocator
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\StoreLocator\Plugin\Model;

use Magento\Framework\Session\SessionManagerInterface;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Mageplaza\StoreLocator\Helper\Data as HelperData;

/**
 * Class ConfigData
 * @package Mageplaza\StoreLocator\Plugin\Model
 */
class ConfigData
{
    /**
     * @var helperData
     */
    protected $_helperData;

    /**
     * @var coreSession
     */
    protected $_coreSession;

    /**
     * ConfigData constructor.
     *
     * @param HelperData $helperData
     * @param SessionManagerInterface $coreSession
     */
    public function __construct(
        HelperData $helperData,
        SessionManagerInterface $coreSession
    ) {
        $this->_helperData  = $helperData;
        $this->_coreSession = $coreSession;
    }

    /**
     * @param AbstractCarrier $subject
     * @param $field
     *
     * @return array
     */
    public function beforeGetConfigData(AbstractCarrier $subject, $field)
    {
        if ($subject->getCarrierCode() === 'mpstorepickup' && !$this->_helperData->checkEnabledModule()) {
            $this->_coreSession->start();
            $this->_coreSession->setEnabled(0);
        }

        return [$field];
    }
}
