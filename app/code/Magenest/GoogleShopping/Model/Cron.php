<?php
namespace Magenest\GoogleShopping\Model;

use Magenest\GoogleShopping\Helper\Data;
use Magenest\GoogleShopping\Model\ResourceModel\GoogleFeed as GoogleFeedResource;
use Magenest\GoogleShopping\Model\ResourceModel\GoogleFeed\CollectionFactory as GoogleFeedCollectionFactory;
use Magenest\GoogleShopping\Model\ResourceModel\GoogleFeedIndex as GoogleFeedIndexResource;
use Magenest\GoogleShopping\Model\ResourceModel\GoogleFeedIndex\CollectionFactory as GoogleFeedIndexCollection;
use Magenest\GoogleShopping\Model\ResourceModel\GoogleProduct as GoogleProductResourceModel;
use Magenest\GoogleShopping\Model\ResourceModel\Template as TemplateResource;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\ProductFactory;
use Magento\Directory\Model\Country;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\CronException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Tax\Model\ClassModel as Tax;
use Psr\Log\LoggerInterface;
use Magenest\GoogleShopping\Logger\Logger;
use Magento\Catalog\Model\Product\Visibility;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;
use Magento\Store\Model\App\Emulation;

class Cron
{
    const SIZE = 500;

    /** @var array  */
    protected $_googleFeed = [];

    /** @var array  */
    protected $_categoryMapped = [];

    /** @var null  */
    protected $_mappingFields = null;

    /** @var null  */
    protected $_selectAttributes = null;

    /** @var GoogleFeedCollectionFactory  */
    protected $_googleFeedCollection;

    /** @var GoogleFeedIndexCollection  */
    protected $_googleFeedIndexCollection;

    /** @var GoogleFeedIndexResource  */
    protected $_googleFeedIndexResource;

    /** @var GoogleFeedIndexFactory  */
    protected $_googleFeedIndexFactory;

    /** @var \Magenest\GoogleShopping\Model\GoogleFeedFactory  */
    protected $_googleFeedFactory;

    /** @var GoogleFeedResource  */
    protected $_googleFeedResource;

    /** @var \Magenest\GoogleShopping\Model\TemplateFactory  */
    protected $_templateFactory;

    /** @var TemplateResource  */
    protected $_templateResource;

    /** @var GoogleProductResourceModel  */
    protected $_googleProduct;

    /** @var \Magenest\GoogleShopping\Model\Client  */
    protected $_client;

    /** @var LoggerInterface  */
    protected $_logger;

    /** @var ProductFactory  */
    protected $_productFactory;

    /** @var Json  */
    protected $_jsonFramework;

    /** @var Country  */
    protected $_country;

    /** @var Tax  */
    protected $_tax;

    /** @var StoreManagerInterface  */
    protected $_storeManager;

    /** @var DateTime  */
    private $dateTime;

    /** @var TimezoneInterface  */
    private $localeDate;

    /** @var Resolver  */
    protected $_resolver;

    /** @var ScopeConfigInterface  */
    protected $_scopeConfig;

    /** @var Image  */
    protected $_imageProductHelper;

    /** @var ProductRepositoryInterface  */
    protected $_productRepository;

    /** @var Logger  */
    protected $_googleShoppingLogger;

    /** @var Configurable */
    protected $_configurableResources;

    /** @var Emulation*/
    protected $emulation;

    /**
     * @param GoogleFeedCollectionFactory $googleFeedCollection
     * @param GoogleFeedIndexCollection $googleFeedClientCollection
     * @param GoogleFeedIndexResource $googleFeedIndexResource
     * @param GoogleFeedIndexFactory $googleFeedIndexFactory
     * @param GoogleFeedFactory $googleFeedFactory
     * @param GoogleFeedResource $googleFeedResource
     * @param TemplateFactory $templateFactory
     * @param TemplateResource $templateResource
     * @param GoogleProductResourceModel $googleProduct
     * @param Client $client
     * @param LoggerInterface $logger
     * @param ProductFactory $productFactory
     * @param Json $jsonFramework
     * @param Country $country
     * @param Tax $tax
     * @param StoreManagerInterface $storeManager
     * @param DateTime $dateTime
     * @param TimezoneInterface $localeDate
     * @param Resolver $resolver
     * @param ScopeConfigInterface $scopeConfig
     * @param Image $imageProductHelper
     * @param ProductRepositoryInterface $productRepository
     * @param Logger $googleShoppingLogger
     * @param Configurable $configurable
     * @param Emulation $emulation
     */
    public function __construct(
        GoogleFeedCollectionFactory $googleFeedCollection,
        GoogleFeedIndexCollection $googleFeedClientCollection,
        GoogleFeedIndexResource $googleFeedIndexResource,
        GoogleFeedIndexFactory $googleFeedIndexFactory,
        GoogleFeedFactory $googleFeedFactory,
        GoogleFeedResource $googleFeedResource,
        TemplateFactory $templateFactory,
        TemplateResource $templateResource,
        GoogleProductResourceModel $googleProduct,
        Client  $client,
        LoggerInterface $logger,
        ProductFactory $productFactory,
        Json $jsonFramework,
        Country $country,
        Tax $tax,
        StoreManagerInterface $storeManager,
        DateTime $dateTime,
        TimezoneInterface $localeDate,
        Resolver $resolver,
        ScopeConfigInterface $scopeConfig,
        Image $imageProductHelper,
        ProductRepositoryInterface $productRepository,
        Logger $googleShoppingLogger,
        Configurable $configurable,
        Emulation $emulation
    ) {
        $this->_googleFeedCollection = $googleFeedCollection;
        $this->_googleFeedIndexCollection = $googleFeedClientCollection;
        $this->_googleFeedIndexResource = $googleFeedIndexResource;
        $this->_googleFeedIndexFactory = $googleFeedIndexFactory;
        $this->_googleFeedFactory = $googleFeedFactory;
        $this->_googleFeedResource = $googleFeedResource;
        $this->_templateFactory = $templateFactory;
        $this->_templateResource = $templateResource;
        $this->_googleProduct = $googleProduct;
        $this->_client = $client;
        $this->_logger = $logger;
        $this->_productFactory = $productFactory;
        $this->_jsonFramework = $jsonFramework;
        $this->_country = $country;
        $this->_tax = $tax;
        $this->_storeManager = $storeManager;
        $this->dateTime = $dateTime;
        $this->localeDate = $localeDate;
        $this->_resolver = $resolver;
        $this->_scopeConfig = $scopeConfig;
        $this->_imageProductHelper = $imageProductHelper;
        $this->_productRepository = $productRepository;
        $this->_googleShoppingLogger = $googleShoppingLogger;
        $this->_configurableResources = $configurable;
        $this->emulation = $emulation;
    }

    /**
     * @throws \Exception
     */
    public function execute()
    {
        try {
            $scheduleMode = $this->_client->getStoreConfig(Data::SCHEDULE_MODE);
            if ($scheduleMode == Data::GENERATE_SCHEDULE && $this->validateTime()) {
                $googleFeedCollections = $this->_googleFeedCollection->create()
                    ->addFieldToFilter(
                        \Magenest\GoogleShopping\Api\Data\FeedInterface::STATUS,
                        \Magenest\GoogleShopping\Model\GoogleFeed::STATUS_ENABLE
                    )->getItems();
                if (count($googleFeedCollections)) {
                    /** @var \Magenest\GoogleShopping\Model\GoogleFeed $feed */
                    foreach ($googleFeedCollections as $feed) {
                        $feedId = $feed->getId();
                        $storeId = $feed->getStoreId();
                        $accounts = $feed->getAccounts() ? $this->_jsonFramework->unserialize($feed->getAccounts()) : [];
                        $this->syncByFeedId($feedId, $storeId, $accounts);
                        $records = $this->_googleProduct->getOfferIdsByFeedId($feedId);
                        if (!count($records)) {
                            continue;
                        }
                        $this->productStatusBath($records, $feedId, $accounts);
                    }
                }
            }
        } catch (\Exception $exception) {
            $this->_logger->debug($exception->getMessage());
        }
    }

    /**
     * @return bool
     */
    private function validateTime()
    {
        $days = $times = [];
        $scheduleDays = $this->_client->getStoreConfig(Data::SCHEDULE_DAYS);
        if ($scheduleDays) {
            $days = explode(',', $scheduleDays);
        }
        $scheduleTimes = $this->_client->getStoreConfig(Data::SCHEDULE_TIMES);
        if ($scheduleTimes) {
            $times = explode(',', $scheduleTimes);
        }
        $currentDay = date('w');
        if (in_array($currentDay, $days)) {
            $mageTime = $this->localeDate->scopeTimeStamp();
            $now = (date("H", $mageTime) * 60) + date("i", $mageTime);
            $modNow = intdiv($now, 30);
            if (in_array($modNow*30, $times)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $response
     * @return array|bool|float|int|mixed|string|null
     * @throws \Exception
     */
    private function responseDecode($response)
    {
        try {
            $responseDecode = $this->_jsonFramework->unserialize($response);
            if (isset($responseDecode["error"])) {
                $error = $responseDecode["error"];
                if (isset($error['code']) && $error['code'] == '401') {
                    throw new \Exception(__("Your credentials is invalid. Please recheck and get access token again!"));
                } else {
                    throw new \Exception($responseDecode["error"]['message']);
                }
            } elseif (isset($responseDecode["entries"])) {
                $entries = $responseDecode["entries"];
                foreach ($entries as $entry) {
                    if (isset($entry['errors'])) {
                        throw new \Exception($entry['errors']['message']);
                    }
                }
            }
            return $responseDecode;
        } catch (\Exception $exception) {
            throw new \Exception(__($exception->getMessage()));
        }
    }

    /**
     * @param GoogleFeedIndex $googleFeedIndex
     * @param $storeId
     * @param $accounts
     * @return array
     * @throws CronException
     */
    private function sync(\Magenest\GoogleShopping\Model\GoogleFeedIndex $googleFeedIndex, $storeId, $accounts)
    {
        if ($googleFeedIndex->getId()) {
            try {
                $results = [];
                $templateId = $googleFeedIndex->getTemplateId();
                $this->_googleShoppingLogger->critical("Products: " . $googleFeedIndex->getProductIds());
                $productIds = $this->_jsonFramework->unserialize($googleFeedIndex->getProductIds());
                $productIdChunks = [];
                if (is_array($productIds) && !empty($productIds)) {
                    $productIdChunks = array_chunk($productIds, self::SIZE);
                }
                $feedId = $googleFeedIndex->getFeedId();

                foreach ($productIdChunks as $productIdChunk) {
                    $dataRaw = $this->getMapping($templateId, $productIdChunk, $feedId, $storeId);

                    if (count($dataRaw)) {
                        $responses = $this->_client->productCustomBath($dataRaw, $accounts);
                        foreach ($responses as $key => $response) {
                            $results[$key] = $this->responseDecode($response);
                        }
                    }
                }
                return $results;
            } catch (\Exception $exception) {
                $this->_googleShoppingLogger->critical($exception->getMessage() . $exception->getLine() );
                throw new CronException(__($exception->getMessage()));
            }
        } else {
            throw new CronException(__("The Feed does not exit."));
        }
    }

    /**
     * @param $feedId
     * @param $storeId
     * @param $accounts
     * @throws \Exception
     */
    public function syncByFeedId($feedId, $storeId, $accounts)
    {
        $googleFeedIndex = $this->_googleFeedIndexFactory->create();
        $this->_googleFeedIndexResource->load($googleFeedIndex, $feedId, 'feed_id');
        $responses = $this->sync($googleFeedIndex, $storeId, $accounts);
        $this->_googleProduct->saveGoogleProductStatus($responses, $feedId);
    }

    /**
     * @param $id
     * @param $productAttributes
     * @param array $map
     * @param $feedId
     * @param $storeId
     * @return array
     * @throws NoSuchEntityException
     */
    public function getProductCollection($id, $productAttributes, $feedId, $storeId, $map = [])
    {
        try {
            $product = $this->_productRepository->getById($id);
        }catch (\Exception $exception) {
            $this->_googleShoppingLogger->critical("ProductId: " .$id . " " .$exception->getMessage());
            return [];
        }

        $attributes = $product->getAttributes();
        $data = $result = [];
        $productAttributes[] = 'quantity_and_stock_status';
        $productAttributes[] = 'country_of_manufacture';
        $productAttributes[] = 'tax_class_id';
        foreach ($productAttributes as $item) {
            if (isset($attributes[$item])) {
                $attribute = $attributes[$item];
                $sub = substr($item, 0, 5);
                if ($sub == 'stock') {
                    $stockItem  = $product->getExtensionAttributes()->getStockItem();
                    $data[$item] = $stockItem->getData(substr($item, 6));
                } elseif ($item == 'quantity_and_stock_status') {
                    $qtyAndStockStatus = $product->getData($item);
                    if (isset($qtyAndStockStatus['is_in_stock']) && $qtyAndStockStatus['is_in_stock']) {
                        $data[$item]        =  'in_stock';
                    }else{
                        $data[$item]        =  'out_of_stock';
                    }
                } else {
                    $data[$item] = $attribute->getFrontend()->getValue($product);
                }
                if (!empty($data['country_of_manufacture'])) {
                    $country_id                     = $data['country_of_manufacture'];
                    $data['country_of_manufacture'] = $this->getCountryName($country_id);
                }

                if (!empty($data['tax_class_id'])) {
                    $tax_id = $data['tax_class_id'];
                    if ($tax_id == null) {
                        $data['tax_class_id'] = "None";
                    } elseif (is_object($tax_id)) {
                        $data['tax_class_id'] = $tax_id->getText();
                    } else {
                        $data['tax_class_id'] = $tax_id;
                    }
                }
            }
        }
        $priceModel = [
            'price',
            'salePrice',
            'costOfGoodsSold'
        ];

        $arrayString = [
            'additionalImageLinks',
            'promotionIds',
            'sizes',
            'taxes',
            'displayAdsSimilarIds',
            'includedDestinations',
            'excludedDestinations',
            'adsLabels',
            'productTypes',
            'shoppingAdsExcludedCountries',
            'productHighlights'
        ];
        $ignoreArr = [
            'shipping',
            'taxes',
            'customAttributes',
            'installment',
            'loyaltyPoints',
            'unitPricingMeasure',
            'unitPricingBaseMeasure',
            'shippingLength',
            'shippingWidth',
            'shippingHeight',
            'productDetails',
            'subscriptionCost'
        ];

        foreach ($map as $key => $value) {
            if (isset($data[$value]) && $data[$value]) {
                if (in_array($key, $priceModel)) {
                    $result[$key] = $this->priceModel($data[$value]);
                    continue;
                }
                if ($key == 'googleProductCategory') {
                    $result[$key] = trim($this->mappedCategory($data[$value]));
                    continue;
                }
                if (in_array($key, $arrayString)) {
                    if (!is_array($data[$value])) {
                        $valueRaw =  $data[$value];
                        $data[$value] = [$valueRaw];
                    }
                }
                if ($key == 'shippingWeight') {
                    $result[$key] = $this->productShippingWeightModel($data[$value]);
                    continue;
                }
                if (in_array($key, $ignoreArr)) {
                    continue;
                }
                $result[$key] = $data[$value];
            }
        }
        $mediaUrl = rtrim($this->getMediaUrl(), "/")."/catalog/product";
        $params = [
            "title" => $product->getName(),
            "link" => $this->getGoogleAnalytics($feedId, $this->getProductUrl($product, $storeId)),
            "description" => strip_tags($product->getDescription()),
            "imageLink" => $mediaUrl.$product->getData('image'),
            "price" => [
                "value" => "" . $product->getPrice() . "",
                "currency" => $this->getCurrency()
            ],
            "contentLanguage" => $this->_scopeConfig->getValue(Data::CONTENT_LANGUAGE),
            "offerId" => $product->getSku(),
            "targetCountry" => $this->_scopeConfig->getValue(Data::TARGET_COUNTRY),
            "channel" => "online"
        ];

        return array_merge($result, $params);
    }

    /**
     * @param ProductInterface $product
     * @param $storeId
     * @return string
     * @throws NoSuchEntityException
     */
    protected function getProductUrl($product, $storeId)
    {
        $this->emulation->startEnvironmentEmulation($storeId, 'frontend');
        $baseUrl = $this->_storeManager->getStore($storeId)->getBaseUrl();
        $this->emulation->stopEnvironmentEmulation();
        $productURLKey = $product->getUrlKey();
        if(
            $product->getVisibility() == Visibility::VISIBILITY_NOT_VISIBLE &&
            $product->getTypeId() == 'simple'
        ) {
            $parentProductId = $this->_configurableResources->getParentIdsByChild($product->getId());
            if (!empty($parentProductId)) {
                $parentProductId = reset($parentProductId);
                $parentProduct = $this->_productRepository->getById($parentProductId);
                $productURLKey = $parentProduct->getUrlKey();
            }
        }
        return $baseUrl.$productURLKey.'.html';
    }
    /**
     * @param $value
     * @return array
     * @throws NoSuchEntityException
     */
    private function priceModel($value)
    {
        return [
            "value" => $value,
            "currency" => $this->getCurrency()
        ];
    }

    /**
     * @param $value
     * @return array
     */
    private function productShippingWeightModel($value)
    {
        return [
            "value" => $value,
            "unit" => "1"
        ];
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    private function getCurrency()
    {
        return $this->_storeManager->getStore()->getCurrentCurrencyCode();
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    private function getMediaUrl()
    {
        return $this ->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
    }

    /**
     * @param $templateId
     * @param $productIds
     * @param $feedId
     * @param $storeId
     * @return array
     * @throws NoSuchEntityException
     */
    private function getMapping($templateId, $productIds, $feedId, $storeId)
    {
        $results = [];
        $map = $this->getFieldMapping($templateId);
        foreach ($productIds as $productId) {
            $result = $this->getProductCollection($productId, $this->_selectAttributes, $feedId, $storeId, $map);
            if(!empty($result)) {
                $results[] = $result;
            }
        }
        $this->_googleShoppingLogger->critical("Product mapping logger: ");
        $this->_googleShoppingLogger->critical(json_encode($results));
        return $results;
    }

    /**
     * @param $templateId
     * @return array|null
     * @throws \Exception
     */
    private function getFieldMapping($templateId)
    {
        if ($this->_mappingFields == null) {
            $map = $selectAttributes = [];
            $fieldsMapped = $this->_templateResource->getAllFieldMap($templateId);
            if (count($fieldsMapped)) {
                foreach ($fieldsMapped as $field) {
                    if (!$field['status']) {
                        continue;
                    }
                    $googleAttribute = $field['google_attribute'];
                    $magentoAttribute = $field['magento_attribute'];
                    $selectAttributes[] = $magentoAttribute;
                    $map[$googleAttribute] = $magentoAttribute;
                }
            }
            $this->_mappingFields = $map;
            $this->_selectAttributes = $selectAttributes;
        }
        return $this->_mappingFields;
    }

    /**
     * @param $id
     * @return string
     */
    public function getCountryName($id)
    {
        try {
            $model = $this->_country->loadByCode($id);
            return $model->getName();
        } catch (\Exception $exception) {
            $this->_logger->debug($exception->getMessage());
            return '';
        }
    }

    /**
     * @param $productIds
     * @param $feedId
     * @param $accounts
     * @throws \Google_Exception
     * @throws \Zend_Http_Client_Exception
     */
    public function productStatusBath($productIds, $feedId, $accounts)
    {
        $responses = $this->_client->productStatusBath($productIds, $accounts);
        $this->_googleProduct->saveStatus($responses, $feedId);
    }

    /**
     * @param $googleFeedId
     * @return GoogleFeed|mixed
     */
    private function getGoogleFeedModel($googleFeedId)
    {
        if (!isset($this->_googleFeed[$googleFeedId])) {
            $googleFeedModel = $this->_googleFeedFactory->create();
            $this->_googleFeedResource->load($googleFeedModel, $googleFeedId);
            $this->_googleFeed[$googleFeedId] = $googleFeedModel;
        }
        return $this->_googleFeed[$googleFeedId];
    }

    /**
     * @param $googleFeedId
     * @param $productUrl
     * @return string
     */
    private function getGoogleAnalytics($googleFeedId, $productUrl)
    {
        $googleFeedModel = $this->getGoogleFeedModel($googleFeedId);
        $analytics = '';
        if (!$googleFeedModel->getId()) {
            return $productUrl;
        }
        $gaMedium = $googleFeedModel->getData('ga_medium');
        $gaCampaign = $googleFeedModel->getData('ga_name');
        $gaContent = $googleFeedModel->getData('ga_content');
        $gaTerm = $googleFeedModel->getData('ga_term');
        $gaSource = $googleFeedModel->getData('ga_source');
        if ($gaMedium || $gaCampaign || $gaContent || $gaTerm || $gaSource) {
            $analytics = '?utm_medium=' . $gaMedium . '&utm_campaign=' . $gaCampaign . '&utm_source=' . $gaSource;
            if ($gaContent) {
                $analytics .= '&utm_content=' . $gaContent;
            }
            if ($gaTerm) {
                $analytics .= '&utm_term=' . $gaTerm . '&uq=';
            }
        }
        return $productUrl . $analytics;
    }

    public function mappedCategory($categoryIds)
    {
        $map = null;
        $categoryMapped = $this->getMappingCategory();
        if (!is_array($categoryMapped) || empty($categoryMapped)) {
            return null;
        }
        foreach ($categoryIds as $categoryId) {
            if (!isset($categoryMapped[$categoryId])) {
                continue;
            }
            $map = $categoryMapped[$categoryId];
        }
        return $this->cutStringCategory($map);
    }

    /**
     * @param $string
     * @return false|string
     */
    private function cutStringCategory($string)
    {
        $arr = explode('-', $string);
        if (is_array($arr) && count($arr) >=2) {
            $reverseArr = array_reverse($arr);
            return reset($reverseArr);
        }
        return $string;
    }

    /**
     * @return array
     * @throws CronException
     */
    private function getMappingCategory()
    {
        if (empty($this->_categoryMapped)) {
            try {
                $templateModel = $this->_templateFactory->create();
                $this->_templateResource->load($templateModel, Template::CATEGORY_TEMPLATE, 'type');
                if ($templateModel->getId()) {
                    $contentTemplate = $this->_jsonFramework->unserialize($templateModel->getContentTemplate());
                    if (is_array($contentTemplate) && !empty($contentTemplate)) {
                        $this->_categoryMapped = $contentTemplate;
                    }
                }
            } catch (\Exception $exception) {
                throw new CronException(__($exception->getMessage()));
            }
        }
        return $this->_categoryMapped;
    }
}
