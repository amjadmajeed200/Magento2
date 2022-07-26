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
namespace Aheadworks\Nmi\Gateway\Validator;

use Aheadworks\Nmi\Model\Api\Result\Response;

/**
 * Class VoidResponseCodeValidator
 * @package Aheadworks\Nmi\Gateway\Validator
 */
class VoidResponseCodeValidator extends ResponseCodeValidator
{
    const RESPONSE_CODE_TRANSACTION_REJECTED_BY_GATEWAY = 300;

    /**
     * {@inheritdoc}
     */
    public function validate(array $validationSubject)
    {
        $result = parent::validate($validationSubject);

        if (!$result->isValid()) {
            /** @var Response $response */
            $response = $this->subjectReader->readResponseObject($validationSubject);
            if ($this->isAuthTokenExpired($response)) {
                $result = $this->createResult(true);
            }
        }

        return $result;
    }

    /**
     * Checks if auth token expired
     *
     * @param Response $response
     * @return bool
     */
    private function isAuthTokenExpired($response)
    {
        $responseCode = $response->getResponseCode();
        return $responseCode == self::RESPONSE_CODE_TRANSACTION_REJECTED_BY_GATEWAY;
    }
}
