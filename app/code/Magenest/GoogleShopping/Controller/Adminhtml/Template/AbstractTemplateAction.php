<?php
namespace Magenest\GoogleShopping\Controller\Adminhtml\Template;

use Magenest\GoogleShopping\Model\ResourceModel\Template as TemplateResourceModel;
use Magenest\GoogleShopping\Model\TemplateFactory;
use Magenest\GoogleShopping\Model\ResourceModel\Template\CollectionFactory as TemplateCollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;

/**
 * Class AbstractTemplateAction
 * @package Magenest\GoogleShopping\Controller\Adminhtml\Template
 */
abstract class AbstractTemplateAction extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = "Magenest_GoogleShopping::google_product_template";

    /** @var TemplateFactory  */
    protected $_templateFactory;

    /** @var TemplateResourceModel  */
    protected $_templateResource;

    protected $_templateCollectionFactory;

    /** @var PageFactory  */
    protected $resultPageFactory;

    /** @var ForwardFactory  */
    protected $resultForwardFactory;

    /** @var Registry  */
    protected $_coreRegistry;

    /** @var LoggerInterface  */
    protected $_logger;

    /** @var Json  */
    protected $_jsonFramework;

    /** @var Filter  */
    protected $_filter;

    /**
     * AbstractTemplateAction constructor.
     * @param TemplateFactory $templateFactory
     * @param TemplateResourceModel $templateResource
     * @param TemplateCollectionFactory $templateCollectionFactory
     * @param PageFactory $resultPageFactory
     * @param ForwardFactory $resultForwardFactory
     * @param Registry $registry
     * @param LoggerInterface $logger
     * @param Json $jsonFramework
     * @param Filter $filter
     * @param Context $context
     */
    public function __construct(
        TemplateFactory $templateFactory,
        TemplateResourceModel $templateResource,
        TemplateCollectionFactory $templateCollectionFactory,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory,
        Registry $registry,
        LoggerInterface $logger,
        Json $jsonFramework,
        Filter $filter,
        Context $context
    ) {
        $this->_templateFactory = $templateFactory;
        $this->_templateResource = $templateResource;
        $this->_templateCollectionFactory = $templateCollectionFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_coreRegistry = $registry;
        $this->_logger = $logger;
        $this->_jsonFramework = $jsonFramework;
        $this->_filter = $filter;
        parent::__construct($context);
    }

    /**
     * Init page
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Magenest_GoogleShopping::google_feed')
            ->addBreadcrumb(__('Google Shopping'), __('Google Shopping'))
            ->addBreadcrumb(__('Google Shopping'), __('Google Shopping'));
        return $resultPage;
    }
}
