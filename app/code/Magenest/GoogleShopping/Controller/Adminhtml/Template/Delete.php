<?php
namespace Magenest\GoogleShopping\Controller\Adminhtml\Template;

/**
 * Class Delete
 * @package Magenest\GoogleShopping\Controller\Adminhtml\Template
 */
class Delete extends AbstractTemplateAction
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $redirectResult */
        $redirectResult = $this->resultRedirectFactory->create();
        try {
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $templateModel = $this->_templateFactory->create();
                $this->_templateResource->load($templateModel, $id);
                if (!$templateModel->getId()) {
                    throw new \Exception(__(__("Template with Id %1 doesn't exit.", $id)));
                }
                $this->_templateResource->delete($templateModel);
                $this->messageManager->addSuccessMessage(__("The Template with Id is %1 was deleted.", $id));
            } else {
                $this->messageManager->addSuccessMessage(__("Something when wrong. Please try again."));
            }
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
            $this->_logger->debug($exception->getMessage());
        }
        $redirectResult->setPath('*/*/index');
        return $redirectResult;
    }
}
