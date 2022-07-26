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

/**
 * Class TransactionTypeDataBuilder
 * @package Aheadworks\Nmi\Gateway\Request
 */
class TransactionTypeDataBuilder implements BuilderInterface
{
    /**
     * Type
     */
    const TYPE = 'type';

    /**
     * @var string
     */
    private $transactionType;

    /**
     * @param string $transactionType
     */
    public function __construct($transactionType)
    {
        $this->transactionType = $transactionType;
    }

    /**
     * {@inheritdoc}
     */
    public function build(array $buildSubject)
    {
        return [
            self::TYPE => $this->transactionType
        ];
    }
}
