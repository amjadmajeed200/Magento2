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

/**
 * Class Amazon
 * @package Cpl\SocialConnect\Model
 */
class Amazon extends \Cpl\SocialConnect\Model\Sociallogin
{
    /**
     * @var string
     */
    protected $type = 'amazon';

    /**
     * @var string
     */
    protected $apiBaseUrl = 'https://api.amazon.com';
    
    /**
     * @var string
     */
    protected $apiAuthorizeUrl = 'https://www.amazon.com/ap/oa';
    
    /**
     * @var string
     */
    protected $apiTokenUrl = 'https://api.amazon.com/auth/o2/token';
    
    /**
     * @var string
     */
    protected $apiTokenRequestUrl = 'https://api.amazon.com//auth/o2/tokeninfo?access_token=';

    /**
     * @var array
     */
    protected $fields = [
        'user_id' => 'id',
        'firstname' => 'firstname',
        'lastname' => 'lastname',
        'email' => 'email',
        'gender' => 'gender'
    ];

    /**
     * @inheritdoc
     */    
    public function _construct()
    {
        parent::_construct();
    }

    /**
     * @param $response
     * @return bool
     */
    public function fetchUserData($response)
    {
        if (empty($response)) {
            return false;
        }
        $data = $userData = [];
        $params = [
            'client_id' => $this->applicationId,
            'client_secret' => $this->secret,
            'grant_type' =>  'authorization_code',
            'code' => $response,
            'redirect_uri' => $this->redirectUri
        ];
        $apiToken = false;
        if ($response = $this->_apiCall($this->apiTokenUrl, $params, 'POST')) {
            $apiToken = json_decode($response, true);
            if (!$apiToken) {
                parse_str($response, $apiToken);
            }
            $data = json_decode($response, true);
            if(isset($data['access_token'])){
                $token = $data['access_token'];
                $this->_setToken($token);
                $reqUrl = $this->apiTokenRequestUrl . $this->urlEncode($token);
                $apiDetails = $this->httpGet($reqUrl);
                $data = json_decode($apiDetails);

                if ($data->aud != $this->_applicationId) {
                    throw new \Exception('The Access Token belongs to neither your Client ID nor App ID');
                }
                $userData = $this->_getUserInfo();
            }
        }
        if (!$this->userData = $this->_setSocialUserData($userData)) {
            return false;
        }
        return true;
    }

    /**
     * @return array
     * @throws \Exception
     */
    protected function _getUserInfo() {
        $userData = [];
        $this->_setCurlHeader();
        $url = $this->apiBaseUrl . '/user/profile';
        $response = $this->httpGet($url);
        $respObj = json_decode($response);
        $userData = $this->_setUserName($respObj->name);
        $userData['id']= $respObj->user_id;
        $userData['email']= $respObj->email ? : '';
        $userData['gender']= '';
        return $userData;
    }

    /**
     * @param $userData
     */
    protected function _setUserName($name) {
        if($name) {
            $nameArr = explode(' ', $name);
            $userData['firstname'] = (isset($nameArr[0])) ? $nameArr[0] : self::PROVIDER_FIRSTNAME_PLACEHOLDER;
            $userData['lastname'] = (isset($nameArr[1])) ? $nameArr[1] : self::PROVIDER_LASTNAME_PLACEHOLDER;
        } else {
            $userData['firstname'] = self::PROVIDER_FIRSTNAME_PLACEHOLDER;
            $userData['lastname'] = self::PROVIDER_LASTNAME_PLACEHOLDER;
        }
        return $userData;
    }

    /**
     * @param $data
     * @return array|bool
     */
    protected function _setSocialUserData($data)
    {
        if (empty($data['id'])) {
            return false;
        }
        return parent::_setSocialUserData($data);
    }

    /**
     * @param $value
     * @return string
     */
    private function urlEncode($value)
    {
        return str_replace('%7E', '~', rawurlencode($value));
    }
}