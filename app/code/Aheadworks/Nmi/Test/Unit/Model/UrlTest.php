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
namespace Aheadworks\Nmi\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Nmi\Model\Config;
use Aheadworks\Nmi\Model\Url;

/**
 * Test for \Aheadworks\Nmi\Model\Url
 */
class UrlTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Url
     */
    private $model;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp() : void
    {
        $objectManager = new ObjectManager($this);

        $this->configMock = $this->createMock(Config::class);

        $this->model = $objectManager->getObject(
            Url::class,
            [
                'config' => $this->configMock
            ]
        );
    }

    /**
     * Test getApiBaseUrl method
     */
    public function testGetApiBaseUrl()
    {
        $apiEndPointUrl = 'https://test.test.test';

        $this->configMock->expects($this->once())
            ->method('getApiEndpointUrl')
            ->with(1)
            ->willReturn($apiEndPointUrl);

        $result = $apiEndPointUrl . Url::API_RELATIVE_PATH;

        $this->assertEquals($result, $this->model->getApiBaseUrl(1));
    }

    /**
     * Test getCollectJsBaseUrl method
     */
    public function testGetCollectJsBaseUrl()
    {
        $apiEndPointUrl = 'https://test.test.test';

        $this->configMock->expects($this->once())
            ->method('getApiEndpointUrl')
            ->with(1)
            ->willReturn($apiEndPointUrl);

        $result = $apiEndPointUrl . Url::COLLECT_JS_RELATIVE_PATH;

        $this->assertEquals($result, $this->model->getCollectJsBaseUrl(1));
    }
}
