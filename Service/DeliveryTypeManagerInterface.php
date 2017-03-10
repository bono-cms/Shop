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

interface DeliveryTypeManagerInterface
{
    /**
     * Updates the delivery type
     * 
     * @param array $input
     * @return boolean
     */
    public function update(array $input);

    /**
     * Adds new delivery type
     * 
     * @param array $input
     * @return boolean
     */
    public function add(array $input);

    /**
     * Returns last delivery type ID
     * 
     * @return string
     */
    public function getLastId();

    /**
     * Delete delivery type by its associated ID
     * 
     * @param string $id
     * @return boolean
     */
    public function deleteById($id);

    /**
     * Fetches entity by its associated ID
     * 
     * @param string $id
     * @return \Krystal\Stdlib\VirtualEntity
     */
    public function fetchById($id);

    /**
     * Fetch all entities
     * 
     * @return array
     */
    public function fetchAll();
}
