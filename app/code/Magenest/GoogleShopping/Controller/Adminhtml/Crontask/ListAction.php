<?php
namespace Magenest\GoogleShopping\Controller\Adminhtml\Crontask;

/**
 * Class ListAction
 * @package Magenest\GoogleShopping\Controller\Adminhtml\Crontask
 */
class ListAction extends AbstractCronTask
{

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magenest_GoogleShopping::cron_task');
        $resultPage->addBreadcrumb(__('Cron Schedule List'), __('Cron Schedule List'));
        $resultPage->addBreadcrumb(__('Google Shopping'), __('Google Shopping'));
        $resultPage->getConfig()->getTitle()->prepend(__('Cron Schedule List'));
        return $resultPage;
    }
}
