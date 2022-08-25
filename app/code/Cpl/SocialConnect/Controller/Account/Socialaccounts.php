<?php
/**
 * Cpl
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Cpl
 * @package    Cpl_SocialConnect
 * @copyright  Copyright (c) 2022 Cpl (https://www.magento.com/)
 */

namespace Cpl\SocialConnect\Controller\Account;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Socialaccounts
 * @package Cpl\SocialConnect\Controller\Account
 */
class Socialaccounts extends Action
{

    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * Socialaccounts constructor.
     * 
     * @param Context       $context
     * @param PageFactory   $pageFactory
     */
    public function __construct(
        Context     $context,
        PageFactory $pageFactory
    )
    {
        parent::__construct($context);
        $this->pageFactory = $pageFactory;
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */    
    public function execute()
    {
        return $this->pageFactory->create();
    }
}