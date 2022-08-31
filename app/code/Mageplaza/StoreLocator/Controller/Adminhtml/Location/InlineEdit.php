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

namespace Mageplaza\StoreLocator\Controller\Adminhtml\Location;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Mageplaza\StoreLocator\Model\Location;
use Mageplaza\StoreLocator\Model\LocationFactory;
use RuntimeException;

/**
 * Class InlineEdit
 * @package Mageplaza\StoreLocator\Controller\Adminhtml\Location
 */
class InlineEdit extends Action
{
    /**
     * JSON Factory
     *
     * @var JsonFactory
     */
    public $jsonFactory;

    /**
     * File Factory
     *
     * @var LocationFactory
     */
    public $locationFactory;

    /**
     * InlineEdit constructor.
     *
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param LocationFactory $locationFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        LocationFactory $locationFactory
    ) {
        $this->jsonFactory     = $jsonFactory;
        $this->locationFactory = $locationFactory;

        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson    = $this->jsonFactory->create();
        $error         = false;
        $messages      = [];
        $locationItems = $this->getRequest()->getParam('items', []);
        if (!(!empty($locationItems) && $this->getRequest()->getParam('isAjax'))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error'    => true,
            ]);
        }

        $key        = array_keys($locationItems);
        $locationId = !empty($key) ? (int) $key[0] : '';
        /** @var Location $location */
        $location = $this->locationFactory->create()->load($locationId);
        try {
            $locationData = $locationItems[$locationId];
            $location->addData($locationData)->save();
        } catch (RuntimeException $e) {
            $messages[] = $this->getErrorWithLocationId($location, $e->getMessage());
            $error      = true;
        } catch (Exception $e) {
            $messages[] = $this->getErrorWithLocationId(
                $location,
                __('Something went wrong while saving the Location. %1', $e->getMessage())
            );
            $error      = true;
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error'    => $error
        ]);
    }

    /**
     * Add Location id to error message
     *
     * @param Location $location
     * @param string $errorText
     *
     * @return string
     */
    public function getErrorWithLocationId(Location $location, $errorText)
    {
        return '[Location ID: ' . $location->getId() . '] ' . $errorText;
    }
}
