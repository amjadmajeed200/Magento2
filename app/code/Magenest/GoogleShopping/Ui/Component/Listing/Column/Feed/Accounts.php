<?php
namespace Magenest\GoogleShopping\Ui\Component\Listing\Column\Feed;

use Magenest\GoogleShopping\Model\Client;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class FeedTemplate
 * @package Magenest\GoogleShopping\Ui\Component\Listing\Column\Feed
 */
class Accounts implements OptionSourceInterface
{
    /** @var Client  */
    protected $client;

    /**
     * FeedTemplate constructor.
     * @param Client $client
     */
    public function __construct(
        Client $client
    ) {
        $this->client = $client;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $result = [];
        $accounts = $this->client->getAccounts();
        if(!empty($accounts)){
            foreach ($accounts as $account) {
                $result[] = [
                    'label' => $account['name'],
                    'value' => $account['merchantId']
                ];
            }
        }
        return $result;
    }
}
