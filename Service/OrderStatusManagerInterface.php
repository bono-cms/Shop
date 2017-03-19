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

interface OrderStatusManagerInterface
{
    /**
     * Returns last ID
     * 
     * @return string
     */
    public function getLastId();

    /**
     * Deletes a order status by its associated ID
     * 
     * @param string $id
     * @return boolean
     */
    public function deleteById($id);

    /**
     * Adds new order status entry
     * 
     * @param array $input
     * @return boolean
     */
    public function add(array $input);

    /**
     * Updates order status entry
     * 
     * @param array $input
     * @return boolean
     */
    public function update(array $input);

    /**
     * Fetch all entities
     * 
     * @return array
     */
    public function fetchAll();

    /**
     * Fetches order status entity by its associated ID
     * 
     * @param string $id
     * @return \Krystal\Stdlib\VirtualEntity|boolean
     */
    public function fetchById($id);
}
