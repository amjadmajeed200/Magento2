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

use Cpl\SocialConnect\Model\Sociallogin;

/**
 * Class Fb
 * @package Cpl\SocialConnect\Model
 */
class Fb extends Sociallogin
{
    /**
     * @var string
     */
    protected $type = 'fb';

    /**
     * @var string
     */
    protected $url = 'https://www.facebook.com/dialog/oauth';

    /**
     * @var string
     */
    protected $apiTokenUrl = 'https://graph.facebook.com/oauth/access_token';

    /**
     * @var string
     */
    protected $apiGraphUrl = 'https://graph.facebook.com/me';

    /**
     * @var array
     */
    protected $fields = [
        'user_id' => 'id',
        'firstname' => 'first_name',
        'lastname' => 'last_name',
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
        $data = [];
        $params = [
            'client_id' => $this->applicationId,
            'client_secret' => $this->secret,
            'code' => $response,
            'redirect_uri' => $this->redirectUri
        ];
        $apiToken = false;
        if ($response = $this->_apiCall($this->apiTokenUrl, $params, 'GET')) {
            $apiToken = json_decode($response, true);
            if (!$apiToken) {
                parse_str($response, $apiToken);
            }
        }
        if (isset($apiToken['access_token'])) {
            $params = [
                'access_token' => $apiToken['access_token'],
                'fields' => implode(',', $this->fields)
            ];
            if ($response = $this->_apiCall($this->apiGraphUrl, $params, 'GET')) {
                $data = json_decode($response, true);
            }
        }
        if (!$this->userData = $this->_setSocialUserData($data)) {
            return false;
        }
        return true;
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
}