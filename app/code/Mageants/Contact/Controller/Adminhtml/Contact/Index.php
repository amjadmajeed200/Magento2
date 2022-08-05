<?php
/**
 * @category  Magento Contact
 * @package   Mageants_Contact
 * @copyright Copyright (c) 2017 Magento
 * @author    Mageants Team <support@mageants.com>
 */
namespace Mageants\Contact\Controller\Adminhtml\Contact;

class Index extends \Magento\Backend\App\Action
{
    /**
     * PageFactory
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory = false;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }
    
    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Mageants_Contact::contact_manage');
        $resultPage->addBreadcrumb(__('Mageants'), __('Mageants'));
        $resultPage->addBreadcrumb(__('Contact'), __('Contact'));
        $resultPage->getConfig()->getTitle()->prepend(__('Contacts'));
        return $resultPage;
    }
}
