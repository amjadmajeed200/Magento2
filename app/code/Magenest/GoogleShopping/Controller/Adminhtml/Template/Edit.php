<?php
namespace Magenest\GoogleShopping\Controller\Adminhtml\Template;

/**
 * Class Edit
 * @package Magenest\GoogleShopping\Controller\Adminhtml\Template
 */
class Edit extends AbstractTemplateAction
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $id = $this->getRequest()->getParam('id');
            $templateModel = $this->_templateFactory->create();
            if ($id) {
                $this->_templateResource->load($templateModel, $id);
                if (!$templateModel->getId()) {
                    $this->messageManager->addErrorMessage(__('This Template Feed no longer exists.'));
                    /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                    $resultRedirect = $this->resultRedirectFactory->create();
                    return $resultRedirect->setPath('*/*/index');
                }
            }
            $this->_coreRegistry->register('template_feed_model', $templateModel);
            $title = $templateModel->getId() ? $templateModel->getName() : __('New Template');
            /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
            $resultPage = $this->resultPageFactory->create();
            $resultPage->setActiveMenu('Magenest_GoogleShopping::google_product_template');
            $resultPage->addBreadcrumb(__('Google Shopping'), __('Google Shopping'));
            $resultPage->addBreadcrumb(__('Product Template'), __('Product Template'));
            $resultPage->getConfig()->getTitle()->prepend($title);
            return $resultPage;
        } catch (\Exception $exception) {
            $this->_logger->debug($exception->getMessage());
        }
    }
}
