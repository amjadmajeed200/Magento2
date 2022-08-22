<?php
namespace Magenest\GoogleShopping\Model\CategoryMapping;

use Magenest\GoogleShopping\Api\ReaderInterface;

/**
 * Class FileReader
 * @package Magenest\GoogleShopping\Model\CategoryMapping
 */
class FileReader implements ReaderInterface
{
    /** @var \Magento\Framework\Filesystem\Driver\File  */
    protected $_fileSystem;

    public function __construct(
        \Magento\Framework\Filesystem\Driver\File $fileSystem
    ) {
        $this->_fileSystem = $fileSystem;
    }

    /**
     * @var int
     */
    protected $limit = 100;

    /**
     * @var string
     */
    protected $file;

    /**
     * @var string
     */
    protected $mappingDelimiter = ' > ';

    /**
     * @param int $limit
     * @return $this
     */
    public function setLimit(int $limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param string $file
     * @return $this
     */
    public function setFile(string $file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param string $search
     * @return array
     */
    public function getRows(string $search)
    {
        $result = [];
        $file = $this->getFile();
        if (isset($file)) {
            $handle = $this->_fileSystem->fileOpen($file, "r");
            $i = 0;
            while (($buffer = fgets($handle, 4096)) !== false) {
                if (($this->getLimit() && $i >= $this->getLimit())) {
                    break;
                }
                if (stripos($buffer, $search) !== false) {
                    $categories = explode($this->getMappingDelimiter(), trim($buffer));
                    $buffer = implode($this->getMappingDelimiter(), $categories);
                    $result[$buffer] = $this->getFileName();
                    $i++;
                }
            }
            $this->_fileSystem->fileClose($handle);
        }
        return $result;
    }

    /**
     * @return string
     */
    public function getMappingDelimiter()
    {
        return $this->mappingDelimiter;
    }

    /**
     * @param string $delimiter
     * @return $this
     */
    public function setMappingDelimiter(string $delimiter)
    {
        $this->mappingDelimiter = $delimiter;

        return $this;
    }

    /**
     * @return string
     */
    protected function getFileName()
    {
        return basename($this->getFile());
    }
}
