<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_StoreLocator
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\StoreLocator\Controller\StoreLocator;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\StoreLocator\Block\Frontend;

/**
 * Class Store
 * @package Mageplaza\StoreLocator\Controller\StoreLocator
 */
class Store extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var LayoutFactory
     */
    protected $_resultLayoutFactory;

    /**
     * @var JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * Store constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param LayoutFactory $resultLayoutFactory
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        LayoutFactory $resultLayoutFactory,
        JsonFactory $resultJsonFactory
    ) {
        $this->resultPageFactory    = $resultPageFactory;
        $this->_resultLayoutFactory = $resultLayoutFactory;
        $this->_resultJsonFactory   = $resultJsonFactory;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        if ($this->getRequest()->isAjax()) {
            $result       = $this->_resultJsonFactory->create();
            $resultLayout = $this->_resultLayoutFactory->create();
            $listLocHtml  = $resultLayout->getLayout()
                ->createBlock(Frontend::class)
                ->setTemplate('Mageplaza_StoreLocator::storelocator/index.phtml')
                ->toHtml();
            $result->setData([
                'success' => $listLocHtml
            ]);

            return $result;
        }

        return $this->resultPageFactory->create();
    }
}
