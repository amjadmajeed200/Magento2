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
namespace Aheadworks\Nmi\Model\Multishipping\Payment\Method\Specification;

use Magento\Payment\Model\Method\SpecificationInterface;
use Aheadworks\Nmi\Model\Ui\ConfigProvider as NmiConfigProvider;
use Aheadworks\Nmi\Model\Config;

/**
 * Class NmiEnabled
 * @package Aheadworks\Nmi\Model\Multishipping\Payment\Method\Specification
 */
class NmiEnabled implements SpecificationInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * NmiEnabled constructor.
     * @param Config $config
     */
    public function __construct (
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Disallow NMI on multishipping if vault disabled because multishipping cannot be processed without vault
     *
     * @inheridoc
     */
    public function isSatisfiedBy($paymentMethod)
    {
        return !(($paymentMethod == NmiConfigProvider::CODE || $paymentMethod == NmiConfigProvider::CC_VAULT_CODE)
            && !$this->config->isAwNmiCcVaultActive());
    }
}
