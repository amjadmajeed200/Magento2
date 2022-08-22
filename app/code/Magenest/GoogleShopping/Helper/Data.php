<?php
namespace Magenest\GoogleShopping\Helper;

use Magenest\GoogleShopping\Model\ResourceModel\Template as TemplateResourceModel;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

/**
 * Class Data
 * @package Magenest\GoogleShopping\Helper
 */
class Data extends AbstractHelper
{
    const GENERATE_MANUALLY = 0;
    const GENERATE_SCHEDULE = 1;
    const ENTITY_TYPE = 'catalog_product';
    const MERCHANT_ID = 'google_shopping/google/merchant_id';
    const GOOGLE_TOKEN = 'google_shopping/google/access_token';
    const GOOGLE_TOKEN_EXPIRESIN = 'google_shopping/google/expires_in';
    const GOOGLE_REFRESH_TOKEN = 'google_shopping/google/refresh_token';
    const GOOGLE_MERCHANT_ID = 'google_shopping/google/merchant_id';
    const CONTENT_LANGUAGE = 'google_shopping/google/content_language';
    const TARGET_COUNTRY = 'google_shopping/google/target_country';
    const SCHEDULE_MODE = 'google_shopping/schedule_mode/mode';
    const SCHEDULE_DAYS = 'google_shopping/schedule_mode/days';
    const SCHEDULE_TIMES = 'google_shopping/schedule_mode/times';
    const GOOGLESHOPPING_CONFIGURATION_SECTION = 'adminhtml/system_config/edit/section/google_shopping';
    const GOOGLESHOPPING_PRODUCT_STATUS = 'googleshopping_product_status';

    /** @var TemplateResourceModel  */
    protected $_templateResource;

    /**
     * Data constructor.
     * @param TemplateResourceModel $templateResourceModel
     * @param Context $context
     */
    public function __construct(
        TemplateResourceModel $templateResourceModel,
        Context $context
    ) {
        $this->_templateResource = $templateResourceModel;
        parent::__construct($context);
    }

    public function getProductAttribute()
    {
        $catalogProduct = $this->_templateResource->getEavIdByType(self::ENTITY_TYPE);
        $fields = $this->_templateResource->getFieldsByEntity($catalogProduct['entity_type_id']);
        $attributesCode  = array_column($fields, 'attribute_code');
        $attributesLabel = array_column($fields, 'frontend_label');
        return $this->fixFields(array_combine($attributesCode, $attributesLabel));
    }
    /**
     * @param array $fields
     *
     * @return array fixed fields
     */
    private function fixFields($fields)
    {
        $arrayResult = [];
        $fixedFields = $fields;
        foreach ($fields as $attributeCode => $attributeLabel) {
            if (!$attributeCode || !$attributeLabel) {
                unset($fixedFields[$attributeCode]);
            } else {
                $arrayResult[] = [
                    'code' => $attributeCode,
                    'label' => $attributeLabel
                ];
            }
        }
        return $arrayResult;
    }

    /**
     * @param $templateId
     * @return array
     */
    public function getFieldsMappedByTemplateId($templateId)
    {
        try {
            return $this->_templateResource->getAllFieldMap($templateId);
        } catch (\Exception $exception) {
            $this->_logger->debug($exception->getMessage());
            return [];
        }
    }
}
