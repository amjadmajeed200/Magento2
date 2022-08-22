<?php
namespace Magenest\GoogleShopping\Model\CategoryMapping;

use Magenest\GoogleShopping\Api\ReaderInterface;
use Magenest\GoogleShopping\Api\ReaderMultiplicityInterface;
use Magenest\GoogleShopping\Model\CategoryMapping\FileReader as CategoryMappingFileReader;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Exception\FileSystemException;
use Psr\Log\LoggerInterface;

/**
 * Class FileReaderMultiplicity
 * @package Magenest\GoogleShopping\Model\CategoryMapping
 */
class FileReaderMultiplicity implements ReaderMultiplicityInterface
{
    /** @var array */
    protected $items = [];

    /** @var CategoryMappingFileReader  */
    protected $_fileReader;

    /** @var Reader  */
    protected $_moduleReader;

    /** @var Filesystem  */
    protected $_fileSystem;

    /** @var LoggerInterface  */
    protected $_logger;

    public function __construct(
        CategoryMappingFileReader $fileReader,
        Reader $moduleReader,
        Filesystem $filesystem,
        LoggerInterface $logger
    ) {
        $this->_fileReader = $fileReader;
        $this->_moduleReader = $moduleReader;
        $this->_fileSystem = $filesystem;
        $this->_logger = $logger;
    }

    public function addItem(ReaderInterface $item)
    {
        $this->items[] = $item;

        return $this;
    }

    public function getItems()
    {
        return $this->items;
    }

    public function count()
    {
        return count($this->items);
    }

    /**
     * @return $this|FileReaderMultiplicity
     */
    public function findAll()
    {
        try {
            $mappingPaths = $this->getMappingPaths();
            foreach ($mappingPaths as $mappingPath) {
                foreach (glob($mappingPath . "/*.txt") as $filename) {
                    $this->addItem($this->_fileReader->setFile($filename));
                }
            }
        } catch (\Exception $exception) {
            $this->_logger->debug($exception->getMessage());
        }
        return $this;
    }

    /**
     * @return array
     * @throws FileSystemException
     */
    protected function getMappingPaths()
    {
        $paths = [];
        try {
            $paths[] = realpath($this->_moduleReader->getModuleDir('etc', 'Magenest_GoogleShopping') . '/../Setup/Patch/Data/Mapping/');
            $paths[] = $this->_fileSystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath() . 'googleshopping/mapping/';
        } catch (\Exception $exception) {
            throw new FileSystemException(__($exception->getMessage()));
        }
        return $paths;
    }
}
