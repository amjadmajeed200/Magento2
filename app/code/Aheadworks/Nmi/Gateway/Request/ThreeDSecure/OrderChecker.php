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
namespace Aheadworks\Nmi\Gateway\Request\ThreeDSecure;

/**
 * Class OrderChecker
 */
class OrderChecker
{
    /**
     * @var string
     */
    private $firstOrderIncrementId;

    /**
     * set first order increment id
     *
     * @param string $orderIncrementId
     */
    public function setFirstOrderIncrementId($orderIncrementId)
    {
        if ($orderIncrementId) {
            $this->firstOrderIncrementId = $orderIncrementId;
        }
    }

    /**
     * 3DSecure only works for one order,
     * so you need to check if a request has already been made
     *
     * @param string $orderIncrementId
     * @return bool
     */
    public function isFirstOrder($orderIncrementId)
    {
        return !(bool)$this->firstOrderIncrementId || $this->firstOrderIncrementId === $orderIncrementId;
    }
}
