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
namespace Aheadworks\Nmi\Block\Customer;

use Magento\Customer\Block\Account\SortLink;
use Magento\Framework\App\DefaultPathInterface;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class MyCreditCardsLink
 *
 * This rewrites credit card link from vault module
 * since the link is natively depended on braintree only
 *
 * @package Aheadworks\Nmi\Block\Customer
 */
class MyCreditCardsLink extends SortLink
{
    /**
     * @var array
     */
    private $configList;

    /**
     * @param Context $context
     * @param DefaultPathInterface $defaultPath
     * @param array $data
     * @param array $configList
     */
    public function __construct(
        Context $context,
        DefaultPathInterface $defaultPath,
        array $data = [],
        array $configList = []
    ) {
        parent::__construct($context, $defaultPath, $data);
        $this->configList = $configList;
    }

    /**
     * @inheritdoc
     */
    public function toHtml()
    {
        if ($this->isBlockVisible()) {
            return parent::toHtml();
        }

        return '';
    }

    /**
     * Check if block is visible
     *
     * @return bool
     */
    public function isBlockVisible()
    {
        $result = false;
        foreach ($this->configList as $config) {
            if ($this->_scopeConfig->getValue($config)) {
                $result = true;
                break;
            }
        }

        return $result;
    }
}
