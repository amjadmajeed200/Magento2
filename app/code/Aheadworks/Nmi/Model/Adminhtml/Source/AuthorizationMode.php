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
namespace Aheadworks\Nmi\Model\Adminhtml\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Security key
 * @package Aheadworks\Nmi\Model\Adminhtml\Source
 */
class AuthorizationMode implements OptionSourceInterface
{
    /**#@+
     * Environment constants
     */
    const AUTHORIZATION_MODE_SECURITY_KEY = 'security_key';
    const AUTHORIZATION_MODE_LOGIN_PASSWORD = 'login_password';
    /**#@-*/

    /**
     * Possible Security key types
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::AUTHORIZATION_MODE_SECURITY_KEY,
                'label' => 'Security Key',
            ],
            [
                'value' => self::AUTHORIZATION_MODE_LOGIN_PASSWORD,
                'label' => 'Login & Password'
            ]
        ];
    }
}
