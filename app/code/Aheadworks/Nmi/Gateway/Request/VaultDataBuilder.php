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
namespace Aheadworks\Nmi\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Aheadworks\Nmi\Model\Config;

/**
 * Class VaultDataBuilder
 * @package Aheadworks\Nmi\Gateway\Request
 */
class VaultDataBuilder implements BuilderInterface
{
    /**
     * Add/Update a secure customer vault record
     */
    const CUSTOMER_VAULT = 'customer_vault';

    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        $vaultData =  [
            self::CUSTOMER_VAULT => 'add_customer'
        ];

        return $this->config->isAwNmiCcVaultActive() ? $vaultData : [];
    }
}
