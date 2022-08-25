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

namespace Cpl\SocialConnect\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManager;
use Magento\Store\Model\Store;
use Magento\Framework\Encryption\Encryptor;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Config;
use Magento\Customer\Model\Attribute;
use Magento\Framework\Math\Random;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\EmailNotificationInterface;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Cpl\SocialConnect\Helper\Data;

/**
 * Class Sociallogin
 * @package Cpl\SocialConnect\Model
 */
class Sociallogin extends AbstractModel
{

    const PROVIDER_FIRSTNAME_PLACEHOLDER = 'FirstName';
    const PROVIDER_LASTNAME_PLACEHOLDER = 'LastName';

    /**
     * @var null | string
     */
    protected $type = '';

    /**
     * @var string
     */
    protected $accessToken = '';

    /**
     * @var bool
     */
    protected $curlHeader = false;

    /**
     * @var EmailNotificationInterface
     */
    protected $emailNotificationInterface;

    /**
     * @var array
     */
    private $headerArray = [];

    /**
     * @var null | int
     */
    protected $websiteId = '';

    /**
     * @var null | string
     */
    protected $redirectUri = '';

    /**
     * @var array
     */
    protected $userData = [];

    /**
     * @var null | string | int
     */
    protected $applicationId = '';

    /**
     * @var null | string | int
     */
    protected $secret = '';

    /**
     * @var string
     */
    protected $rpCode = 'code';

    /**
     * @var null | array | string
     */
    protected $callInfo = null;

    /**
     * @var array
     */
    protected $gender = [
        'male',
        'female'
    ];

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var StoreManager
     */
    protected $storeManager;

    /**
     * @var Store
     */
    protected $store;

    /**
     * @var Encryptor
     */
    protected $encryptor;

    /**
     * @var Customer
     */
    protected $customer;

    /**
     * @var Config
     */
    protected $eavConfig;

    /**
     * @var Attribute
     */
    protected $attribute;

    /**
     * @var Random
     */
    protected $random;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var File
     */
    protected $ioFile;

    /**
     * @var DirectoryList
     */
    protected $dir;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var CustomerInterfaceFactory
     */
    protected $customerDataFactory;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * Sociallogin constructor.
     * 
     * @param Context                       $context
     * @param Registry                      $registry
     * @param StoreManager                  $storeManager
     * @param Store                         $store
     * @param Encryptor                     $encryptor
     * @param Customer                      $customer
     * @param Config                        $eavConfig
     * @param Attribute                     $attribute
     * @param Random                        $random
     * @param Filesystem                    $filesystem
     * @param File                          $ioFile
     * @param DirectoryList                 $dir
     * @param Session                       $customerSession
     * @param CustomerRepositoryInterface   $customerRepository
     * @param CustomerInterfaceFactory      $customerData
     * @param CustomerFactory               $customerFactory
     * @param AccountManagementInterface    $accountManagement
     * @param EmailNotificationInterface    $emailNotificationInterface
     * @param AbstractResource|null         $resource
     * @param AbstractDb|null               $resourceCollection
     * @param Data                          $slHelper
     * @param array                         $data
     */
    public function __construct(
        Context                     $context,
        Registry                    $registry,
        StoreManager                $storeManager,
        Store                       $store,
        Encryptor                   $encryptor,
        Customer                    $customer,
        Config                      $eavConfig,
        Attribute                   $attribute,
        Random                      $random,
        Filesystem                  $filesystem,
        File                        $ioFile,
        DirectoryList               $dir,
        Session                     $customerSession,
        CustomerRepositoryInterface $customerRepository,
        CustomerInterfaceFactory    $customerData,
        CustomerFactory             $customerFactory,
        AccountManagementInterface  $accountManagement,
        EmailNotificationInterface  $emailNotificationInterface,
        AbstractResource            $resource = null,
        AbstractDb                  $resourceCollection = null,
        Data                        $slHelper,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->storeManager = $storeManager;
        $this->store = $store;
        $this->encryptor = $encryptor;
        $this->customer = $customer;
        $this->eavConfig = $eavConfig;
        $this->attribute = $attribute;
        $this->random = $random;
        $this->filesystem = $filesystem;
        $this->ioFile = $ioFile;
        $this->dir = $dir;
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->customerDataFactory = $customerData;
        $this->customerFactory = $customerFactory;
        $this->accountManagement = $accountManagement;
        $this->emailNotificationInterface = $emailNotificationInterface;
        $this->helper = $slHelper;
    }

    /**
     * @return void
     * @throws \Exception
     */    
    public function _construct()
    {
        $this->_init('Cpl\SocialConnect\Model\ResourceModel\Sociallogin');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->storeManager = !empty($this->storeManager) ? $this->storeManager : $objectManager->get(\Magento\Store\Model\StoreManager::class);
        $this->helper = !empty($this->helper) ? $this->helper : $objectManager->get(\Cpl\SocialConnect\Helper\Data::class);
        $this->encryptor = !empty($this->encryptor) ? $this->encryptor : $objectManager->get(\Magento\Framework\Encryption\Encryptor::class);
        $this->websiteId = $this->storeManager->getWebsite()->getId();
        $this->redirectUri = $this->helper->getCallback($this->type);
        $this->applicationId = $this->encryptor->decrypt(trim($this->helper->getConfig($this->helper->getConfigSectionId() . '/' . $this->type . '/app_id')));
        $this->secret = $this->encryptor->decrypt(trim($this->helper->getConfig($this->helper->getConfigSectionId() . '/' . $this->type . '/app_secret')));
    }

    /**
     * @param $customerId
     * @return $this
     * @throws \Exception
     */
    public function setCustomerByUser($customerId)
    {
        $data = [
            'type' => $this->type,
            'sociallogin_id' => $this->fetchSocialUserData('user_id'),
            'customer_id' => $customerId
        ];
        $this->addData($data)->save();
        return $this;
    }

    /**
     * @return int|mixed
     */
    public function getCustomerIdByUser()
    {
        $customerId = $this->_getCustomerIdByUser();
        if (!$customerId && $this->helper->isGlobalScope()) {
            $customerId = $this->_getCustomerIdByUser(true);
        }
        return $customerId;
    }

    /**
     * @param bool $useGlobalScope
     * @return int|mixed
     */
    protected function _getCustomerIdByUser($globalScope = false)
    {
        $customerId = false;
        if ($this->fetchSocialUserData('user_id')) {
            $collection = $this->getCollection()
                ->join(['ce' => 'customer_entity'], 'ce.entity_id = main_table.customer_id', null)
                ->addFieldToFilter('main_table.type', $this->type)
                ->addFieldToFilter('main_table.sociallogin_id', $this->fetchSocialUserData('user_id'))
                ->setPageSize(1);

            if ($globalScope === false) {
                $collection->addFieldToFilter('ce.website_id', $this->websiteId);
            }

            $customerId = $collection->getFirstItem()->getData('customer_id');
        }
        return $customerId;
    }

    /**
     * @param $email
     * @param null $websiteId
     * @return \Magento\Customer\Model\Customer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomerByEmail($email, $websiteId = null)
    {
        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $this->customerFactory->create();
        $customer->setWebsiteId($websiteId ?: $this->storeManager->getWebsite()->getId());
        $customer->loadByEmail($email);
        return $customer;
    }

    /**
     * @return int
     */
    public function getCustomerIdByUserEmail()
    {
        $customerId = $this->_getCustomerIdByUserEmail();
        if (!$customerId && $this->helper->isGlobalScope()) {
            $customerId = $this->_getCustomerIdByUserEmail(true);
        }
        return $customerId;
    }

    /**
     * @param bool $globalScope
     * @return int
     */
    protected function _getCustomerIdByUserEmail($globalScope = false)
    {
        $customerId = 0;
        if ($this->fetchSocialUserData('email')) {
            $collection = $this->customer->getCollection()
                ->addFieldToFilter('email', $this->fetchSocialUserData('email'))
                ->setPageSize(1);
            if ($globalScope === false) {
                $collection->addFieldToFilter('website_id', $this->websiteId);
            }
            $customerId = $collection->getFirstItem()->getId();
        }
        return $customerId;
    }

    /**
     * @param $customerId
     * @param bool $globalScope
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function getUsersByCustomerId($customerId, $globalScope = false)
    {
        $collection = $this->getCollection()
            ->join(['ce' => 'customer_entity'], 'ce.entity_id = main_table.customer_id', ['firstname', 'lastname', 'email'])
            ->addFieldToFilter('main_table.customer_id', $customerId);
        if ($globalScope === false) {
            $collection->addFieldToFilter('ce.website_id', $this->websiteId);
        }
        return $collection;
    }

    /**
     * @param $id
     * @return string
     * @throws \Exception
     */
    public function unlinkUser($id)
    {
        if (!$id) {
            return 'No link id provided';
        }
        $link = $this->load($id);
        $link->delete();
        return true;
    }

    /**
     * @return int|mixed
     */
    public function createNewCustomer()
    {
        $customerId = false;
        $errors = [];
        $customer = $this->customer->setId(null);
        try {
            $customer->setData($this->fetchSocialUserData())
                ->setConfirmation($this->fetchSocialUserData('password'))
                ->setPasswordConfirmation($this->fetchSocialUserData('password'))
                ->setData('is_active', 1)
                ->getGroupId();
            $errors = $this->validate($customer);
            $correctEmail = \Zend_Validate::is($this->fetchSocialUserData('email'), 'EmailAddress');
            if ((empty($errors) || $this->helper->validateIgnore()) && $correctEmail) {
                $customerId = $customer->save()->getId();
                $customer->setConfirmation(null)->save();
            }
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }
        $this->setCustomer($customer);
        $this->setErrors($errors);
        return $customerId;
    }

    /**
     * @param $data
     * @param $store
     * @return mixed
     * @throws \Exception
     */
    public function createCustomerSocial($data, $store)
    {
        $errors = [];
        /** @var CustomerInterface $customer */
        $customer = $this->customerDataFactory->create();
        $customer->setFirstname($data['firstname'])
            ->setLastname($data['lastname'])
            ->setEmail($data['email'])
            ->setStoreId($store->getId())
            ->setWebsiteId($store->getWebsiteId())
            ->setCreatedIn($store->getName());
        try {
            $customer = $this->customerRepository->save($customer);
            $newPasswordToken = $this->random->getUniqueHash();
            $this->accountManagement->changeResetPasswordLinkToken($customer, $newPasswordToken);
            try {
                $this->getEmailNotification()->newAccount($customer, EmailNotificationInterface::NEW_ACCOUNT_EMAIL_REGISTERED_NO_PASSWORD, '', $store->getId());
            } catch (\Exception $ex) {}
            $this->setUser($data['identifier'], $customer->getId(), $data['type']);
        } catch (AlreadyExistsException $e) {
            $errors[] = $e->getMessage();
            throw new InputMismatchException(
                __('A customer with the same email already exists in an associated website.')
            );
        } catch (\Exception $e) {
            if ($customer->getId()) {
                $this->_registry->register('isSecureArea', true, true);
                $this->customerRepository->deleteById($customer->getId());
            }
            $errors[] = $e->getMessage();
            throw $e;
        }
        /** @var Customer $customer */
        $customer = $this->customerFactory->create()->load($customer->getId());
        $this->setErrors($errors);
        return $customer;
    }

    /**
     * Get email notification
     *
     * @return EmailNotificationInterface
     */
    private function getEmailNotification()
    {
        return $this->emailNotificationInterface;
    }

    /**
     * @param $identifier
     * @param $customerId
     * @param $type
     * @return $this
     * @throws \Exception
     */
    public function setUser($identifier, $customerId, $type)
    {
        $this->setData([
            'sociallogin_id' => $identifier,
            'customer_id' => $customerId,
            'type' => $type
        ])
            ->setId(null)
            ->save();
        return $this;
    }

    /**
     * @param $customer
     * @return array
     */
    protected function validate($customer)
    {
        $errorArr = [];
        $valid = $customer->validate();
        if (true !== $valid) {
            $errorArr = $valid;
        }
        return $errorArr;
    }

    /**
     * @return string
     */
    public function getRpCode()
    {
        return $this->rpCode;
    }

    /**
     * @param $key
     * @param null $value
     * @return $this
     */
    public function setUserData($key, $value = null)
    {
        if (is_array($key)) {
            $this->userData = array_merge($this->userData, $key);
        } else {
            $this->userData[$key] = $value;
        }
        return $this;
    }

    /**
     * @param null $key
     * @return array|mixed|null
     */
    public function fetchSocialUserData($key = null)
    {
        if ($key !== null) {
            return isset($this->userData[$key]) ? $this->userData[$key] : null;
        }
        return $this->userData;
    }

    /**
     * @param $data
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _setSocialUserData($data)
    {
        $_data = [];
        foreach ($this->fields as $customerField => $userField) {
            $_data[$customerField] = ($userField && isset($data[$userField])) ? $data[$userField] : null;
        }
        if (empty($_data['firstname'])) {
            $_data['firstname'] = $this->_setFirstName();
        }
        if (empty($_data['lastname'])) {
            $_data['lastname'] = $this->_setLastName();
        }
        if (!empty($_data['gender'])) {
            $genderData = $this->eavConfig->getAttribute('customer', 'gender');
            if ($genderData && $genderOptions = $genderData->getSource()->getAllOptions(false)) {
                switch ($_data['gender']) {
                    case $this->_gender[0]:
                        $_data['gender'] = $genderOptions[0]['value'];
                        break;
                    case $this->_gender[1]:
                        $_data['gender'] = $genderOptions[1]['value'];
                        break;
                    default:
                        $_data['gender'] = 0;
                }
            } else {
                $_data['gender'] = 0;
            }
        } else {
            $_data['gender'] = 0;
        }
        $_data['taxvat'] = '0';
        $_data['password'] = $this->_getRandomPassword();
        return $_data;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _setEmail($userData)
    {
        $email = $userData['id'] . '@' . $this->type . '.com';
        return $email;
    }

    /**
     * @param $userData
     * @return mixed
     */
    protected function _setFirstName()
    {
        return self::PROVIDER_FIRSTNAME_PLACEHOLDER;
    }

    /**
     * @param $userData
     * @return mixed
     */
    protected function _setLastName()
    {
        return self::PROVIDER_LASTNAME_PLACEHOLDER;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getRandomPassword()
    {
        $len = 6;
        return $this->random->getRandomString($len);
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function sendMail()
    {
        $storeId = $this->storeManager->getStore()->getId();
        $this->customer->sendNewAccountEmail('registered', '', $storeId);
        return true;
    }

    /**
     * @return null|string
     */
    public function getProvider()
    {
        return $this->type;
    }

    /**
     * @param $url
     * @param array $params
     * @param string $method
     * @param null $curlResource
     * @return mixed|null
     */
    protected function _apiCall($url, $paramsArr = [], $method = 'POST', $curlResource = null, $headerArr = [])
    {
        $result = null;
        $paramsStr = is_array($paramsArr) ? urlencode(http_build_query($paramsArr)) : urlencode($paramsArr);
        $curl = is_resource($curlResource) ? $curlResource : curl_init();
        if ($method == 'POST') {
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, urldecode($paramsStr));
            if(!empty($headerArr)) {
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headerArr);
            }
        } else {
            if ($paramsStr) {
                $url .= '?' . urldecode($paramsStr);
            }
            curl_setopt($curl, CURLOPT_URL, $url);
        }
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    /**
     * @param $token
     */
    protected function _setToken($token)
    {
        $this->accessToken = $token;
    }

    /**
     * @param $url
     * @return mixed|string
     * @throws \Exception
     */
    public function httpGet($url)
    {
        $ch = $this->commonCurlParams($url);
        if ($this->curlHeader) {
            $this->headerArray[] = 'Authorization: Bearer ' . $this->accessToken;
        }
        $response = $this->execute($ch);
        return $response;
    }

    /**
     * @param $ch
     * @return mixed|string
     * @throws \Exception
     */
    protected function execute($ch)
    {
        $response = '';
        $this->headerArray[] = 'Expect:';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headerArray);
        $response = curl_exec($ch);
        if ($response === false) {
            $error_msg = "Unable to post request, underlying exception of " . curl_error($ch);
            curl_close($ch);
            throw new \Exception($error_msg);
        }
        curl_close($ch);
        return $response;
    }

    /**
     * @param $url
     * @return resource
     */
    protected function commonCurlParams($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PORT, 443);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        return $ch;
    }

    /**
     * @return void
     */    
    protected function _setCurlHeader()
    {
        $this->curlHeader = true;
    }
}