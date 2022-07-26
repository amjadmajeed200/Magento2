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
namespace Aheadworks\Nmi\Gateway\Http\Client;

/**
 * Class TransactionSale
 * @package Aheadworks\Nmi\Model\Api
 */
class TransactionSale extends AbstractTransaction
{
    /**
     * {@inheritdoc}
     */
    protected function process(array $data, $storeId = null)
    {
        return $this->requestManager->pay($data);
    }
}
