<?php
namespace Magenest\GoogleShopping\Controller\Auth;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Callback
 * @package Magenest\GoogleShopping\Controller\Auth
 */
class Callback extends \Magento\Framework\App\Action\Action
{
    const CLIENT_ID = '157707452386-4mplmv0ubu46rk6pk170pvm4nvqdmqv2.apps.googleusercontent.com';
    const CLIENT_SECRET = 'hFCJTo-dr3Ki9QFcxVHiFgTn';
    const GRANT_TYPE = 'authorization_code';
    const REDIRECT_URI = 'http://51ac8347eada.ngrok.io/googleshopping/auth/callback';
    const SCOPE = 'https://www.googleapis.com/auth/content';

    /** @var LoggerInterface  */
    protected $_logger;

    /** @var RedirectFactory  */
    protected $_redirectFactory;

    /** @var UrlInterface  */
    protected $_urlInterface;

    /**
     * Callback constructor.
     * @param LoggerInterface $logger
     * @param RedirectFactory $redirectFactory
     * @param UrlInterface $urlInterface
     * @param Context $context
     */
    public function __construct(
        LoggerInterface $logger,
        RedirectFactory $redirectFactory,
        UrlInterface $urlInterface,
        Context $context
    ) {
        $this->_logger = $logger;
        $this->_redirectFactory = $redirectFactory;
        $this->_urlInterface = $urlInterface;
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $baseUrl = $this->_urlInterface->getBaseUrl();
            $params = $this->getRequest()->getParams();
            $client = new \Google_Client();
            $client->setClientId(self::CLIENT_ID);
            $client->setClientSecret(self::CLIENT_SECRET);
            $client->setRedirectUri($baseUrl . "googleshopping/auth/callback");
            $client->setScopes(self::SCOPE);
            $client->setAccessType('offline');
            $urlClient = '';
            if (isset($params['code']) && isset($params['state'])) {
                $urlClient = $params['state'];
                $client->authenticate($params['code']);
                $accessToken = $client->getAccessToken();
                $data = [
                    'access_token' => $accessToken['access_token'] ?? null,
                    'refresh_token' => $accessToken['refresh_token'] ?? null,
                    'expires_in' => $accessToken['expires_in'] ?? null
                ];
                $stringParam = '';
                foreach ($data as $key => $value) {
                    if ($value === null) {
                        throw new LocalizedException(__("Something when wrong!"));
                    }
                    $stringParam .= $key . "=" . $value . "&";
                }
            }
            $urlClient .= "?" . $stringParam;
            $redirect = $this->_redirectFactory->create();
            $redirect->setPath($urlClient);
            return $redirect;
        } catch (\Exception $exception) {
            $this->_logger->debug($exception->getMessage());
        }
    }
}
