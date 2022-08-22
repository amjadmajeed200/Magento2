<?php
namespace Magenest\GoogleShopping\Api\Data;

/**
 * Interface FeedInterface
 * @package Magenest\GoogleShopping\Api\Data
 */
interface FeedInterface
{
    const ID = 'id';
    const NAME = 'name';
    const STATUS = 'status';
    const STORE_ID = 'store_id';
    const CONTENT_TEMPLATE = 'content_template';
    const TEMPLATE_ID = 'template_id';
    const CONDITIONS_SERIALIZED = 'conditions_serialized';
    const MAPPING_CATEGORY = 'mapping_category';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * @return int|null
     */
    public function getId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return mixed
     */
    public function getStoreId();

    /**
     * @return mixed
     */
    public function getContentTemplate();

    /**
     * @return mixed
     */
    public function getTemplateId();

    /**
     * @return mixed
     */
    public function getConditionsSerialized();

    /**
     * @return mixed
     */
    public function getMappingCategory();

    /**
     * @return mixed
     */
    public function getCreatedAt();

    /**
     * @return mixed
     */
    public function getUpdatedAt();

    /**
     * @param int $id
     * @return FeedInterface
     */
    public function setId($id);

    /**
     * @param string $name
     * @return FeedInterface
     */
    public function setName($name);

    /**
     * @param string $storeId
     * @return FeedInterface
     *
     */
    public function setStoreId($storeId);

    /**
     * @param string $contentTemplate
     * @return FeedInterface
     */
    public function setContentTemplate($contentTemplate);

    /**
     * @param string $templateId
     * @return FeedInterface
     */
    public function setTemplateId($templateId);

    /**
     * @param string $conditionsSerialized
     * @return FeedInterface
     */
    public function setConditionsSerialized($conditionsSerialized);

    /**
     * @param string $mappingCategory
     * @return FeedInterface
     */
    public function setMappingCategory($mappingCategory);

    /**
     * @param string $createdAt
     * @return FeedInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * @param string $updatedAt
     * @return FeedInterface
     */
    public function setUpdatedAt($updatedAt);
}
