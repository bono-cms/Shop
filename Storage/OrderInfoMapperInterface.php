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

interface OrderInfoMapperInterface
{
    /**
     * Counts all orders
     * 
     * @param boolean $approved Whether to count only approved orders
     * @return integer
     */
    public function countAll($approved);

    /**
     * Counts amount of unapproved orders
     * 
     * @return string
     */
    public function countUnapproved();

    /**
     * Adds new order data
     * 
     * @param array $data
     * @return boolean
     */
    public function insert(array $data);

    /**
     * Fetches all orders associated with customer ID
     * 
     * @param string $customerId
     * @return array
     */
    public function fetchAllByCustomerId($customerId);

    /**
     * Fetches latest orders
     * 
     * @param integer $limit
     * @return array
     */
    public function fetchLatest($limit);

    /**
     * Fetches all orders filtered by pagination
     * 
     * @param integer $page Current page number
     * @param integer $itemsPerPage Per page count
     * @return array
     */
    public function fetchAllByPage($page, $itemsPerPage);

    /**
     * Deletes an order by its associated id
     * 
     * @param string $id Order's id
     * @return boolean
     */
    public function deleteById($id);

    /**
     * Updates order status ID by associated order ID
     * 
     * @param string $orderId
     * @param string $statusId
     * @return boolean
     */
    public function updateOrderStatus($orderId, $statusId);

    /**
     * Approves an order by its associated id
     * 
     * @param string $id Order's id
     * @return boolean
     */
    public function approveById($id);
}
