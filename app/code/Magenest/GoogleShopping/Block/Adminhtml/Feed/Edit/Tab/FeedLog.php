<?php
namespace Magenest\GoogleShopping\Block\Adminhtml\Feed\Edit\Tab;

use Magenest\GoogleShopping\Model\ResourceModel\GoogleProduct\CollectionFactory as GoogleProductCollectionFactory;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use Magento\Framework\Registry;
use Psr\Log\LoggerInterface;

/**
 * Class FeedTemplate
 * @package Magenest\GoogleShopping\Block\Adminhtml\Feed\Edit\Tab
 */
class FeedLog extends Extended
{
    protected $_feedModel = null;

    /** @var LoggerInterface  */
    protected $_logger;

    /** @var GoogleProductCollectionFactory  */
    protected $_collectionFactory;

    /** @var Registry  */
    protected $_coreRegistry;

    /**
     * FeedLog constructor.
     * @param LoggerInterface $logger
     * @param GoogleProductCollectionFactory $googleProductCollectionFactory
     * @param Registry $coreRegistry
     * @param \Magento\Backend\Block\Template\Context $context
     * @param Data $backendHelper
     * @param array $data
     */
    public function __construct(
        LoggerInterface $logger,
        GoogleProductCollectionFactory $googleProductCollectionFactory,
        Registry $coreRegistry,
        \Magento\Backend\Block\Template\Context $context,
        Data $backendHelper,
        array $data = []
    ) {
        $this->_logger = $logger;
        $this->_collectionFactory = $googleProductCollectionFactory;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('googlefeed_edit_tab_log');
        $this->setDefaultSort('id');
        $this->setDefaultDir('desc');
        $this->setUseAjax(true);
    }

    /**
     * @return FeedLog
     */
    protected function _prepareCollection()
    {
        try {
            $feedId = $this->getFeedModel()->getId();
            $collection = $this->_collectionFactory->create()->addFieldToFilter('feed_id', $feedId);
        } catch (\Exception $e) {
            $this->_logger->debug($e->getMessage());
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return FeedLog
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            [
                'header' => __('ID #'),
                'width' => '150px',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'index' => 'offerId',
                'filter' => false,
                'renderer' => \Magenest\GoogleShopping\Block\Adminhtml\Feed\Edit\Tab\Renderer\ProductId::class,
            ]
        );

        $this->addColumn(
            'google_product',
            [
                'header' => __('Google Product Id'),
                'width' => '100',
                'index' => 'google_product'
            ]
        );

        $this->addColumn(
            'offerId',
            [
                'header' => __('SKU'),
                'index' => 'offerId'
            ]
        );

        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status'
            ]
        );
        $this->addColumn(
            'merchant',
            [
                'header' => __('Merchant'),
                'index' => 'merchant'
            ]
        );
        $this->addColumn(
            'message',
            [
                'header' => __('Message'),
                'index' => 'message',
                'renderer' => \Magenest\GoogleShopping\Block\Adminhtml\Feed\Edit\Tab\Renderer\Message::class,
                'filter' => false,
                'sortable'  => false
            ]
        );
        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }
        parent::_prepareColumns();
        return $this;
    }

    /**
     * @return mixed|null
     */
    protected function getFeedModel()
    {
        if ($this->_feedModel == null) {
            $this->_feedModel = $this->_coreRegistry->registry('feed_model');
        }
        return $this->_feedModel;
    }
    /**
     * @inheritdoc
     */
    public function getGridUrl()
    {
        return $this->getUrl('googleshopping/*/template', ['_current' => true]);
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareFilterButtons()
    {
        $feedModel = $this->getFeedModel();
        $this->setChild(
            'googleproduct_status_collecting',
            $this->getLayout()->createBlock(
                \Magento\Backend\Block\Widget\Button::class
            )->setData(
                [
                    'label'   => __('Update product status'),
                    'onclick' => 'setLocation("' . $this->getUrl('googleshopping/feed/updateStatus', ['_current' => true, 'id' => $feedModel->getId()]) . '")',
                    'class'   => 'action-default action-reset action-tertiary',
                ]
            )->setDataAttribute(['action' => 'emulate_collecting_redirect'])
        );
        parent::_prepareFilterButtons();
    }

    /**
     * @return string
     */
    public function getMainButtonsHtml()
    {
        $html = '';
        if ($this->getFilterVisibility()) {
            $html .= $this->getSearchButtonHtml();
            $html .= $this->getResetFilterButtonHtml();
            $html .= $this->getButtonsHtml();
        }
        return $html;
    }

    /**
     * @return string
     */
    protected function getButtonsHtml()
    {
        return $this->getChildHtml('googleproduct_status_collecting');
    }
}
