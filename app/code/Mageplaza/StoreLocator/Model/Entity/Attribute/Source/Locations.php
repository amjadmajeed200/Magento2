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
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\StoreLocator\Model\Entity\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageplaza\StoreLocator\Block\Frontend;
use Mageplaza\StoreLocator\Model\ResourceModel\Location\CollectionFactory;

/**
 * Class Locations
 * @package Mageplaza\StoreLocator\Model\Entity\Attribute\Source
 */
class Locations extends AbstractSource
{
    /**
     * @var CollectionFactory
     */
    protected $_locationColFactory;

    /**
     * @var Frontend
     */
    protected $_frontend;

    /**
     * Locations constructor.
     *
     * @param Frontend $frontend
     * @param CollectionFactory $locationColFactory
     */
    public function __construct(
        Frontend $frontend,
        CollectionFactory $locationColFactory
    ) {
        $this->_frontend           = $frontend;
        $this->_locationColFactory = $locationColFactory;
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    public function getAllOptions()
    {
        $arrayList        = [];
        $locations        = $this->_locationColFactory->create()->addFieldToFilter('status', 1);
        $locationsByStore = $this->_frontend->filterLocation($locations);

        foreach ($locationsByStore as $location) {
            $key                           = $location->getUrlKey() . '-' . $location->getId();
            $arrayList[$location->getId()] = [
                'label' => $location->getName(),
                'value' => $key
            ];
        }

        return $arrayList;
    }
}
