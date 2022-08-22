<?php
namespace Magenest\GoogleShopping\Controller\Adminhtml\Template;

/**
 * Class Index
 * @package Magenest\GoogleShopping\Controller\Adminhtml\Template
 */
class Index extends AbstractTemplateAction
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        try {
            $resultPage = $this->resultPageFactory->create();
            $this->initPage($resultPage)->getConfig()->getTitle()->prepend(__('Product Template'));
            return $resultPage;
        } catch (\Exception $exception) {
            $this->_logger->debug($exception->getMessage());
        }
    }
}
