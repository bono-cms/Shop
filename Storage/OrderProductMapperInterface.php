<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Shop\Storage;

interface OrderProductMapperInterface
{
    /**
     * Counts the sum of sold products
     * 
     * @return float
     */
    public function getPriceSumCount();

    /**
     * Counts total amount of sold products
     * 
     * @return integer
     */
    public function getQtySumCount();

    /**
     * Deletes all products associated with provided order's id
     * 
     * @param string $id Order's id
     * @return boolean
     */
    public function deleteAllByOrderId($id);

    /**
     * Add an order
     * 
     * @param array $data
     * @return boolean
     */
    public function insert(array $data);

    /**
     * Decrements product in stock qty
     * 
     * @param string $id
     * @return boolean
     */
    public function decrementProductInStockQtyById($id);

    /**
     * Find product ids by associated order id
     * 
     * @param string $id Order id
     * @return array
     */
    public function findProductIdsByOrderId($id);

    /**
     * Fetches all details by associated order's id
     * 
     * @param string $id Order's ID
     * @param string $customerId Optional filter by customer ID
     * @return array
     */
    public function fetchAllDetailsByOrderId($id, $customerId = null);
}
