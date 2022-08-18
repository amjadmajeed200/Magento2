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

namespace Mageplaza\StoreLocator\Controller\Adminhtml\Holiday;

use Exception;
use Magento\Framework\Controller\Result\Redirect;
use Mageplaza\StoreLocator\Controller\Adminhtml\Holiday;

/**
 * Class Delete
 * @package Mageplaza\StoreLocator\Controller\Adminhtml\Holiday
 */
class Delete extends Holiday
{
    /**
     * @return Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $this->holidayFactory->create()->load($id)->delete();

                $this->messageManager->addSuccessMessage(__('The Holiday has been deleted.'));
            } catch (Exception $e) {
                /** display error message */
                $this->messageManager->addErrorMessage($e->getMessage());
                /** go back to edit form */
                $resultRedirect->setPath('mpstorelocator/*/edit', ['id' => $id]);

                return $resultRedirect;
            }
        } else {
            /** display error message */
            $this->messageManager->addErrorMessage(__('Holiday to delete was not found.'));
        }

        /** goto grid */
        $resultRedirect->setPath('mpstorelocator/*/');

        return $resultRedirect;
    }
}
