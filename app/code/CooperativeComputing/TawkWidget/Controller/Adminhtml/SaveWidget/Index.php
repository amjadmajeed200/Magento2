<?php
/**
 * CooperativeComputing
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   CooperativeComputing
 * @package    CooperativeComputing_TawkWidget
 * @copyright  Copyright (c) 2022 CooperativeComputing (https://www.magento.com/)
 */

namespace CooperativeComputing\TawkWidget\Controller\Adminhtml\SaveWidget;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Psr\Log\LoggerInterface;
use Tawk\Widget\Model\WidgetFactory;

/**
 * Class Index
 * @package CooperativeComputing\TawkWidget\Controller\Adminhtml\SaveWidget
 */
class Index extends \Tawk\Widget\Controller\Adminhtml\SaveWidget\Index
{

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var LoggerInterface
     */    
    protected $logger;

    /**
     * @var WidgetFactory
     */
    protected $modelWidgetFactory;

    /**
     * @var Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * Index Construct.
     *
     * @param Context           $context
     * @param JsonFactory       $resultJsonFactory
     * @param LoggerInterface   $logger
     * @param WidgetFactory     $modelFactory
     */
    public function __construct(
        Context         $context,
        JsonFactory     $resultJsonFactory,
        LoggerInterface $logger,
        WidgetFactory   $modelFactory
    ) {
        parent::__construct($modelFactory, $context, $resultJsonFactory, $logger);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->logger = $logger;
        $this->modelWidgetFactory = $modelFactory->create();
        $this->request = $this->getRequest();
    }

    /**
     * Save widget
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $response = $this->resultJsonFactory->create();
        $response->setHeader('Content-type', 'application/json');

        $pageId = filter_var($this->request->getParam('pageId'), FILTER_UNSAFE_RAW);
        $widgetId = filter_var($this->request->getParam('widgetId'), FILTER_UNSAFE_RAW);
        $storeId = filter_var($this->request->getParam('id'), FILTER_UNSAFE_RAW);

        if (!$pageId || !$widgetId || !$storeId) {
            return $response->setData(['success' => false]);
        }

        $alwaysdisplay = filter_var($this->request->getParam('alwaysdisplay'), FILTER_SANITIZE_NUMBER_INT);
        $excludeurl = filter_var($this->request->getParam('excludeurl'), FILTER_UNSAFE_RAW);
        $donotdisplay = filter_var($this->request->getParam('donotdisplay'), FILTER_SANITIZE_NUMBER_INT);
        $includeurl = filter_var($this->request->getParam('includeurl'), FILTER_UNSAFE_RAW);
        $enableVisitorRecognition = filter_var(
            $this->request->getParam('enableVisitorRecognition'),
            FILTER_UNSAFE_RAW
        );

        $model = $this->modelWidgetFactory->loadByForStoreId($storeId);

        if ($pageId != '-1') {
            $model->setPageId($pageId);
        }

        if ($widgetId != '-1') {
            $model->setWidgetId($widgetId);
        }

        $model->setForStoreId($storeId);

        $model->setAlwaysDisplay($alwaysdisplay);
        $model->setExcludeUrl($excludeurl);

        $model->setDoNotDisplay($donotdisplay);
        $model->setIncludeUrl($includeurl);

        $model->setEnableVisitorRecognition($enableVisitorRecognition);

        $model->save();

        return $response->setData(['success' => true]);
    }
}














