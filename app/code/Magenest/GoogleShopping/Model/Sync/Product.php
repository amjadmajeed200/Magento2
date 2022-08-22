<?php
namespace Magenest\GoogleShopping\Model\Sync;

use Magenest\GoogleShopping\Model\Client;

class Product
{
    protected $_clientModel;

    public function __construct(
        Client $clientModel
    ) {
        $this->_clientModel = $clientModel;
    }

    /**
     * @param \Magenest\GoogleShopping\Model\GoogleFeed $googleFeed
     */
    public function sync($googleFeed)
    {
        $templateId = $googleFeed->getId();
        $conditions = $googleFeed->getConditionsSerialized();
    }
}
