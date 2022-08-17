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

namespace Mageplaza\StoreLocator\Block\Product\View;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Mageplaza\StoreLocator\Block\Frontend;
use Mageplaza\StoreLocator\Model\ResourceModel\Location\CollectionFactory;

/**
 * Class AvailableStore
 * @package Mageplaza\StoreLocator\Block\Product\View
 */
class AvailableStore extends AbstractProduct
{
    /**
     * @var Frontend
     */
    protected $_frontend;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * AvailableStore constructor.
     *
     * @param Context $context
     * @param Frontend $frontend
     * @param CollectionFactory $collectionFactory
     * @param ManagerInterface $messageManager
     * @param array $data
     */
    public function __construct(
        Context $context,
        Frontend $frontend,
        CollectionFactory $collectionFactory,
        ManagerInterface $messageManager,
        array $data = []
    ) {
        $this->_frontend         = $frontend;
        $this->collectionFactory = $collectionFactory;
        $this->messageManager    = $messageManager;

        parent::__construct($context, $data);
    }

    /**
     * @return array
     */
    public function getAppliedLocationIds()
    {
        $locationIds         = [];
        $pickupLocationsData = $this->getProduct()->getData('mp_pickup_locations');

        foreach (explode(',', $pickupLocationsData) as $item) {
            $params        = explode('-', $item);
            $locationIds[] = array_pop($params);
        }

        return $locationIds;
    }

    /**
     * @param $locationIds
     *
     * @return array
     */
    public function getLocationsData($locationIds)
    {
        $data       = [];
        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('location_id', $locationIds)
            ->addFieldToFilter('status', 1);

        try {
            $locations = $this->_frontend->filterLocation($collection);
            foreach ($locations as $location) {
                if ($location->getIsShowProductPage()) {
                    $data[] = [
                        'name'    => $location->getName(),
                        'address' => $location->getStreet() . ' ' .
                            $location->getStateProvince() . ', ' .
                            $location->getCity() . ', ' .
                            $location->getCountry()
                    ];
                }
            }
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $data;
    }
}
