<?php
namespace Magenest\GoogleShopping\Block\Adminhtml\System\Config\Form;

use Magento\Framework\Exception\LocalizedException;

class GetAccessToken extends AbstractButton
{
    const CLIENT_ID = '157707452386-4mplmv0ubu46rk6pk170pvm4nvqdmqv2.apps.googleusercontent.com';
    const REDIRECT_URI = 'https://accounts.google.com/o/oauth2/v2/auth';
    const ACCESS_TYPE = 'offline';
    const INCLUDE_GRANTED_SCOPE  = 'true';
    const RESPONSE_TYPE = 'code';
    const STATE = '';
    const PROMPT = 'consent';
    const SCOPE = 'https://www.googleapis.com/auth/content';

    /** @var string  */
    protected $_buttonLabel = "Get Access Token";

    public function getUrlAuth()
    {
        try {
            $state = $this->getUrl("googleshopping/auth/index");
            $redirectUrl = "https://store.magenest.com/magenest/google/callback";
            return self::REDIRECT_URI
                . "?scope=" . self::SCOPE
                . "&access_type=" . self::ACCESS_TYPE
                . "&include_granted_scopes=" . self::INCLUDE_GRANTED_SCOPE
                . "&response_type=" . self::RESPONSE_TYPE
                . "&state=" . $state
                . "&prompt=" . self::PROMPT
                . "&redirect_uri=" . $redirectUrl
                . "&client_id=" . self::CLIENT_ID;
        } catch (\Exception $exception) {
            throw new LocalizedException(__($exception->getMessage()));
        }
    }
}
