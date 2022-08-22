<?php
namespace Magenest\GoogleShopping\Controller\Adminhtml\Crontask;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Cron\Model\ResourceModel\Schedule as CronScheduleResource;
use Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory as CronScheduleCollection;
use Magento\Cron\Model\ScheduleFactory;
use Magento\Framework\View\Result\PageFactory as ResultPageFactory;

abstract class AbstractCronTask extends Action
{
    const ADMIN_RESOURCE = 'Magenest_GoogleShopping::cron_task';

    /** @var ResultPageFactory  */
    protected $resultPageFactory;

    /** @var ScheduleFactory  */
    protected $_cronScheduleFactory;

    /** @var CronScheduleResource  */
    protected $_cronScheduleResource;

    /** @var CronScheduleCollection  */
    protected $_cronScheduleCollection;

    public function __construct(
        ResultPageFactory $resultPageFactory,
        ScheduleFactory $cronScheduleFactory,
        CronScheduleResource $cronScheduleResource,
        CronScheduleCollection $cronScheduleCollection,
        Context $context
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_cronScheduleCollection = $cronScheduleCollection;
        $this->_cronScheduleFactory = $cronScheduleFactory;
        $this->_cronScheduleResource = $cronScheduleResource;
        parent::__construct($context);
    }
}
