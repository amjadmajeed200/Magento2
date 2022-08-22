<?php
namespace Magenest\GoogleShopping\Controller\Adminhtml\Category;

use Magenest\GoogleShopping\Model\ResourceModel\Template\CollectionFactory as TemplateCollection;
use Magenest\GoogleShopping\Model\Template as GoogleShoppingTemplate;
use Magenest\GoogleShopping\Model\TemplateFactory as GoogleShoppingTemplateFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;

/**
 * Class Index
 * @package Magenest\GoogleShopping\Controller\Adminhtml\Category
 */
class Index extends \Magento\Backend\App\Action
{
    /** @var TemplateCollection  */
    protected $_collectionFactory;

    /** @var GoogleShoppingTemplateFactory  */
    protected $_templateFactory;

    /** @var Registry  */
    protected $_coreRegistry;

    /** @var LoggerInterface  */
    protected $_logger;

    /** @var PageFactory  */
    protected $resultPageFactory;

    public function __construct(
        TemplateCollection $collectionFactory,
        GoogleShoppingTemplateFactory $templateFactory,
        Registry $coreRegistry,
        LoggerInterface $logger,
        PageFactory $pageFactory,
        Context $context
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_templateFactory = $templateFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_logger = $logger;
        $this->resultPageFactory = $pageFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $templateModel = $this->_collectionFactory->create()
                ->addFieldToFilter('type', GoogleShoppingTemplate::CATEGORY_TEMPLATE)
                ->getFirstItem();
            if (!$templateModel) {
                $templateModel = $this->_templateFactory->create();
            }
            $this->_coreRegistry->register('template_feed_model', $templateModel);
            /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->prepend(__('Google Category Mapping'));
            return $resultPage;
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
            $this->_logger->debug($exception->getMessage());
        }
    }
}
