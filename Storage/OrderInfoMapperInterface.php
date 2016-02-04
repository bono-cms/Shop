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
     * Adds new order data
     * 
     * @param array $data
     * @return boolean
     */
    public function insert(array $data);

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
     * Approves an order by its associated id
     * 
     * @param string $id Order's id
     * @return boolean
     */
    public function approveById($id);
}