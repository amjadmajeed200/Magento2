<?php
namespace Magenest\GoogleShopping\Controller\Adminhtml\Template;

/**
 * Class MassDelete
 * @package Magenest\GoogleShopping\Controller\Adminhtml\Template
 */
class MassDelete extends AbstractTemplateAction
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $count = 0;
            $collections = $this->_filter->getCollection($this->_templateCollectionFactory->create());
            foreach ($collections->getItems() as $item) {
                $this->_templateResource->delete($item);
                $count++;
            }
            $this->messageManager->addSuccessMessage(__('Total of %1 record(s) were deleted.', $count));
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $redirectResult */
        $redirectResult = $this->resultRedirectFactory->create();
        $redirectResult->setPath('*/*/index');
        return $redirectResult;
    }
}
