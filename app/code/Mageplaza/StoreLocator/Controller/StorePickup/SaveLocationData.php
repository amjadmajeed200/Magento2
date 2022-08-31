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

namespace Mageplaza\StoreLocator\Controller\StorePickup;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class SaveLocationData
 * @package Mageplaza\StoreLocator\Controller\StorePickup
 */
class SaveLocationData extends Action
{
    /**
     * @var PageFactory
     */
    protected $_checkoutSession;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * SaveLocationData constructor.
     *
     * @param Context $context
     * @param Session $checkoutSession
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        JsonFactory $resultJsonFactory
    ) {
        $this->_checkoutSession  = $checkoutSession;
        $this->resultJsonFactory = $resultJsonFactory;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Json|ResultInterface|null
     */
    public function execute()
    {
        $locationId = $this->_request->getParam('locationId');
        $timePickup = $this->_request->getParam('timePickup');
        $type       = $this->getRequest()->getParam('type');

        if ($type === 'set') {
            $this->_checkoutSession->setLocationIdSelected($locationId);
            $this->_checkoutSession->setPickupTime($timePickup);
        }

        if ($type === 'get') {
            $result     = $this->resultJsonFactory->create();
            $locationId = $this->_checkoutSession->getLocationIdSelected();

            $result->setData(['locationId' => $locationId]);

            return $result;
        }

        return null;
    }
}
