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

namespace Mageplaza\StoreLocator\Setup;

use Exception;
use Magento\Cms\Model\BlockFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Mageplaza\Core\Helper\AbstractData as CoreHelper;
use Mageplaza\StoreLocator\Helper\Data;

/**
 * Class InstallData
 * @package Mageplaza\StoreLocator\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var BlockFactory
     */
    private $_blockFactory;

    /**
     * @var CoreHelper
     */
    private $_helperData;

    /**
     * @var Filesystem
     */
    protected $_fileSystem;

    /**
     * @var DirectoryList
     */
    protected $_directoryList;

    /**
     * InstallData constructor.
     *
     * @param BlockFactory $blockFactory
     * @param Filesystem $filesystem
     * @param DirectoryList $directoryList
     * @param Data $helperData
     */
    public function __construct(
        BlockFactory $blockFactory,
        Filesystem $filesystem,
        DirectoryList $directoryList,
        Data $helperData
    ) {
        $this->_blockFactory  = $blockFactory;
        $this->_fileSystem    = $filesystem;
        $this->_directoryList = $directoryList;
        $this->_helperData    = $helperData;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @throws Exception
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $testBlock = [
            'title'      => 'Mageplaza Store Locator Static Block',
            'identifier' => 'mp-storelocator-block',
            'stores'     => [0],
            'content'    => $this->getDefaultTemplateHtml('footer'),
            'is_active'  => 1,
        ];

        if (!$this->_blockFactory->create()->load('mp-storelocator-block', 'identifier')) {
            $this->_blockFactory->create()->setData($testBlock)->save();
        }

        $this->_prepareDayData($setup, Data::MONDAY);
        $this->_prepareDayData($setup, Data::TUESDAY);
        $this->_prepareDayData($setup, Data::WEDNESDAY);
        $this->_prepareDayData($setup, Data::THURSDAY);
        $this->_prepareDayData($setup, Data::FRIDAY);
        $this->_prepareDayData($setup, Data::SATURDAY);
        $this->_prepareDayData($setup, Data::SUNDAY);
    }

    /**
     * @param $setup
     * @param $day
     */
    protected function _prepareDayData($setup, $day)
    {
        $configData = [
            $day => [
                'value' => '1',
                'from'  => [
                    '08',
                    '00'
                ],
                'to'    => [
                    '17',
                    '30'
                ]
            ]
        ];

        $value = $this->_helperData->jsEncode($configData);
        $data  = [
            'scope'    => 'default',
            'scope_id' => 0,
            'path'     => Data::CONFIG_MODULE_PATH . '/time_default/' . $day,
            'value'    => $value,
        ];
        $setup->getConnection()
            ->insertOnDuplicate($setup->getTable('core_config_data'), $data, ['value']);
    }

    /**
     * @param $templateId
     *
     * @return string
     * @throws FileSystemException
     */
    public function getDefaultTemplateHtml($templateId)
    {
        return $this->readFile($this->getTemplatePath($templateId));
    }

    /**
     * @param $relativePath
     *
     * @return string
     * @throws FileSystemException
     */
    public function readFile($relativePath)
    {
        $rootDirectory = $this->_fileSystem->getDirectoryRead(DirectoryList::ROOT);

        return $rootDirectory->readFile($relativePath);
    }

    /**
     * @param $templateId
     * @param string $type
     * @param string $subPath
     *
     * @return string
     */
    public function getTemplatePath(
        $templateId,
        $type = '.html',
        $subPath = 'view/base/templates/default/static-block/'
    ) {
        /** Get directory of Data.php */
        $currentDir = __DIR__;

        /** Get root directory(path of magento's project folder) */
        $rootPath = $this->_directoryList->getRoot();

        $currentDirArr = explode('\\', $currentDir);
        if (count($currentDirArr) === 1) {
            $currentDirArr = explode('/', $currentDir);
        }

        $rootPathArr = explode('/', $rootPath);
        if (count($rootPathArr) === 1) {
            $rootPathArr = explode('\\', $rootPath);
        }

        $basePath           = '';
        $countCurrentDirArr = count($currentDirArr);
        for ($i = count($rootPathArr); $i < $countCurrentDirArr - 1; $i++) {
            $basePath .= $currentDirArr[$i] . '/';
        }

        $templatePath = $basePath . $subPath;

        return $templatePath . $templateId . $type;
    }
}
