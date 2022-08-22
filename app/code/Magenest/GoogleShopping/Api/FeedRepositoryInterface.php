<?php
namespace Magenest\GoogleShopping\Api;

interface FeedRepositoryInterface
{
    /**
     * Retrieve Feed.
     *
     * @param string $feedId
     * @return \Magenest\GoogleShopping\Api\Data\FeedInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($feedId);
}
