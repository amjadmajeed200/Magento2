<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_StoreLocator
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\StoreLocator\Test\Unit\Plugin\Model;

use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Mageplaza\StoreLocator\Helper\Data;
use Mageplaza\StoreLocator\Plugin\Model\ConfigData;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class ConfigDataTest
 * @package Mageplaza\StoreLocator\Test\Unit\Plugin\Model
 */
class ConfigDataTest extends TestCase
{
    /**
     * @var ConfigData
     */
    private $object;

    /**
     * @var Data|MockObject
     */
    private $helperDataMock;

    /**
     * @var SessionManagerInterface|MockObject
     */
    private $coreSessionMock;

    /**
     * @var AbstractCarrier|MockObject
     */
    private $subjectMock;

    public function testBeforeGetConfigData()
    {
        $this->checkMethodExits();
        $this->subjectMock->expects($this->once())->method('getCarrierCode')->willReturn('mpstorepickup');
        $this->helperDataMock->expects($this->once())->method('checkEnabledModule')->willReturn(false);
        $this->coreSessionMock->expects($this->once())->method('start')->willReturnSelf();
        $this->coreSessionMock->expects($this->once())->method('setEnabled')->with(0)->willReturnSelf();

        $this->object->beforeGetConfigData($this->subjectMock, '');
    }

    private function checkMethodExits()
    {
        $subjectMethods = get_class_methods(AbstractCarrier::class);
        if (!in_array('getConfigData', $subjectMethods, true)) {
            $this->fail('Method does not exits');
        }
    }

    protected function setUp()
    {
        $this->helperDataMock  = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()->getMock();
        $sessionMethods        = array_unique(
            array_merge(
                get_class_methods(SessionManagerInterface::class),
                ['setEnabled']
            )
        );
        $this->coreSessionMock = $this->getMockBuilder(SessionManagerInterface::class)
            ->setMethods($sessionMethods)
            ->disableOriginalConstructor()->getMockForAbstractClass();
        $this->subjectMock     = $this->getMockBuilder(AbstractCarrier::class)
            ->disableOriginalConstructor()->getMock();

        $objectManagerHelper = new ObjectManager($this);
        $this->object        = $objectManagerHelper->getObject(
            ConfigData::class,
            [
                '_helperData'  => $this->helperDataMock,
                '_coreSession' => $this->coreSessionMock
            ]
        );
    }
}
