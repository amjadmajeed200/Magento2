<?php
namespace Magenest\GoogleShopping\Model;

use Magenest\GoogleShopping\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;
use Magenest\GoogleShopping\Model\ResourceModel\Accounts\CollectionFactory as AccountsCollection;
use Magenest\GoogleShopping\Model\AccountsFactory as AccountsModel;
use Magenest\GoogleShopping\Model\ResourceModel\Accounts as AccountsResource;
use PHPUnit\Util\Exception;

class Client
{
    const URI = 'https://www.googleapis.com';
    const VERSION = 'v2.1';

    protected $_googleClient = null;

    /** @var Json  */
    protected $_jsonFramework;

    /** @var CurlFactory  */
    protected $_curlFactory;

    /** @var UrlInterface  */
    protected $_url;

    /** @var ScopeConfigInterface  */
    protected $_scopeConfig;

    /** @var ComponentRegistrar  */
    protected $_componentRegistrar;

    /** @var AccountsCollection */
    protected $accountsCollection;

    /** @var AccountsModel */
    protected $accountsModel;

    /** @var AccountsResource */
    protected $accountsResource;

    /**
     * Client constructor.
     *
     * @param Json $jsonFramework
     * @param CurlFactory $curlFactory
     * @param UrlInterface $url
     * @param ScopeConfigInterface $scopeConfig
     * @param ComponentRegistrar $componentRegistrar
     * @param AccountsCollection $accountsCollection
     * @param Accounts $accountsModel
     * @param AccountsResource $accountsResource
     */
    public function __construct(
        Json $jsonFramework,
        CurlFactory $curlFactory,
        UrlInterface $url,
        ScopeConfigInterface $scopeConfig,
        ComponentRegistrar $componentRegistrar,
        AccountsCollection $accountsCollection,
        AccountsModel $accountsModel,
        AccountsResource $accountsResource
    ) {
        $this->_jsonFramework = $jsonFramework;
        $this->_curlFactory = $curlFactory;
        $this->_url = $url;
        $this->_scopeConfig = $scopeConfig;
        $this->_componentRegistrar = $componentRegistrar;
        $this->accountsCollection = $accountsCollection;
        $this->accountsModel = $accountsModel;
        $this->accountsResource = $accountsResource;
    }

    /**
     * @param $url
     * @param string $method
     * @param array $params
     * @return array|bool|float|int|mixed|string|null
     * @throws LocalizedException
     */
    protected function _httpRequest($url, $method = 'GET', $params = [])
    {
        /** @var  $curl */
        $curl = $this->_curlFactory->create();
        $curl->setConfig([
            'timeout'   => 2,
            'useragent' => 'Magenest Google Shopping',
            'referer'   => $this->_url->getUrl('*/*/*')
        ]);
        $headers = ["Content-Type" => "application/json", "Content-Length" => "2000"];
        switch ($method) {
            case 'GET':
                if (!empty($params)) {
                    $url .= '?' . http_build_query($params);
                }
                $curl->write($method, $url);
                break;
            case 'POST':
                $curl->write($method, $url, '1.1', $headers, $params);
                break;
        }
        $response = $curl->read();
        $curl->close();
        if ($response === false) {
            throw new LocalizedException(__('HTTP error occurred while issuing request. Please contact Administrator for more information.'));
        }
        $response = preg_split('/^\r?$/m', $response, 2);
        if (count($response) != 2) {
            $decodedResponse = trim($response[0]);
        } else {
            $response        = trim($response[1]);
            $decodedResponse = $this->_jsonFramework->unserialize($response);
        }
        if (is_array($decodedResponse) && !empty($decodedResponse)) {
            if (isset($decodedResponse['error'])) {
                $error = $decodedResponse['error'];
                throw new LocalizedException(__(implode(', ', $error)));
            }
            if (isset($decodedResponse['data'])) {
                // reverser list photo to arrange the latest photos for the highest id
                $decodedResponse['data'] = array_reverse($decodedResponse['data']);
            }
            return $decodedResponse;
        } else {
            return [];
        }
    }

    /**
     * @param $method
     * @param $url
     * @param $accessToken
     * @param $params
     * @return string|null
     * @throws \Zend_Http_Client_Exception
     */
    public function makeRequest($method, $url, $accessToken = [], $params = [])
    {
        $headers = ["Content-Type" => "application/json"];
        if (is_array($accessToken) && isset($accessToken['access_token'])) {
            $headers = array_merge($headers, ["Authorization" => "Bearer " . $accessToken['access_token']]);
        }
        $client = new \Zend_Http_Client($url);
        $client->setHeaders($headers);
        if ($method != \Zend_Http_Client::GET) {
            $client->setParameterPost($params);
            if (isset($headers['Content-Type']) && $headers['Content-Type'] == 'application/json') {
                $client->setEncType('application/json');
                $params = $this->_jsonFramework->serialize($params);
                $client->setRawData($params);
            }
        }
        $response = $client->request($method)->getBody();
        return $response;
    }

    /**
     * @param $accessToken
     * @return array|array[]|bool|bool[]|float|float[]|int|int[]|null[]|string|string[]|null
     * @throws LocalizedException
     */
    public function getAccountInfo($accessToken)
    {
        //api: content/v2.1/accounts/authinfo
        $endpoint = self::URI . "/content/" . self::VERSION . "/accounts/authinfo";

//        $accessToken = $this->getStoreConfig(Data::GOOGLE_TOKEN);
        $query = [
            'access_token' => "$accessToken"
        ];
        $url = $endpoint . '?' . http_build_query($query);
        $response = $this->_httpRequest($url);
        $individualAccount = [];
        $subAccounts = [];
        $result = [];

        if (is_array($response) && isset($response['accountIdentifiers']) && !empty($response['accountIdentifiers'])) {
            $accountIdentifiers = $response['accountIdentifiers'];

            foreach ($accountIdentifiers as $accountIdentifier) {
                if (is_array($accountIdentifier)
                    && !isset($accountIdentifier['merchantId'])
                    && isset($accountIdentifier['aggregatorId'])
                ) {
                    $subAccounts = array_merge($subAccounts, $this->fetchSubAccounts($query, $accountIdentifier['aggregatorId']));
                } else {
                    $individualAccount[] = $this->getIndividualAccount($query, $accountIdentifier['merchantId']);
                }
            }
            $result = array_merge($subAccounts, $individualAccount);
            $this->updateAccounts($result);
        }

        return $result;
    }

    /**
     * @param $query
     * @param $merchant
     * @return array|array[]|bool[]|float[]|int[]|null[]|string[]
     * @throws LocalizedException
     */
    public function getIndividualAccount($query, $merchant) {
        //api: content/v2.1/accounts/{merchantId}/accounts/{accountId}
        $endpointForListAccount = self::URI . "/content/" . self::VERSION . "/" . $merchant . "/accounts/". $merchant;
        $urlGetMerchantList = $endpointForListAccount . '?' . http_build_query($query);
        return [$this->_httpRequest($urlGetMerchantList)];
    }

    /**
     * @param $accounts
     * @return void
     */
    public function updateAccounts($accounts) {
        $accountModel = $this->accountsModel->create()->getCollection();
        $connection = $accountModel->getConnection();
        $tableName = $accountModel->getMainTable();
        $connection->truncateTable($tableName);

        if (!empty($accounts)) {
            foreach ($accounts as $account) {
                try {
                    $newModel = $this->accountsModel->create();
                    $newModel->addData(
                        [
                            'name' => $account['name'],
                            'merchant_id' => (int)$account['id']
                        ]
                    );
                    $this->accountsResource->save($newModel);
                } catch (\Exception $exception) {
                    throw new Exception(__('Cant save the merchants, please try again'));
                }
            }
        }
    }

    /**
     * @param $query
     * @param $aggregator
     * @return array|bool|float|int|mixed|string|null
     * @throws LocalizedException
     */
    public function fetchSubAccounts($query, $aggregator) {
        //api: content/v2.1/accounts/{merchantId}/accounts
        $endpointForListAccount = self::URI . "/content/" . self::VERSION . "/" . $aggregator . "/accounts";
        $urlGetMerchantList = $endpointForListAccount . '?' . http_build_query($query);
        $response = $this->_httpRequest($urlGetMerchantList);
        return $response['resources'];
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function getProductBath()
    {
        //api: content/v2.1/products/batch
        $endpoint = self::URI . "/content/" . self::VERSION . "/products/batch";
        $accessToken = $this->getStoreConfig(Data::GOOGLE_TOKEN);
        $query = [
            'access_token' => "$accessToken"
        ];
        $url = $endpoint . '?' . http_build_query($query);
        $response = $this->_httpRequest($url, 'POST');
    }

    /**
     * @param $paramter
     * @return array|bool|float|int|mixed|string|null
     * @throws \Google_Exception
     * @throws \Zend_Http_Client_Exception
     */
    public function insertProduct($paramter)
    {
        //api: content/v2.1/{merchantId}/products
        $accessToken = $this->getAccessToken();
        $merchantId = $this->getStoreConfig(Data::MERCHANT_ID);
        $endpoint = self::URI . "/content/" . self::VERSION . "/" . $merchantId . "/products";
        $response = $this->makeRequest(\Zend_Http_Client::POST, $endpoint, $accessToken, $paramter);
        return $this->handleResponse($response);
    }

    /**
     * @param $response
     * @return array|bool|float|int|mixed|string|null
     * @throws \Exception
     */
    public function handleResponse($responses)
    {
        try {
            $results = [];
            foreach ($responses as $key => $response) {
                $results[$key] = $this->_jsonFramework->unserialize($response);
            }
            return $results;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }
    /**
     * @param $xmlPath
     * @return mixed
     */
    public function getStoreConfig($xmlPath)
    {
        return $this->_scopeConfig->getValue($xmlPath);
    }

    /**
     * @return array
     * @throws \Google_Exception
     */
    private function getAccessToken()
    {
        $accessToken = $this->getStoreConfig(Data::GOOGLE_TOKEN);
        $refreshToken = $this->getStoreConfig(Data::GOOGLE_REFRESH_TOKEN);
        $expiresIn = $this->getStoreConfig(Data::GOOGLE_TOKEN_EXPIRESIN);
        $data = [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_in' => $expiresIn
        ];
        $googleClientModel = $this->getGoogleClientModel();
        $googleClientModel->setAccessToken($data);
        if ($googleClientModel->isAccessTokenExpired()) {
            $refreshToken = $this->getStoreConfig(Data::GOOGLE_REFRESH_TOKEN);
            $googleClientModel->refreshToken($refreshToken);
        }
        return $googleClientModel->getAccessToken();
    }

    /**
     * @return \Google_Client
     * @throws \Google_Exception
     */
    private function getGoogleClientModel()
    {
        if ($this->_googleClient == null) {
            $client = new \Google_Client();
            $clientSecretJson = $this->_componentRegistrar->getPath('module', 'Magenest_GoogleShopping') . '/lib/client_secret.json';
            $client->setAuthConfig($clientSecretJson);
            $this->_googleClient = $client;
        }
        return $this->_googleClient;
    }

    /**
     * @param $products
     * @param $merchantIds
     * @return array
     * @throws \Google_Exception
     * @throws \Zend_Http_Client_Exception
     */
    public function productCustomBath($products, $merchantIds)
    {
        //api: content/v2.1/products/batch
        $response = [];
        foreach ($merchantIds as $merchantId) {
            $entries = [];
            $i = 0;
            foreach ($products as $product) {
                $entries[] = [
                    "batchId" => $i,
                    "merchantId" => $merchantId,
                    "method" => "insert",
                    "product" => $product
                ];
                $i++;
            }
            $paramter = [
                "entries" => $entries
            ];
            $endpoint = self::URI . "/content/" . self::VERSION . "/products/batch";
            $accessToken = $this->getAccessToken();
            $response[$merchantId] = $this->makeRequest(\Zend_Http_Client::POST, $endpoint, $accessToken, $paramter);
        }
        return $response;
    }

    /**
     * @param $productIds
     * @param $accounts
     * @return array|bool|float|int|mixed|string|null
     * @throws \Google_Exception
     * @throws \Zend_Http_Client_Exception
     */
    public function productStatusBath($productIds, $accounts)
    {
        //api: content/v2.1/productstatuses/batch
        $merchantIds = $accounts;
        $entries = [];
        $i = 0;
        $responses = [];
        foreach ($merchantIds as $merchantId) {
            foreach ($productIds as $productId) {
                $entries[] = [
                    "batchId" => $i,
                    "merchantId" => $merchantId,
                    "method" => "get",
                    "productId" => $productId,
                    "includeAttributes" => true
                ];
                $i++;
            }
            $paramter = [
                "entries" => $entries
            ];
            $endpoint = self::URI . "/content/" . self::VERSION . "/productstatuses/batch";
            $accessToken = $this->getAccessToken();
            $responses[$merchantId] = $this->makeRequest(\Zend_Http_Client::POST, $endpoint, $accessToken, $paramter);
        }

        return $this->handleResponse($responses);
    }

    /**
     * Get accounts merchant id
     *
     * @return array
     */
    public function getAccounts(){
        $result = [];
        $accounts = $this->accountsCollection->create();
        if (!empty($accounts->getItems())) {
            foreach ($accounts->getItems() as $account) {
                $result[] = [
                    'merchantId' => $account->getData('merchant_id'),
                    'name' => $account->getData('name')
                ];
            }
        }
        return $result;
    }

}
