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

namespace Cpl\SocialConnect\Controller;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Store\Model\StoreManager;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager;
use Cpl\SocialConnect\Helper\Data;

/**
 * Class AbstractAccount
 * @package Cpl\SocialConnect\Controller
 */
abstract class AbstractAccount extends Action
{

    /**
     * @var
     */
    public $type;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var StoreManager
     */
    protected $storeManager;

    /**
     * @var LayoutInterface
     */
    protected $layout;

    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    /**
     * @type
     */
    protected $cookieMetadataManager;

    /**
     * @type
     */
    protected $cookieMetadataFactory;

    /**
     * @var Data
     */
    protected $slHelper;    

    /**
     * AbstractAccount constructor.
     * 
     * @param Context           $context
     * @param Session           $customerSession
     * @param StoreManager      $storeManager
     * @param LayoutInterface   $layout
     * @param RawFactory        $resultRawFactory
     * @param Data              $slHelper
     */
    public function __construct(
        Context         $context,
        Session         $customerSession,
        StoreManager    $storeManager,
        LayoutInterface $layout,
        RawFactory      $resultRawFactory,
        Data            $slHelper
    )
    {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->layout = $layout;
        $this->resultRawFactory = $resultRawFactory;
        $this->slHelper = $slHelper;
    }

    /**
     * @param $type
     */
    protected function _setType($type) {
        $this->type = $type;
    }

    /**
     * @param $type
     * @return mixed
     */
    protected function _getModel() {
        $className = 'Cpl\SocialConnect\Model\\' . ucfirst($this->type);
        $exist = class_exists($className);
        if(!$exist) {
            $this->_windowClose();
        }

        return $this->_objectManager->get($className);
    }

    /**
     * @param $userProfile
     * @return bool
     */
    public function createCustomerProcess($userProfile)
    {
        $user = array_merge([
            'email'      => $userProfile['email'],
            'firstname'  => $userProfile['firstname'],
            'lastname'   => $userProfile['lastname'],
            'identifier' => $userProfile['user_id'],
            'type'       => $this->type
        ], $this->getUserData($userProfile));
        return $this->createCustomer($user, $this->type);
    }

    /**
     * @param $user
     * @return bool
     */
    public function createCustomer($user)
    {
        $customer = $this->model->getCustomerByEmail($user['email']);
        if (!$customer->getId()) {
            try {
                $customer = $this->model->createCustomerSocial($user, $this->storeManager->getStore());
            } catch (\Exception $e) {
                $this->emailRedirect();
                return false;
            }
        } else {
            $this->model->setUser($user['user_id'], $customer->getId(), $this->type);
        }
        return $customer;
    }

    /**
     * @param $profile
     * @return array
     */
    protected function getUserData()
    {
        return [];
    }

    /**
     * @param $msg
     * @param bool $needTranslate
     * @return $this
     */
    public function emailRedirect()
    {
        $this->_redirect('customer/account/login');
        return $this;
    }

    protected function _windowClose()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->getResponse()->clearHeaders()->setHeader('Content-type', 'application/json', true);
            $this->getResponse()->setBody(json_encode([
                'windowClose' => true
            ]));
        } else {
            $this->getResponse()->setBody($this->attacheJs('window.close();'));
        }
    }

    /**
     * @param $customer
     */
    protected function _dispatchRegisterSuccess($customer)
    {
        $this->_eventManager->dispatch(
            'customer_register_success',
            ['account_controller' => $this, 'customer' => $customer]
        );
    }

    /**
     * @return \Magento\Customer\Model\Session
     */
    protected function _getSession()
    {
        return $this->customerSession;
    }

    /**
     * @param       $url
     * @param array $params
     *
     * @return string
     */
    protected function _getUrl($url, $params = [])
    {
        return $this->_url->getUrl($url, $params);
    }

    /**
     * @return \Cpl\SocialConnect\Helper\Data
     */
    protected function _slHelper()
    {
        return $this->slHelper;
    }

    /**
     * @param $content
     * @return string
     */
    protected function attacheJs($content)
    {
        return '<html><head></head><body><script type="text/javascript">' . $content . '</script></body></html>';
    }

    /**
     * @param null $content
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function _appendJs($content = null)
    {
        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents($content);
    }

    /**
     * @param $customer
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function refresh($customer)
    {
        if ($customer && $customer->getId()) {
            $this->customerSession->setCustomerAsLoggedIn($customer);
            $this->customerSession->regenerateId();
            if ($this->getCookieManager()->getCookie('mage-cache-sessid')) {
                $metadata = $this->getCookieMetadataFactory()->createCookieMetadata();
                $metadata->setPath('/');
                $this->getCookieManager()->deleteCookie('mage-cache-sessid', $metadata);
            }
        }
    }

    /**
     * Retrieve cookie manager
     *
     * @deprecated
     * @return \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     */
    private function getCookieManager()
    {
        if (!$this->cookieMetadataManager) {
            $this->cookieMetadataManager = ObjectManager::getInstance()->get(
                PhpCookieManager::class
            );
        }
        return $this->cookieMetadataManager;
    }

    /**
     * Retrieve cookie metadata factory
     *
     * @deprecated
     * @return \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    private function getCookieMetadataFactory()
    {
        if (!$this->cookieMetadataFactory) {
            $this->cookieMetadataFactory = ObjectManager::getInstance()->get(
                CookieMetadataFactory::class
            );
        }
        return $this->cookieMetadataFactory;
    }
}
