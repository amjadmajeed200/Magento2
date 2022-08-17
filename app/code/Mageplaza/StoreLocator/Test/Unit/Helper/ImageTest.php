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

namespace Mageplaza\StoreLocator\Test\Unit\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Mageplaza\StoreLocator\Helper\Image;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class ImageTest
 * @package Mageplaza\StoreLocator\Test\Unit\Helper
 */
class ImageTest extends TestCase
{
    /**
     * @var Image
     */
    private $object;

    /**
     * @var WriteInterface|MockObject
     */
    private $mediaDirectoryMock;

    public function testGetNotDuplicatedFilename()
    {
        $this->mediaDirectoryMock->expects($this->exactly(2))->method('getAbsolutePath')
            ->withConsecutive(
                ['mageplaza/store_locator/i/m/image.png'],
                ['mageplaza/store_locator/i/m/image1.png']
            )->willReturnOnConsecutiveCalls('image1.png', 'image1.png');

        $this->object->getNotDuplicatedFilename('/i/m/image.png', '/i/m');
    }

    protected function setUp()
    {
        /**
         * @var Context|MockObject $contextMock
         */
        $contextMock              = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->mediaDirectoryMock = $this->getMockForAbstractClass(WriteInterface::class);

        $objectManagerHelper = new ObjectManager($this);

        $this->object = $objectManagerHelper->getObject(
            Image::class,
            [
                'context'        => $contextMock,
                'mediaDirectory' => $this->mediaDirectoryMock,
            ]
        );
    }
}
