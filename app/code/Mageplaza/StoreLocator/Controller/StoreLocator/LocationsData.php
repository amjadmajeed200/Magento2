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

namespace Mageplaza\StoreLocator\Controller\StoreLocator;

use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Mageplaza\StoreLocator\Block\Frontend;
use Mageplaza\StoreLocator\Helper\Data as HelperData;

/**
 * Class LocationsData
 * @package Mageplaza\StoreLocator\Controller\StoreLocator
 */
class LocationsData extends Action
{
    /**
     * @var Frontend
     */
    protected $_frontEnd;

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * @var JsonFactory
     */
    protected $jsonResultFactory;

    /**
     * LocationsData constructor.
     *
     * @param Context $context
     * @param HelperData $helperData
     * @param JsonFactory $jsonResultFactory
     * @param Frontend $frontend
     */
    public function __construct(
        Context $context,
        HelperData $helperData,
        JsonFactory $jsonResultFactory,
        Frontend $frontend
    ) {
        $this->_helperData       = $helperData;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->_frontEnd         = $frontend;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Json|ResultInterface
     * @throws Exception
     */
    public function execute()
    {
        /** @var Json $result */
        $result       = $this->jsonResultFactory->create();
        $locations    = $this->_frontEnd->getLocationList();
        $locationData = $this->_frontEnd->getDataLocations($locations);
        $result->setData($this->_helperData->jsDecode($locationData));

        return $result;
    }
}
