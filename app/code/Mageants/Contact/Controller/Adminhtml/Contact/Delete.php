<?php
/**
 * @category  Magento Contact
 * @package   Mageants_Contact
 * @copyright Copyright (c) 2017 Magento
 * @author    Mageants Team <support@mageants.com>
 */
namespace Mageants\Contact\Controller\Adminhtml\Contact;

use Magento\Backend\App\Action\Context;
use Mageants\Contact\Model\Contact;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var Contact $categoryModel
     */
    protected $_categoryModel;

    /**
     * @param Context $context
     * @param Category Model $category_model
     */
    public function __construct(Context $context,Contact $categoryModel)
    {
        $this->_categoryModel = $categoryModel;
        parent::__construct($context);
    }
   
    /**
     * Delete Action
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
		$id = $this->getRequest()->getParam('id');
		try {
				$image = $this->_categoryModel->load($id);
				$image->delete();
                $this->messageManager->addSuccess(
                    __('Delete successfully !')
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
	    $this->_redirect('*/*/');
    }
}

