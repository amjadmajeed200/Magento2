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

use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Customer;
use Magento\Store\Model\StoreManager;
use Magento\Framework\View\LayoutInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Data\Form\FormKey;
use Cpl\SocialConnect\Helper\Data;

/**
 * Class Login
 * @package Cpl\SocialConnect\Controller\Account
 */
class Login extends \Cpl\SocialConnect\Controller\AbstractAccount
{

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var Customer
     */
    protected $customer;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var FormKey
     */
    protected $formKey;

    /**
     * @var
     */
    protected $model;

    /**
     * Login constructor.
     * 
     * @param Context                       $context
     * @param Session                       $customerSession
     * @param StoreManager                  $storeManager
     * @param LayoutInterface               $layout
     * @param Customer                      $customer
     * @param Data                          $dataHelper
     * @param RawFactory                    $resultRawFactory
     * @param CustomerRepositoryInterface   $customerRepository
     * @param FormKey                       $formKey
     */
    public function __construct(
        Context                     $context,
        Session                     $customerSession,
        StoreManager                $storeManager,
        LayoutInterface             $layout,
        CustomerFactory             $customer,
        Data                        $dataHelper,
        RawFactory                  $resultRawFactory,
        CustomerRepositoryInterface $customerRepository,
        FormKey                     $formKey
    )
    {
        parent::__construct($context, $customerSession, $storeManager, $layout, $resultRawFactory, $dataHelper);
        $this->customer = $customer;
        $this->customerRepository = $customerRepository;
        $this->formKey = $formKey;
    }

    /**
     * @return \Magento\Framework\App\RequestInterface
     */
    public function execute()
    {
        $session = $this->_getSession();
        $type = $this->getRequest()->getParam('type');
        $formKey = $this->formKey->getFormKey();
        if ($session->isLoggedIn()) {
            return $this->_windowClose();
        }
        if (!$type) {
            return $this->_windowClose();
        }
        $this->_setType($type);
        $this->model = $this->_getModel($this->type);
        $rpCodes = $this->model->getRpCode();
        if (is_array($rpCodes)) {
            $response = [];
            foreach ($rpCodes as $code) {
                $response[$code] = $this->getRequest()->getParam($code);
            }
        } else {
            $response = $this->getRequest()->getParam($rpCodes);
        }
        if (!$this->model->fetchUserData($response)) {
            return $this->_windowClose();
        }
        $newUserData = $this->model->fetchSocialUserData();
        $customerId = $this->model->getCustomerIdByUser();
        if ($customerId) {
            $redirectUrl = $this->_slHelper()->getRedirectUrl();
        } elseif ($customerId = $this->model->getCustomerIdByUserEmail()) {
            $this->model->setCustomerByUser($customerId);
            $message = __('Customer with email %1 already exists in the database. Your %2 Profile is linked to this customer.', '<b>'.$this->model->fetchSocialUserData('email'). '</b>', '<b>'.ucfirst($this->type).'</b>');
            $this->messageManager->addNotice($message);
            $redirectUrl = $this->_slHelper()->getRedirectUrl();
        } else {
            // instagram does not return the social user email address
            if(empty($this->model->fetchSocialUserData('email'))) {
                $this->customerSession->setUserProfile($newUserData);
                return $this->_appendJs("<script>window.close();window.opener.emailCallback();</script>");
            }
            $customer = $this->createCustomerProcess($newUserData);
            if ($customer) {
                $customerId = $customer->getId();
                $this->messageManager->addSuccess(__('Customer registration successful. Your password reset link was sent to the email: %1', $this->model->fetchSocialUserData('email')));
                $this->_dispatchRegisterSuccess($customer);
                $redirectUrl = $this->_slHelper()->getRedirectUrl();
            } else {
                $session->setCustomerFormData($newUserData);
                $redirectUrl = $this->_getUrl('customer/account/create', ['_secure' => true]);
                $errors = [];
                if ($errors = $this->model->getErrors()) {
                    foreach ($errors as $error) {
                        $this->messageManager->addError($error);
                    }
                }
                $session->setData('sociallogin', [
                    'provider' => $this->model->getProvider(),
                    'user_id' => $this->model->fetchSocialUserData('user_id')
                ]);
            }
        }
        if ($customerId) {
            $customer = $this->customer->create()->load($customerId);
            try {
                $this->refresh($customer);
            } catch (\Exception $ex) {}
            $session->setData('sociallogin', [
                'provider' => $this->model->getProvider(),
                'sociallogin_id' => $this->model->fetchSocialUserData('user_id'),
                'sl_form_key' => $formKey
            ]);
            $this->_slHelper()->getReferer(null);
        }
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->getResponse()->clearHeaders()->setHeader('Content-type', 'application/json', true);
            $this->getResponse()->setBody(json_encode([
                'redirectUrl' => $redirectUrl
            ]));
        } else {
            $action = '
                var slDoc = window.opener ? window.opener.document : document;
                slDoc.getElementById("sociallogin-referer").value = "' . htmlspecialchars(base64_encode($redirectUrl)) . '";
                slDoc.getElementById("sociallogin-submit").click();
            ';
            $body = $this->attacheJs('if(window.opener && window.opener.location &&  !window.opener.closed) { window.close(); }; ' . $action . ';');
            $this->getResponse()->setBody($body);
        }
    }
}