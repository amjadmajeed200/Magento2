<?php
/**
 * @category  Magento Contact
 * @package   Mageants_Contact
 * @copyright Copyright (c) 2017 Magento
 * @author    Mageants Team <support@mageants.com>
 */
namespace Mageants\Contact\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

/**
 * Class ContactActions
 */
class ContactActions extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
               $item[$this->getData('name')]['delete'] = [
                    'href' => $this->urlBuilder->getUrl(
                        'mageants_contact/contact/delete',
                        ['id' => $item['message_id']]
                    ),
                    'label' => __('Delete'),
					'confirm' => [
						'title' => __('Delete Contact Message'),
						'message' => __('Are you sure you wan\'t to delete a  contact massage?')
					],
                    'hidden' => false,
                ];
                $item[$this->getData('name')]['edit'] = [
                   'href' => $this->urlBuilder->getUrl(
                       'mageants_contact/contact/edit',
                       ['id' => $item['message_id']]
                   ),
                   'label' => __('View'),
                   'hidden' => false,
               ];
            }
        }
        return $dataSource;
    }
}
