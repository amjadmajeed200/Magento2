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

namespace Mageplaza\StoreLocator\Controller\Adminhtml\Holiday;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Js;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Mageplaza\StoreLocator\Controller\Adminhtml\Holiday;
use Mageplaza\StoreLocator\Model\HolidayFactory;
use RuntimeException;

/**
 * Class Save
 * @package Mageplaza\StoreLocator\Controller\Adminhtml\Holiday
 */
class Save extends Holiday
{
    /**
     * JS helper
     *
     * @var Js
     */
    public $jsHelper;

    /**
     * @var DateTime
     */
    public $date;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param Js $jsHelper
     * @param HolidayFactory $holidayFactory
     * @param DateTime $date
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Js $jsHelper,
        HolidayFactory $holidayFactory,
        DateTime $date
    ) {
        $this->jsHelper = $jsHelper;
        $this->date     = $date;

        parent::__construct($holidayFactory, $registry, $context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data = $this->getRequest()->getPostValue()) {
            /** @var \Mageplaza\StoreLocator\Model\Holiday $holiday */
            $holiday = $this->initHoliday();
            $this->prepareData($holiday, $data);

            $this->_eventManager->dispatch(
                'mageplaza_storelocator_holiday_prepare_save',
                ['holiday' => $holiday, 'request' => $this->getRequest()]
            );

            try {
                $holiday->save();

                $this->messageManager->addSuccessMessage(__('The holiday has been saved.'));
                $this->_getSession()->setData('mageplaza_storelocator_holiday_data', false);

                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath('mpstorelocator/*/edit', ['id' => $holiday->getId(), '_current' => true]);
                } else {
                    $resultRedirect->setPath('mpstorelocator/*/');
                }

                return $resultRedirect;
            } catch (RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Holiday.'));
            }
            $this->_getSession()->setData('mageplaza_storelocator_holiday_data', $data);
            $resultRedirect->setPath('mpstorelocator/*/edit', ['id' => $holiday->getId(), '_current' => true]);

            return $resultRedirect;
        }
        $resultRedirect->setPath('mpstorelocator/*/');

        return $resultRedirect;
    }

    /**
     * @param \Mageplaza\StoreLocator\Model\Holiday $holiday
     * @param array $data
     *
     * @return $this
     */
    protected function prepareData($holiday, $data = [])
    {
        if ($holiday->getCreatedAt() === null) {
            $data['general']['created_at'] = $this->date->date();
        }

        $data['general']['updated_at'] = $this->date->date();

        if (!$data['general']['from']) {
            $data['general']['from'] = $this->date->date();
        }

        $holiday->addData($data['general']);

        $locations = $this->getRequest()->getPost('locations');

        if (isset($locations)) {
            $holiday->setIsLocationGrid(true);
            $holiday->setLocationsIds(
                $this->jsHelper->decodeGridSerializedInput($locations)
            );
        }

        return $this;
    }
}
