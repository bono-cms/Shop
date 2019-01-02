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
     * Adds or updates new order status entry
     * 
     * @param array $input
     * @return boolean
     */
    public function save(array $input);

    /**
     * Fetch associative list
     * 
     * @return array
     */
    public function fetchList();

    /**
     * Fetch all entities
     * 
     * @param boolean $sort Whether to sort rows
     * @return array
     */
    public function fetchAll($sort = false);

    /**
     * Fetches order status entity by its associated ID
     * 
     * @param string $id
     * @param boolean $withTranslations Whether to fetch translations or not
     * @return \Krystal\Stdlib\VirtualEntity|boolean
     */
    public function fetchById($id, $withTranslations);
}
