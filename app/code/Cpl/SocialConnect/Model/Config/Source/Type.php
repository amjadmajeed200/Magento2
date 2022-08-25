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

namespace Cpl\SocialConnect\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Type
 * @package Cpl\SocialConnect\Model\Config\Source
 */
class Type implements SourceInterface, OptionSourceInterface
{

    /**
     * Get available types
     *
     * @return array
     */    
    public function getAvailableTypes()
    {
        return [
            'fb' => 'Facebook',
            'amazon' => 'Amazon',
            'google' => 'Google'
        ];
    }

    /**
     * Retrieve All options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $result = [];
        foreach ($this->getAvailableTypes() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }
        return $result;
    }

    /**
     * Retrieve Option value text
     *
     * @param string $value
     * @return mixed
     */
    public function getOptionText($value)
    {
        $options = $this->getAvailableTypes();
        return isset($options[$value]) ? $options[$value] : null;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}
