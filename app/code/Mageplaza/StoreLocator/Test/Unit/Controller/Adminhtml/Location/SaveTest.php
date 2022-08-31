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

namespace Mageplaza\StoreLocator\Test\Unit\Controller\Adminhtml\Location;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Js;
use Magento\Backend\Model\Session;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Mageplaza\StoreLocator\Controller\Adminhtml\Location\Save;
use Mageplaza\StoreLocator\Helper\Data;
use Mageplaza\StoreLocator\Helper\Image;
use Mageplaza\StoreLocator\Model\Location;
use Mageplaza\StoreLocator\Model\LocationFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * Class SaveTest
 * @package Mageplaza\StoreLocator\Test\Unit\Controller\Adminhtml\Location
 */
class SaveTest extends TestCase
{
    /**
     * @var Save
     */
    private $object;

    /**
     * @var RequestInterface|MockObject
     */
    private $requestMock;

    /**
     * @var LocationFactory|MockObject
     */
    private $locationFactoryMock;

    /**
     * @var RedirectFactory|MockObject
     */
    private $resultRedirectFactoryMock;

    /**
     * @var DateTime|MockObject
     */
    private $dateMock;

    /**
     * @var Js|MockObject
     */
    private $jsHelperMock;

    /**
     * @var EventManagerInterface|MockObject
     */
    private $eventManagerMock;

    /**
     * @var MessageManagerInterface|MockObject
     */
    private $messageManagerMock;

    /**
     * @var Session|MockObject
     */
    private $sessionMock;

    /**
     * @var Data|MockObject
     */
    private $helperDataMock;

    /**
     * @var Image|MockObject
     */
    private $imageHelperMock;

    /**
     * @var WriteInterface|MockObject
     */
    private $mediaDirectoryMock;

    /**
     * @var File|MockObject
     */
    private $fileMock;

    /**
     * @var ProductCollectionFactory|MockObject
     */
    private $productCollectionFactoryMock;

    public function testExecuteWithNoPostValue()
    {
        $resultRedirect = $this->getMockBuilder(Redirect::class)
            ->disableOriginalConstructor()->getMock();
        $this->resultRedirectFactoryMock->expects($this->once())->method('create')
            ->willReturn($resultRedirect);
        $this->requestMock->expects($this->once())->method('getPostValue')->willReturn(null);
        $resultRedirect->expects($this->once())->method('setPath')->with('mpstorelocator/*/')->willReturnSelf();

        $this->assertEquals($resultRedirect, $this->object->execute());
    }

    public function testExecuteWithLocationDoesNotExits()
    {
        $resultRedirect = $this->getMockBuilder(Redirect::class)
            ->disableOriginalConstructor()->getMock();
        $this->resultRedirectFactoryMock->expects($this->once())->method('create')
            ->willReturn($resultRedirect);
        $this->requestMock->expects($this->once())->method('getPostValue')->willReturn(['data']);
        $this->requestMock->expects($this->once())->method('getParam')->with('id')->willReturn(1);
        $location = $this->getMockBuilder(Location::class)->disableOriginalConstructor()->getMock();
        $this->locationFactoryMock->expects($this->once())->method('create')->willReturn($location);
        $location->expects($this->once())->method('load')->with(1)->willReturnSelf();
        $location->expects($this->once())->method('getId')->willReturn(null);
        $this->messageManagerMock->expects($this->once())->method('addErrorMessage')
            ->with(__('This location no longer exists.'))->willReturnSelf();
        $resultRedirect->expects($this->once())->method('setPath')->with('mpstorelocator/*/')->willReturnSelf();

        $this->assertEquals($resultRedirect, $this->object->execute());
    }

    /**
     * @return array[]
     */
    public function providerExecute()
    {
        $resultData = [
            'general'     => [
                'name'        => 'nghĩa',
                'status'      => '1',
                'description' => '',
                'url_key'     => 'nghia',
                'store_ids'   => [0 => '0'],
                'sort_order'  => '0',
            ],
            'location'    => [
                'street'         => '123',
                'city'           => '123',
                'state_province' => '123',
                'postal_code'    => '123',
                'country'        => 'AF',
                'latitude'       => '20.9790643',
                'longitude'      => '105.7854772'
            ],
            'time'        => [
                'operation_mon'       => [
                    'value' => '0',
                    'from'  => [0 => '08', 1 => '00'],
                    'to'    => [0 => '17', 1 => '30']
                ],
                'operation_tue'       => 'use_config',
                'operation_wed'       => 'use_config',
                'operation_thu'       => 'use_config',
                'operation_fri'       => 'use_config',
                'operation_sat'       => 'use_config',
                'operation_sun'       => 'use_config',
                'time_zone'           => 'use_config',
                'is_config_time_zone' => 1
            ],
            'contact'     => [
                'phone_one'         => '',
                'phone_two'         => '',
                'website'           => 'https://mageplaza.com',
                'fax'               => '',
                'email'             => '',
                'is_config_website' => 1,
            ],
            'imagesArray' => [
                [
                    'position' => '6',
                    'file'     => '/i/m/image4.png',
                    'label'    => ''
                ],
                [
                    'position' => '7',
                    'file'     => '/i/m/image5.png',
                    'label'    => ''
                ]
            ],
            'images'      => [
                'images' => '[{"position":"6","file":"\/i\/m\/image4.png","label":""},{"position":"7","file":"\/i\/m\/image5.png","label":""}]'
            ],

        ];

        return [
            [$this->getPostValue(), $resultData, 1, 1],
            [$this->getPostValue(0), $resultData, 1, 0],
            [$this->getPostValue(0, 0), $resultData, 0, 1],
            [$this->getPostValue(0, 0, 0), $resultData, 0, 0],
            [$this->getPostValue(0, 0, 0, 0), $resultData, 1, 0],
            [$this->getPostValue(0, 0, 0, 0, 0), $resultData, 0, 1],
            [$this->getPostValue(0, 0, 0, 0, 0), $resultData, 1, 1],

        ];
    }

    /**
     * @param int $hasImage
     * @param int $hasContact
     * @param int $hasTime
     * @param int $isSelectedAllProduct
     * @param int $hasHolidays
     *
     * @return array
     */
    public function getPostValue(
        $hasImage = 1,
        $hasContact = 1,
        $hasTime = 1,
        $isSelectedAllProduct = 1,
        $hasHolidays = 1
    ) {
        $postValue = [
            'general'            => [
                'name'        => 'nghĩa',
                'status'      => '1',
                'description' => '',
                'url_key'     => 'nghia',
                'store_ids'   => [0 => '0'],
                'sort_order'  => '0'
            ],
            'location'           => [
                'street'         => '123',
                'city'           => '123',
                'state_province' => '123',
                'postal_code'    => '123',
                'country'        => 'AF',
                'latitude'       => '20.9790643',
                'longitude'      => '105.7854772'
            ],
            'limit'              => '20',
            'page'               => '1',
            'in_holidays'        => '',
            'holiday_id'         => ['from' => '', 'to' => ''],
            'name'               => '',
            'created_at'         => ['from' => '', 'to' => '', 'locale' => 'en_US'],
            'position'           => '',
            'template-images'    => '[{"position":"0","file":"\/s\/c\/screenshot_from_2020-05-11_09-44-46_1.png","label":""},{"position":"1","file":"\/s\/c\/screenshot_1.png","label":""}]',
            'available_products' => ['is_show_product_page' => '0', 'is_selected_all_product' => '0'],
            'in_product'         => '',
            'entity_id'          => ['from' => '', 'to' => ''],
            'price'              => ['from' => '', 'to' => ''],
            'product_ids'        => '2023&2030&2046&'
        ];

        if ($isSelectedAllProduct) {
            $postValue['available_products']['is_selected_all_product'] = '0';
        }

        if ($hasHolidays) {
            $postValue['holidays'] = '1=asd&2=cas';
        }

        if ($hasContact) {
            $postValue['contact'] = [
                'phone_one' => '',
                'phone_two' => '',
                'website'   => ['use_system_config' => '0'],
                'fax'       => '',
                'email'     => ''
            ];
        }
        if ($hasTime) {
            $postValue['time'] = [
                'operation_mon' => [
                    'value' => '0',
                    'from'  => [0 => '08', 1 => '00'],
                    'to'    => [0 => '17', 1 => '30']
                ],
                'operation_tue' => ['use_system_config' => '1'],
                'operation_wed' => ['use_system_config' => '1'],
                'operation_thu' => ['use_system_config' => '1'],
                'operation_fri' => ['use_system_config' => '1'],
                'operation_sat' => ['use_system_config' => '1'],
                'operation_sun' => ['use_system_config' => '1'],
                'time_zone'     => ['use_system_config' => '0']
            ];
        }
        if ($hasImage) {
            $postValue ['images'] = [
                'gko0apbifkn' => [
                    'position' => '0',
                    'file'     => '/i/m/image1.png.tmp',
                    'label'    => '',
                    'removed'  => '1'
                ],
                'g3cc2vv3clm' => [
                    'position' => '1',
                    'file'     => '/i/m/image2.png',
                    'label'    => '',
                    'removed'  => '1'
                ],
                'g3cc2vv3cln' => [
                    'position' => '2',
                    'file'     => '/i/m/not_exist_image.png',
                    'label'    => '',
                    'removed'  => '1'
                ],
                'gko0apbifkh' => [
                    'position' => '3',
                    'file'     => '/i/m/not_exist_image.png.tmp',
                    'label'    => '',
                    'removed'  => ''
                ],
                'gko0apbifkm' => [
                    'position' => '4',
                    'file'     => '/i/m/no_extension_file.tmp',
                    'label'    => '',
                    'removed'  => ''
                ],
                'gko0apbifkg' => [
                    'position' => '5',
                    'file'     => '/i/m/not_image_file.doc.tmp',
                    'label'    => '',
                    'removed'  => ''
                ],
                'gko0apbifkp' => [
                    'position' => '6',
                    'file'     => '/i/m/image4.png.tmp',
                    'label'    => '',
                    'removed'  => ''
                ],
                'gko0apbifko' => [
                    'position' => '7',
                    'file'     => '/i/m/image5.png',
                    'label'    => '',
                    'removed'  => ''
                ]
            ];
        }

        return $postValue;
    }

    /**
     * @param $data
     * @param $resultData
     * @param $locationId
     * @param $back
     *
     * @throws LocalizedException
     * @dataProvider providerExecute
     */
    public function testExecute($data, $resultData, $locationId, $back)
    {
        $resultRedirect = $this->getMockBuilder(Redirect::class)
            ->disableOriginalConstructor()->getMock();
        $this->resultRedirectFactoryMock->expects($this->once())->method('create')
            ->willReturn($resultRedirect);
        $this->requestMock->expects($this->once())->method('getPostValue')->willReturn($data);
        $count = 0;
        if (isset($data['images'])) {
            $media = 'mageplaza/store_locator';
            $this->imageHelperMock->expects($this->exactly(3))->method('getMediaPath')
                ->withConsecutive(
                    ['/i/m/image2.png'],
                    ['/i/m/not_exist_image.png'],
                    ['/i/m/image4.png']
                )
                ->willReturnOnConsecutiveCalls(
                    $media . '/i/m/image2.png',
                    $media . '/i/m/not_exist_image.png',
                    $media . '/i/m/image4.png'
                );

            $this->mediaDirectoryMock->expects($this->exactly(6))->method('getRelativePath')
                ->withConsecutive(
                    [$media . '/i/m/image2.png'],
                    [$media . '/i/m/not_exist_image.png'],
                    ['tmp/i/m/not_exist_image.png'],
                    ['tmp/i/m/no_extension_file'],
                    ['tmp/i/m/not_image_file.doc'],
                    ['tmp/i/m/image4.png']
                )
                ->willReturnOnConsecutiveCalls(
                    $media . '/i/m/image2.png',
                    $media . '/i/m/not_exist_image.png',
                    $media . 'tmp/i/m/not_exist_image.png',
                    $media . 'tmp/i/m/no_extension_file',
                    $media . 'tmp/i/m/not_image_file.doc',
                    $media . 'tmp/i/m/image4.png'
                );

            $this->mediaDirectoryMock->expects($this->exactly(6))->method('isFile')
                ->withConsecutive(
                    [$media . '/i/m/image2.png'],
                    [$media . '/i/m/not_exist_image.png'],
                    [$media . 'tmp/i/m/not_exist_image.png'],
                    [$media . 'tmp/i/m/no_extension_file'],
                    [$media . 'tmp/i/m/not_image_file.doc'],
                    [$media . 'tmp/i/m/image4.png']
                )
                ->willReturnOnConsecutiveCalls(
                    true,
                    false,
                    false,
                    true,
                    true,
                    true
                );

            $this->mediaDirectoryMock->expects($this->once())->method('delete')
                ->with($media . '/i/m/image2.png')->willReturnSelf();

            $this->imageHelperMock->expects($this->exactly(4))->method('getTmpMediaPath')
                ->withConsecutive(
                    ['/i/m/not_exist_image.png'],
                    ['/i/m/no_extension_file'],
                    ['/i/m/not_image_file.doc'],
                    ['/i/m/image4.png']
                )
                ->willReturnOnConsecutiveCalls(
                    'tmp/i/m/not_exist_image.png',
                    'tmp/i/m/no_extension_file',
                    'tmp/i/m/not_image_file.doc',
                    'tmp/i/m/image4.png'
                );
            $this->fileMock->expects($this->exactly(3))->method('getPathInfo')
                ->withConsecutive(
                    [$media . 'tmp/i/m/no_extension_file'],
                    [$media . 'tmp/i/m/not_image_file.doc'],
                    [$media . 'tmp/i/m/image4.png']
                )
                ->willReturnOnConsecutiveCalls(
                    [],
                    ['extension' => 'doc'],
                    [
                        'extension' => 'png',
                        'basename'  => 'image4.png'
                    ]
                );

            $this->imageHelperMock->expects($this->once())->method('getNotDuplicatedFilename')
                ->with('/i/m/image4.png', '/i/m')
                ->willReturn('/i/m/image4.png');

            $this->mediaDirectoryMock->expects($this->once())->method('renameFile')
                ->with($media . 'tmp/i/m/image4.png', $media . '/i/m/image4.png')
                ->willReturnSelf();

            $this->helperDataMock->expects($this->at($count))->method('jsEncode')
                ->with($resultData['imagesArray'])
                ->willReturn($resultData['images']['images']);
            $count++;
        }

        $this->requestMock->expects($this->once())->method('getPostValue')->willReturn(['data']);
        $this->requestMock->expects($this->exactly(2))->method('getParam')
            ->withConsecutive(['id'], ['back'])->willReturn($locationId, $back);

        $locationMethods = array_unique(
            array_merge(
                get_class_methods(Location::class),
                ['setIsHolidayGrid', 'setHolidaysIds']
            )
        );
        $location        = $this->getMockBuilder(Location::class)
            ->setMethods($locationMethods)
            ->disableOriginalConstructor()->getMock();
        $this->locationFactoryMock->expects($this->once())->method('create')->willReturn($location);

        $locationCount = 0;
        if ($locationId) {
            $location->expects($this->once())->method('load')->with(1)->willReturnSelf();
            $locationCount++;
            $location->expects($this->at($locationCount))->method('getId')->willReturn($locationId);
            $locationCount++;
        }

        $this->sessionMock->expects($this->exactly(2))->method('setData')
            ->withConsecutive(
                ['mageplaza_storelocator_location_model', $location],
                ['mageplaza_storelocator_location_data', false]
            )
            ->willReturnSelf();

        $location->expects($this->at($locationCount))->method('addData')
            ->with($resultData['general'])->willReturnSelf();
        $locationCount++;
        $location->expects($this->at($locationCount))->method('addData')
            ->with($resultData['location'])->willReturnSelf();
        $locationCount++;
        if (isset($data['contact'])) {
            if (isset($data['contact']['website']['use_system_config'])) {
                $this->helperDataMock->expects($this->at($count))->method('getConfigGeneral')
                    ->with('website')->willReturn('https://mageplaza.com');
                $count++;
            }

            $location->expects($this->at($locationCount))->method('addData')
                ->with($resultData['contact'])->willReturnSelf();
            $locationCount++;
        }

        if (isset($data['images'])) {
            $location->expects($this->at($locationCount))->method('addData')
                ->with($resultData['images'])->willReturnSelf();
            $locationCount++;
        }

        if (isset($data['time'])) {
            if (!isset($data['time']['operation_mon']['use_system_config'])) {
                $this->helperDataMock->expects($this->at($count))->method('jsEncode')
                    ->with($data['time']['operation_mon'])
                    ->willReturn($resultData['time']['operation_mon']);
                $count++;
            }
            if (!isset($data['time']['operation_tue']['use_system_config'])) {
                $this->helperDataMock->expects($this->at($count))->method('jsEncode')
                    ->with($data['time']['operation_tue'])
                    ->willReturn($resultData['time']['operation_tue']);
                $count++;
            }
            if (!isset($data['time']['operation_wed']['use_system_config'])) {
                $this->helperDataMock->expects($this->at($count))->method('jsEncode')
                    ->with($data['time']['operation_wed'])
                    ->willReturn($resultData['time']['operation_wed']);
                $count++;
            }
            if (!isset($data['time']['operation_thu']['use_system_config'])) {
                $this->helperDataMock->expects($this->at($count))->method('jsEncode')
                    ->with($data['time']['operation_thu'])
                    ->willReturn($resultData['time']['operation_thu']);
                $count++;
            }
            if (!isset($data['time']['operation_fri']['use_system_config'])) {
                $this->helperDataMock->expects($this->at($count))->method('jsEncode')
                    ->with($data['time']['operation_fri'])
                    ->willReturn($resultData['time']['operation_fri']);
                $count++;
            }
            if (!isset($data['time']['operation_sat']['use_system_config'])) {
                $this->helperDataMock->expects($this->at($count))->method('jsEncode')
                    ->with($data['time']['operation_sat'])
                    ->willReturn($resultData['time']['operation_sat']);
                $count++;
            }
            if (!isset($data['time']['operation_sun']['use_system_config'])) {
                $this->helperDataMock->expects($this->at($count))->method('jsEncode')
                    ->with($data['time']['operation_sun'])
                    ->willReturn($resultData['time']['operation_sun']);
            }

            $location->expects($this->at($locationCount))->method('addData')
                ->with($resultData['time'])->willReturnSelf();
            $locationCount++;
        }

        if ($data['available_products']['is_selected_all_product']) {
            $productCollection = $this->getMockBuilder(ProductCollection::class)
                ->disableOriginalConstructor()->getMock();
            $this->productCollectionFactoryMock->expects($this->once())
                ->method('create')->willReturn($productCollection);
            $productCollection->expects($this->once())->method('getAllIds')->willReturn([1, 2, 3]);
            $this->sessionMock->expects($this->once())->method('setProductIds')
                ->with('1&2&3')->willReturnSelf();
        }

        if (isset($data['holidays'])) {
            $location->expects($this->at($locationCount))->method('setIsHolidayGrid')->with(true)->willReturnSelf();
            $locationCount++;
            $this->jsHelperMock->expects($this->once())->method('decodeGridSerializedInput')
                ->with($data['holidays'])->willReturn([1, 2]);
            $location->expects($this->at($locationCount))->method('setHolidaysIds')->with([1, 2])->willReturnSelf();
            $locationCount++;
        }

        $this->eventManagerMock->expects($this->once())->method('dispatch')
            ->with(
                'mageplaza_storelocator_location_prepare_save',
                ['location' => $location, 'request' => $this->requestMock]
            )->willReturnSelf();

        $location->expects($this->once())->method('save')->willReturnSelf();
        $locationCount++;

        $this->messageManagerMock->expects($this->once())->method('addSuccessMessage')
            ->with(__('The location has been saved.'))
            ->willReturnSelf();
        if ($back) {
            $location->expects($this->at($locationCount))->method('getId')->willReturn(1);
            $resultRedirect->expects($this->once())->method('setPath')
                ->with('mpstorelocator/*/edit', ['id' => 1, '_current' => true])->willReturnSelf();
        } else {
            $resultRedirect->expects($this->once())->method('setPath')
                ->with('mpstorelocator/*/')->willReturnSelf();
        }

        $this->assertEquals($resultRedirect, $this->object->execute());
    }

    public function testExecuteWithSaveThrowRuntimeException()
    {
        $data           = $this->getPostValue(0, 0, 0, 0, 0);
        $resultRedirect = $this->getMockBuilder(Redirect::class)
            ->disableOriginalConstructor()->getMock();
        $this->resultRedirectFactoryMock->expects($this->once())->method('create')
            ->willReturn($resultRedirect);
        $this->requestMock->expects($this->once())->method('getPostValue')->willReturn($data);
        $this->requestMock->expects($this->once())->method('getParam')->with('id')->willReturn(0);

        $location = $this->getMockBuilder(Location::class)->disableOriginalConstructor()->getMock();
        $this->locationFactoryMock->expects($this->once())->method('create')->willReturn($location);
        $location->expects($this->exactly(2))->method('addData')
            ->withConsecutive([$data['general']], [$data['location']])->willReturnSelf();
        $this->eventManagerMock->expects($this->once())->method('dispatch')
            ->with(
                'mageplaza_storelocator_location_prepare_save',
                ['location' => $location, 'request' => $this->requestMock]
            )->willReturnSelf();
        $location->expects($this->once())->method('save')->willThrowException(new RuntimeException('Runtime Error'));
        $this->messageManagerMock->expects($this->once())->method('addErrorMessage')
            ->with('Runtime Error')->willReturnSelf();

        $this->sessionMock->expects($this->exactly(2))->method('setData')
            ->withConsecutive(
                ['mageplaza_storelocator_location_model', $location],
                ['mageplaza_storelocator_location_data', $data]
            )
            ->willReturnSelf();

        $location->expects($this->once())->method('getId')->willReturn(null);

        $resultRedirect->expects($this->once())->method('setPath')
            ->with(
                'mpstorelocator/*/edit',
                ['id' => null, '_current' => true]
            )
            ->willReturnSelf();

        $this->assertEquals($resultRedirect, $this->object->execute());
    }

    public function testExecuteWithSaveThrowException()
    {
        $data = $this->getPostValue(0, 0, 0, 0, 0);

        $resultRedirect = $this->getMockBuilder(Redirect::class)
            ->disableOriginalConstructor()->getMock();
        $this->resultRedirectFactoryMock->expects($this->once())->method('create')
            ->willReturn($resultRedirect);
        $this->requestMock->expects($this->once())->method('getPostValue')->willReturn($data);
        $this->requestMock->expects($this->once())->method('getParam')->with('id')->willReturn(0);

        $location = $this->getMockBuilder(Location::class)->disableOriginalConstructor()->getMock();
        $this->locationFactoryMock->expects($this->once())->method('create')->willReturn($location);
        $location->expects($this->exactly(2))->method('addData')
            ->withConsecutive([$data['general']], [$data['location']])->willReturnSelf();
        $this->eventManagerMock->expects($this->once())->method('dispatch')
            ->with(
                'mageplaza_storelocator_location_prepare_save',
                ['location' => $location, 'request' => $this->requestMock]
            )->willReturnSelf();
        $e = new Exception('Exception Error');
        $location->expects($this->once())->method('save')->willThrowException($e);
        $this->messageManagerMock->expects($this->once())->method('addExceptionMessage')
            ->with($e, __('Something went wrong while saving the Location.'))->willReturnSelf();

        $this->sessionMock->expects($this->exactly(2))->method('setData')
            ->withConsecutive(
                ['mageplaza_storelocator_location_model', $location],
                ['mageplaza_storelocator_location_data', $data]
            )
            ->willReturnSelf();

        $location->expects($this->once())->method('getId')->willReturn(null);

        $resultRedirect->expects($this->once())->method('setPath')
            ->with(
                'mpstorelocator/*/edit',
                ['id' => null, '_current' => true]
            )
            ->willReturnSelf();

        $this->assertEquals($resultRedirect, $this->object->execute());
    }

    protected function setUp()
    {
        /**
         * @var Context|MockObject $contextMock
         */
        $contextMock                        = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultRedirectFactoryMock    = $this->getMockBuilder(RedirectFactory::class)
            ->disableOriginalConstructor()->getMock();
        $requestMethods                     = array_unique(
            array_merge(
                get_class_methods(RequestInterface::class),
                ['getPostValue', 'getPost']
            )
        );
        $this->requestMock                  = $this->getMockForAbstractClass(
            RequestInterface::class,
            [],
            '',
            false,
            false,
            false,
            $requestMethods
        );
        $this->dateMock                     = $this->getMockBuilder(DateTime::class)
            ->disableOriginalConstructor()->getMock();
        $this->jsHelperMock                 = $this->getMockBuilder(Js::class)
            ->disableOriginalConstructor()->getMock();
        $this->eventManagerMock             = $this->getMockBuilder(EventManagerInterface::class)
            ->disableOriginalConstructor()->getMock();
        $this->messageManagerMock           = $this->getMockBuilder(MessageManagerInterface::class)
            ->disableOriginalConstructor()->getMock();
        $sessionMethods                     = array_unique(
            array_merge(
                get_class_methods(Session::class),
                ['setData', 'setProductIds']
            )
        );
        $this->sessionMock                  = $this->getMockBuilder(Session::class)
            ->setMethods($sessionMethods)
            ->disableOriginalConstructor()->getMock();
        $this->helperDataMock               = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()->getMock();
        $this->locationFactoryMock          = $this->getMockBuilder(LocationFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->imageHelperMock              = $this->getMockBuilder(Image::class)
            ->disableOriginalConstructor()->getMock();
        $this->fileMock                     = $this->getMockBuilder(File::class)
            ->disableOriginalConstructor()->getMock();
        $this->productCollectionFactoryMock = $this->getMockBuilder(ProductCollectionFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->mediaDirectoryMock           = $this->getMockForAbstractClass(WriteInterface::class);
        $contextMock->method('getRequest')->willReturn($this->requestMock);
        $contextMock->method('getResultRedirectFactory')->willReturn($this->resultRedirectFactoryMock);
        $contextMock->method('getEventManager')->willReturn($this->eventManagerMock);
        $contextMock->method('getMessageManager')->willReturn($this->messageManagerMock);
        $contextMock->method('getSession')->willReturn($this->sessionMock);

        $objectManagerHelper = new ObjectManager($this);
        $this->object        = $objectManagerHelper->getObject(
            Save::class,
            [
                'context'                  => $contextMock,
                'date'                     => $this->dateMock,
                'jsHelper'                 => $this->jsHelperMock,
                'locationFactory'          => $this->locationFactoryMock,
                '_helperData'              => $this->helperDataMock,
                '_imageHelper'             => $this->imageHelperMock,
                'mediaDirectory'           => $this->mediaDirectoryMock,
                '_file'                    => $this->fileMock,
                'productCollectionFactory' => $this->productCollectionFactoryMock,
                'resultRedirectFactory'    => $this->resultRedirectFactoryMock
            ]
        );
    }
}
