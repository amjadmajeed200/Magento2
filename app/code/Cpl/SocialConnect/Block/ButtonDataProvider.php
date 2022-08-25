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

namespace Cpl\SocialConnect\Block;

use Cpl\SocialConnect\Block\SocialLogin;

/**
 * Class ButtonDataProvider
 * @package Cpl\SocialConnect\Block
 */
class ButtonDataProvider extends SocialLogin
{
    /**
     * @var bool
     */
    protected $content = false;

    /**
     * @return array
     */
    public function getButtons() {
        $buttons = [];
        foreach ($this->socialMedia as $mediaType => $endPoint) {
            if($this->helper->getSocialConfig($mediaType, $mediaType.'_enabled')){
                $appId = $this->encryptorInterface->decrypt($this->helper->getSocialConfig($mediaType, 'app_id'));
                $url = $endPoint . $this->getEndpointParams($mediaType, $appId);
                $buttons[$mediaType]['api_id'] = $appId;
                $buttons[$mediaType]['endpoint'] = $url;
            }
        }
        return $buttons;
    }

    /**
     * @param $type
     * @param $clientId
     */
    protected function getEndpointParams($type, $clientId) {
        $params = '';
        if($type == 'fb') {
            $redirectUrl = $this->getUrl('sociallogin/account/login', ['type' => $type]);
            $params = 'client_id='.$clientId.'&display=popup&redirect_uri='.$redirectUrl.'&scope=email';
        } elseif($type == 'amazon') {
            $redirectUrl = $this->getUrl('sociallogin/account/login', ['type' => $type]);
            $params = 'client_id='.$clientId.'&redirect_uri='.$redirectUrl.'&response_type=code&scope=profile';
        } elseif($type == 'google') {
            $redirectUrl = $this->getUrl('sociallogin/account/login', ['type' => $type]);
            $params = 'client_id='.$clientId.'&redirect_uri='.$redirectUrl.'&response_type=code';
        } else {
            $params = '';
        }
        return $params;
    }

    /**
     * @param bool $flag
     */
    public function getContent($flag = true)
    {
        $this->content = $flag;
    }

    /**
     * @return string
     */
    public function getEmailFormUrl()
    {
        return $this->getUrl('sociallogin/account/email', ['_secure' => true]);
    }

    /**
     * @return string
     */    
    public function afterToHtml($html)
    {
        if ($this->content && trim($html)) {
            $html = '<script type="text/javascript">'
                . 'window.' . $this->content . ' = \'' . str_replace(["\n", 'script'], ['', "scri'+'pt"], $this->escapeJs($html)) . '\';'
                . '</script>';
        } elseif($this->content == 'socialloginButtons' && !$html) {
            $html = '<script type="text/javascript">'
                . 'window.' . $this->content . ' = \'' . str_replace(["\n", 'script'], ['', "scri'+'pt"], $this->escapeJs('')) . '\';'
                . '</script>';
        }
        return parent::afterToHtml($html);
    }

    /**
     * @return mixed
     */
    public function isSecure()
    {
        return (bool) $this->helper->isSecure();
    }
}