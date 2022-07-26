<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://aheadworks.com/end-user-license-agreement/
 *
 * @package    Nmi
 * @version    1.3.1
 * @copyright  Copyright (c) 2022 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Nmi\Block\Checkout;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Aheadworks\Nmi\ViewModel\CollectJs as CollectJsViewModel;

/**
 * Class CollectJs
 * @package Aheadworks\Nmi\Block\Checkout
 */
class CollectJs extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Aheadworks_Nmi::checkout/collect_js_init.phtml';

    /**
     * @var CollectJsViewModel
     */
    private $viewModel;

    /**
     * @param Context $context
     * @param CollectJsViewModel $viewModel
     * @param array $data
     */
    public function __construct(
        Context $context,
        CollectJsViewModel $viewModel,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->viewModel = $viewModel;
    }

    /**
     * Retrieve view model
     *
     * @return CollectJsViewModel
     */
    public function getViewModel()
    {
        return $this->viewModel;
    }
}
