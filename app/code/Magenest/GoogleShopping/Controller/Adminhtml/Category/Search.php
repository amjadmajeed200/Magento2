<?php
namespace Magenest\GoogleShopping\Controller\Adminhtml\Category;

use Magenest\GoogleShopping\Model\CategoryMapping\FileReaderMultiplicity;
use Magenest\GoogleShopping\Model\CategoryMapping\ReaderMapper;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Psr\Log\LoggerInterface;

/**
 * Class Search
 * @package Magenest\GoogleShopping\Controller\Adminhtml\Category
 */
class Search extends \Magento\Backend\App\Action
{
    /** @var FileReaderMultiplicity  */
    protected $_fileReaderMultiplicity;

    /** @var ReaderMapper  */
    protected $_readerMapper;

    /** @var LoggerInterface  */
    protected $_logger;

    /**
     * Search constructor.
     * @param FileReaderMultiplicity $fileReaderMultiplicity
     * @param ReaderMapper $readerMapper
     * @param LoggerInterface $logger
     * @param Context $context
     */
    public function __construct(
        FileReaderMultiplicity $fileReaderMultiplicity,
        ReaderMapper $readerMapper,
        LoggerInterface $logger,
        Context $context
    ) {
        $this->_fileReaderMultiplicity = $fileReaderMultiplicity;
        $this->_readerMapper = $readerMapper;
        $this->_logger = $logger;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        try {
            $search = $this->getRequest()->getParam('query');
            $this->_fileReaderMultiplicity->findAll();
            if ($this->_fileReaderMultiplicity->count()) {
                $this->_readerMapper->addMultiplicity($this->_fileReaderMultiplicity);
            }
            $resultPage->setData($this->_readerMapper->getData($search));
        } catch (\Exception $exception) {
            $this->_logger->debug($exception->getMessage());
        }
        return $resultPage;
    }
}
