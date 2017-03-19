<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Shop\Service;

interface OrderManagerInterface
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
     * Counts all orders
     * 
     * @param boolean $approved Whether to count only approved orders
     * @return integer
     */
    public function countAll($approved);

    /**
     * Counts amount of unapproved orders
     * 
     * @return integer
     */
    public function countUnapproved();

    /**
     * Approves an order by its associated id
     * 
     * @param string $id Order's id
     * @return boolean
     */
    public function approveById($id);

    /**
     * Update order statuses
     * 
     * @param array $relations
     * @return boolean
     */
    public function updateOrderStatuses(array $relations);

    /**
     * Removes an order by its associated id
     * 
     * @param string $id Order's id
     * @return boolean
     */
    public function deleteById($id);

    /**
     * Remove a collection of orders by their associated id
     * 
     * @param array $ids
     * @return boolean
     */
    public function deleteByIds(array $ids);

    /**
     * Fetches order entity by its associated ID
     * 
     * @param string $id Order id
     * @return \Krystal\Stdlib\VirtualEntity
     */
    public function fetchById($id);

    /**
     * Fetches all orders associated with customer ID
     * 
     * @param string $customerId
     * @return array
     */
    public function fetchAllByCustomerId($customerId);

    /**
     * Fetches all details by associated order ID
     * 
     * @param string $id Order's ID
     * @param string $customerId Optional filter by customer ID
     * @param string $coverDimensions Cover dimensions for image covers to be returned
     * @return array
     */
    public function fetchAllDetailsByOrderId($id, $customerId = null, $coverDimensions = '75x75');

    /**
     * Returns prepared paginator's instance
     * 
     * @return \Krystal\Paginate\Paginator
     */
    public function getPaginator();

    /**
     * Makes an order
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function make(array $input);

    /**
     * Fetches latest order entities
     * 
     * @param integer $limit
     * @return array
     */
    public function fetchLatest($limit);

    /**
     * Fetches all entities filtered by pagination
     * 
     * @param integer $page Current page number
     * @param integer $itemsPerPage Per page count
     * @return array
     */
    public function fetchAllByPage($page, $itemsPerPage);
}
