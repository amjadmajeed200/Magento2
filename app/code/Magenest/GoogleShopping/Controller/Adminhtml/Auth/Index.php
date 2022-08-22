<?php
namespace Magenest\GoogleShopping\Controller\Adminhtml\Auth;

use Magenest\GoogleShopping\Helper\Data as HelperData;
use Magenest\GoogleShopping\Model\Client;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Cache\Manager as CachedManager;
use Magento\Framework\App\Cache\Type\Config;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Index
 * @package Magenest\GoogleShopping\Controller\Adminhtml\Auth
 */
class Index extends \Magento\Backend\App\Action
{
    /** @var Client  */
    protected $_clientModel;

    /** @var LoggerInterface  */
    protected $_logger;

    /** @var WriterInterface  */
    protected $_writerInterface;

    /** @var CachedManager  */
    protected $_cacheManager;

    /**
     * Index constructor.
     * @param Client $clientModel
     * @param LoggerInterface $logger
     * @param WriterInterface $writerInterface
     * @param CachedManager $cacheManager
     * @param Context $context
     */
    public function __construct(
        Client $clientModel,
        LoggerInterface $logger,
        WriterInterface $writerInterface,
        CachedManager $cacheManager,
        Context $context
    ) {
        $this->_clientModel = $clientModel;
        $this->_logger = $logger;
        $this->_writerInterface = $writerInterface;
        $this->_cacheManager = $cacheManager;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        try {
            $params = $this->getRequest()->getParams();
            if (isset($params['access_token'])) {
                $accessToken = urldecode($params['access_token']);
                $refreshToken = urldecode($params['refresh_token']);
                $expiresIn = urldecode($params['expires_in']);
                $this->_writerInterface->save(HelperData::GOOGLE_TOKEN, $accessToken);
                $this->_writerInterface->save(HelperData::GOOGLE_REFRESH_TOKEN, $refreshToken);
                $this->_writerInterface->save(HelperData::GOOGLE_TOKEN_EXPIRESIN, $expiresIn);
                $this->cleanConfigCache();
                $accountIdentifiers = $this->_clientModel->getAccountInfo($accessToken);
                if (empty($accountIdentifiers)) {
                    throw new \Exception(__("Please access with the account merchant center."));
                }
                $this->messageManager->addSuccessMessage(__("Get access token successfully!"));
            }
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage(__("Something when wrong. Please try again!"));
            $this->_logger->debug($exception->getMessage());
        }
        $this->_redirect(HelperData::GOOGLESHOPPING_CONFIGURATION_SECTION);
    }

    /** Clean config cache */
    private function cleanConfigCache()
    {
        $this->_cacheManager->clean([Config::TYPE_IDENTIFIER]);
    }
}
