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
 * @package     Mageplaza_OrderAttributes
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\StoreLocator\Test\Unit\Block\Adminhtml\Location\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form as FormWidget;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\UrlInterface;
use Mageplaza\StoreLocator\Block\Adminhtml\Location\Edit\Tab\AvailableProducts;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class AvailableProductsTest
 * @package Mageplaza\StoreLocator\Test\Unit\Block\Adminhtml\Location\Edit\Tab
 */
class AvailableProductsTest extends TestCase
{
    /**
     * @var Context|MockObject
     */
    private $contextMock;

    /**
     * @var UrlInterface|MockObject
     */
    private $urlBuilderMock;

    /**
     * @var AvailableProducts
     */
    private $availableProductsBlock;

    /**
     * @var Form|MockObject
     */
    private $formMock;

    /**
     * @var FormKey|MockObject
     */
    private $formKeyMock;

    /**
     * @var RequestInterface|MockObject
     */
    private $requestMock;

    public function testGetAjaxUrl()
    {
        $this->urlBuilderMock->expects($this->once())->method('getUrl')->with('mpstorelocator/location/productsgrid')
            ->willReturn('url');

        $this->assertEquals('url', $this->availableProductsBlock->getAjaxUrl());
    }

    public function testSelectAllJs()
    {
        $gridUrl    = $this->getProductGridUrl();
        $locationId = 1;
        $this->requestMock->expects($this->once())->method('getParam')->with('id')->willReturn($locationId);

        $result = '<script type="text/x-magento-init">
                {
                    "#available_products_is_selected_all_product": {
                        "Mageplaza_StoreLocator/js/is_selected": {
                            "gridUrl": "' . $gridUrl . '",
                            "locationId": "' . $locationId . '"
                        }
                    }
                }
            </script>';

        $this->assertEquals($result, $this->availableProductsBlock->selectAllJs());
    }

    /**
     * @return string
     */
    public function getProductGridUrl()
    {
        $formKey = 'asbab123';
        $this->formKeyMock->expects($this->once())->method('getFormKey')->willReturn($formKey);
        $this->urlBuilderMock->expects($this->once())->method('getUrl')
            ->with('mpstorelocator/location/productsgrid', ['form_key' => $formKey, 'loadGrid' => 1])
            ->willReturn('url/loadGrid/1/form_key/asbab123');

        return 'url/loadGrid/1/form_key/asbab123';
    }

    public function testGetProductGridUrl()
    {
        $gridUrl = $this->getProductGridUrl();

        $this->assertEquals($gridUrl, $this->availableProductsBlock->getProductGridUrl());
    }

    protected function setUp()
    {
        $this->contextMock    = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()->getMock();
        $this->requestMock    = $this->getMockBuilder(RequestInterface::class)
            ->disableOriginalConstructor()->getMock();
        $this->urlBuilderMock = $this->getMockBuilder(UrlInterface::class)
            ->disableOriginalConstructor()->getMock();
        $this->formKeyMock    = $this->getMockBuilder(FormKey::class)
            ->disableOriginalConstructor()->getMock();
        $this->formMock       = $this->getMockBuilder(FormWidget::class)
            ->disableOriginalConstructor()->getMock();
        $this->contextMock->method('getUrlBuilder')->willReturn($this->urlBuilderMock);
        $this->contextMock->method('getFormKey')->willReturn($this->formKeyMock);
        $this->contextMock->method('getRequest')->willReturn($this->requestMock);

        $objectManagerHelper = new ObjectManager($this);

        $this->availableProductsBlock = $objectManagerHelper->getObject(
            AvailableProducts::class,
            [
                'context' => $this->contextMock
            ]
        );
    }
}
