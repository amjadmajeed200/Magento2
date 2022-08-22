<?php
namespace Magenest\GoogleShopping\Api;

/**
 * Interface ReaderMultiplicityInterface
 * @package Magenest\GoogleShopping\Api
 */
interface ReaderMultiplicityInterface
{
    /**
     * @return $this
     */
    public function findAll();

    /**
     * @param \Magenest\GoogleShopping\Api\ReaderInterface $item
     * @return $this
     */
    public function addItem(\Magenest\GoogleShopping\Api\ReaderInterface $item);

    /**
     * @return array
     */
    public function getItems();
}
