<?php
namespace Magenest\GoogleShopping\Controller\Adminhtml\Feed;

use Magenest\GoogleShopping\Model\GoogleFeedFactory;
use Magenest\GoogleShopping\Model\ResourceModel\GoogleFeed as GoogleResourceModel;
use Magenest\GoogleShopping\Model\ResourceModel\GoogleFeed\CollectionFactory as GoogleFeedCollectionFactory;
use Magenest\GoogleShopping\Model\ResourceModel\GoogleFeedIndex as GoogleFeedIndexResourceModel;
use Magenest\GoogleShopping\Model\Rule\GetValidProduct;
use Magenest\GoogleShopping\Model\Cron;
use Magenest\GoogleShopping\Model\ResourceModel\GoogleProduct as GoogleProductResourceModel;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\CatalogRule\Model\RuleFactory;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\View\LayoutFactory;

/**
 * Class AbstractFeedAction
 * @package Magenest\GoogleShopping\Controller\Adminhtml\Feed
 */
abstract class AbstractFeedAction extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magenest_GoogleShopping::google_feed';

    /** @var GoogleFeedFactory  */
    protected $_googleFeedFactory;

    /** @var GoogleResourceModel  */
    protected $_googleFeedResource;

    /** @var GoogleFeedCollectionFactory  */
    protected $_collectionFactory;

    /** @var GoogleFeedIndexResourceModel  */
    protected $_googleFeedIndexResource;

    /** @var GetValidProduct  */
    protected $_ruleValidProduct;

    /** @var Cron  */
    protected $_cronModel;

    /** @var GoogleProductResourceModel  */
    protected $_googleProduct;

    /** @var LoggerInterface  */
    protected $_logger;

    /** @var PageFactory  */
    protected $resultPageFactory;

    /** @var ForwardFactory  */
    protected $resultForwardFactory;

    /** @var Registry  */
    protected $_coreRegistry;

    /** @var \Magento\CatalogRule\Model\RuleFactory  */
    protected $_ruleFactory;

    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $resultLayoutFactory;

    /** @var Json  */
    protected $_jsonFramework;

    /** @var Filter  */
    protected $_filter;

    /** @var RawFactory  */
    protected $resultRawFactory;

    /** @var LayoutFactory  */
    protected $layoutFactory;

    /**
     * AbstractFeedAction constructor.
     * @param GoogleFeedFactory $googleFeedFactory
     * @param GoogleResourceModel $googleFeedResource
     * @param GoogleFeedCollectionFactory $collectionFactory
     * @param GoogleFeedIndexResourceModel $googleFeedIndexResource
     * @param GetValidProduct $ruleValidProduct
     * @param Cron $cronModel
     * @param GoogleProductResourceModel $googleProductResource
     * @param LoggerInterface $logger
     * @param PageFactory $resultPageFactory
     * @param ForwardFactory $resultForwardFactory
     * @param Registry $registry
     * @param RuleFactory $ruleFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param Json $jsonFramework
     * @param Filter $filter
     * @param RawFactory $rawFactory
     * @param LayoutFactory $layoutFactory
     * @param Context $context
     */
    public function __construct(
        GoogleFeedFactory $googleFeedFactory,
        GoogleResourceModel $googleFeedResource,
        GoogleFeedCollectionFactory $collectionFactory,
        GoogleFeedIndexResourceModel $googleFeedIndexResource,
        GetValidProduct  $ruleValidProduct,
        Cron $cronModel,
        GoogleProductResourceModel $googleProductResource,
        LoggerInterface $logger,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory,
        Registry $registry,
        \Magento\CatalogRule\Model\RuleFactory $ruleFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        Json $jsonFramework,
        Filter $filter,
        RawFactory $rawFactory,
        LayoutFactory $layoutFactory,
        Context $context
    ) {
        $this->_googleFeedFactory = $googleFeedFactory;
        $this->_googleFeedResource = $googleFeedResource;
        $this->_collectionFactory = $collectionFactory;
        $this->_googleFeedIndexResource = $googleFeedIndexResource;
        $this->_ruleValidProduct = $ruleValidProduct;
        $this->_cronModel = $cronModel;
        $this->_googleProduct = $googleProductResource;
        $this->_logger = $logger;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_coreRegistry = $registry;
        $this->_ruleFactory = $ruleFactory;
        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->_jsonFramework = $jsonFramework;
        $this->_filter = $filter;
        $this->resultRawFactory = $rawFactory;
        $this->layoutFactory = $layoutFactory;
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
            ->addBreadcrumb(__('Google Feed'), __('Google Feed'))
            ->addBreadcrumb(__('Feed'), __('Feed'));
        return $resultPage;
    }

    /**
     * @return \Magenest\GoogleShopping\Model\GoogleFeed
     */
    protected function initCurrentFeed()
    {
        $id = $this->getRequest()->getParam('id');
        $googleFeedModel = $this->_googleFeedFactory->create();
        if ($id) {
            $this->_googleFeedResource->load($googleFeedModel, $id);
        }
        $this->_coreRegistry->register('feed_model', $googleFeedModel);
        return $googleFeedModel;
    }

    /**
     * @return \Magenest\GoogleShopping\Model\GoogleFeed
     * @throws \Exception
     */
    public function getFeedModel()
    {
        $id = $this->getRequest()->getParam('id');
        $googleFeedModel = $this->_googleFeedFactory->create();
        if ($id) {
            $this->_googleFeedResource->load($googleFeedModel, $id);
            if (!$googleFeedModel->getId()) {
                throw new \Exception(__("Can not find Feed"));
            }
        }
        return $googleFeedModel;
    }
}
