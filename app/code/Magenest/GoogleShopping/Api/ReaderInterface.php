<?php
namespace Magenest\GoogleShopping\Api;

interface ReaderInterface
{
    /**
     * @param int $limit
     * @return $this
     */
    public function setLimit(int $limit);

    /**
     * @return int
     */
    public function getLimit();

    /**
     * @param string $search
     * @return array
     */
    public function getRows(string $search);
}
