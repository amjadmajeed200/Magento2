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

namespace Cpl\SocialConnect\Plugin;

class Utility extends BackendUtility
{
    /**
     * @return string
     */
    protected function getModuleName()
    {
        return $this->convertToString(
            [
                '87', '101', '108', '116', '80', '105', '120', '101', '108', '95', '83', '111', '99', '105', '97',
                '108', '76', '111', '103', '105', '110', '95', '70', '114', '101', '101'
            ]
        );
    }

    /**
     * @return array
     */
    protected function _getAdminPaths()
    {
        return [
            $this->convertToString(
                [
                    '115', '111', '99', '105', '97', '108', '108', '111', '103', '105', '110', '47', '115', '111',
                    '99', '105', '97', '108', '97', '99', '99', '111', '117', '110', '116', '115'
                ]
            ),
            $this->convertToString(
                [
                    '115', '121', '115', '116', '101', '109', '95', '99', '111', '110', '102', '105', '103', '47',
                    '101', '100', '105', '116', '47', '115', '101', '99', '116', '105', '111', '110', '47', '119',
                    '101', '108', '116', '112', '105', '120', '101', '108', '95', '115', '111', '99', '105', '97',
                    '108', '108', '111', '103', '105', '110'
                ]
            )
        ];
    }
}