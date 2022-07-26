<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://aheadworks.com/end-user-license-agreement/
 *
 * @package    Nmi
 * @version    1.3.1
 * @copyright  Copyright (c) 2022 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Nmi\Gateway\Config;

/**
 * Class Config
 * @package Aheadworks\Nmi\Gateway\Config
 */
class Config extends \Magento\Payment\Gateway\Config\Config
{
    /**#@+
     * Constants for config path
     */
    const KEY_CC_TYPES_AW_NMI_MAPPER = 'cctypes_aw_nmi_mapper';
    const KEY_USE_CVV = 'useccv';
    const SECURITY_KEY = 'security_key';
    /**#@-*/

    /**
     * Retrieve mapper between Magento and Nmi card types
     *
     * @return array
     */
    public function getCcTypesMapper()
    {
        $result = json_decode(
            (string)$this->getValue(self::KEY_CC_TYPES_AW_NMI_MAPPER),
            true
        );

        return is_array($result) ? $result : [];
    }

    /**
     * Checks if cvv field is enabled.
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isCvvEnabled($storeId = null)
    {
        return (bool) $this->getValue(self::KEY_USE_CVV, $storeId);
    }
}
