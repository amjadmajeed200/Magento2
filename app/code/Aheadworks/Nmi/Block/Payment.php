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
namespace Aheadworks\Nmi\Block;

use Aheadworks\Nmi\Model\Ui\ConfigProvider;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\Element\Template\Context;

/**
 * Represents the payment block for the admin checkout form
 */
class Payment extends Template
{
    /**
     * @var ConfigProviderInterface
     */
    private $config;

    /**
     * @var Json
     */
    private $json;

    /**
     * @param Context $context
     * @param ConfigProviderInterface $config
     * @param Json $json
     * @param array $data
     */
    public function __construct(
        Context $context,
        ConfigProviderInterface $config,
        Json $json,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
        $this->json = $json;
    }

    /**
     * Retrieves the config that should be used by the block
     *
     * @return string
     */
    public function getPaymentConfig()
    {
        $payment = $this->config->getConfig()['payment'];
        $config = $payment[$this->getMethodCode()];
        $config['code'] = $this->getMethodCode();

        return $this->json->serialize($config);
    }

    /**
     * Returns the method code for this payment method
     *
     * @return string
     */
    public function getMethodCode()
    {
        return ConfigProvider::CODE;
    }
}
