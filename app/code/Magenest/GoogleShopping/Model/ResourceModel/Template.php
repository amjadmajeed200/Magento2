<?php
namespace Magenest\GoogleShopping\Model\ResourceModel;

use Magento\Framework\DB\Adapter\DuplicateException;

/**
 * Class Template
 * @package Magenest\GoogleShopping\Model\ResourceModel
 */
class Template extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected $_feedMapTable = null;

    /** @var \Magento\Framework\DB\Adapter\AdapterInterface */
    protected $_connection;

    public function _construct()
    {
        $this->_init('magenest_google_feed_template', 'id');
        $this->_connection = $this->getConnection();
    }

    /**
     * @return string
     */
    private function getFeedMapTable()
    {
        if ($this->_feedMapTable === null) {
            $this->_feedMapTable = $this->getTable('magenest_google_feed_map');
        }
        return $this->_feedMapTable;
    }
    /**
     * @param $type
     * @return array
     */
    public function getFieldsByEntity($type)
    {
        $select = $this->_connection->select()->from($this->getTable('eav_attribute'))
            ->where('entity_type_id = ?', $type);
        $results = $this->_connection->fetchAll($select);
        return $results;
    }
    /**
     * @param $type
     * @return array
     */
    public function getEavIdByType($type)
    {
        $select = $this->_connection->select()->from($this->getTable('eav_entity_type'))
            ->where('entity_type_code = ?', $type);
        $result = $this->_connection->fetchRow($select);
        return $result;
    }

    /**
     * @param $array
     * @throws DuplicateException
     */
    public function saveTemplateContent($array)
    {
        try {
            $mappTable = $this->getFeedMapTable();
            if (is_array($array) && !empty($array)) {
                $this->_connection->insertOnDuplicate(
                    $mappTable,
                    $array,
                    ['magento_attribute', 'google_attribute', 'status', 'template_id']
                );
            }
        } catch (DuplicateException $duplicateException) {
            throw new DuplicateException(__("DUPLICATE: magento attribute fields is duplicated. We auto replace them. "));
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * @param $id
     * @param $array
     * @throws \Exception
     */
    public function deleteTemplateContent($id, $array)
    {
        try {
            $mappedTable = $this->getFeedMapTable();
            $this->_connection->delete(
                $mappedTable,
                [
                    'magento_attribute IN (?)' => $array,
                    'template_id' => $id
                ]
            );
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * @param $id
     * @return array
     * @throws \Exception
     */
    public function getAllFieldMap($id)
    {
        try {
            $mappedTable = $this->getFeedMapTable();
            $select = $this->_connection->select()->from($mappedTable)->where('template_id = :template_id');
            return $this->_connection->fetchAll($select, [':template_id' => $id]);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * @param $id
     * @return array
     * @throws \Exception
     */
    public function getAllMagentoMappedFields($id)
    {
        try {
            $mappedTable = $this->getFeedMapTable();
            $select = $this->_connection->select()->from($mappedTable, ['magento_attribute'])->where('template_id = :template_id');
            return $this->_connection->fetchCol($select, [':template_id' => $id]);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }
    public function getNameById($id)
    {
        try {
            $select = $this->_connection->select()->from($this->getMainTable(), 'name')
                ->where('id = :id');
            return $this->_connection->fetchOne($select, [':id' => $id]);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return Template
     * @throws \Exception
     */
    public function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        try {
            if ($objectId = $object->getData('id')) {
                $feedTable = $this->getTable('magenest_google_feed');
                $select = $this->_connection->select()->from($feedTable)->where('template_id = :template_id');
                $result = $this->_connection->fetchOne($select, [':template_id' => $objectId]);
                if ($result != null) {
                    throw new \Exception(__("The '%1' is assigned to a Feed", $object->getData('name')));
                }
                return parent::_beforeDelete($object); // TODO: Change the autogenerated stub
            } else {
                throw new \Exception(__('Template does not exit.'));
            }
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }
}
