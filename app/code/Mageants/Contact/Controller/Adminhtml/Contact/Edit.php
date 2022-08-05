<?php
/**
 * @category  Magento Contact
 * @package   Mageants_Contact
 * @copyright Copyright (c) 2017 Magento
 * @author    Mageants Team <support@mageants.com>
 */

namespace Mageants\Contact\Controller\Adminhtml\Contact;
 
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Mageants\Contact\Model\Contact;	
 
class Edit extends \Magento\Backend\App\Action
{
    /**
     * @var Contact $categoryModel
     */
    protected $_categoryModel;    
    
    /**
     * @var Filter
     */
    protected $filter;
 
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory; 
 
	/**
	 * @param Context $context
	 * @param Registry $coreRegistry
	 */
	public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        Contact $categoryModel        
    ) 
    {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_categoryModel = $categoryModel;
    }

    /**
     * Edi action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
		$rowId = (int) $this->getRequest()->getParam('id');
        $rowData = $this->_categoryModel;
        if ($rowId) {
		    $rowData = $rowData->load($rowId);
		    $rowTitle = $rowData->getName();
            if (!$rowData->getMessageId()) {
                $this->messageManager->addError(__('contact message no longer exist.'));
                $this->_redirect('mageants_contact/index/');
                return;
            }
        }
 
        $this->_coreRegistry->register('mageants_contact', $rowData);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $title = $rowId ? __('View Contact ').$rowTitle : __('Add Row Data');
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }
}

