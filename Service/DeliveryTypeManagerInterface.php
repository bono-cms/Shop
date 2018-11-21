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
     * Creates delivery status (name +price)
     * 
     * @param string $id Delivery ID
     * @return string
     */
    public function createDeliveryStatus($id);

    /**
     * Fetches delivery type name by its associated ID
     * 
     * @param string $id
     * @return string
     */
    public function fetchNameById($id);


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
     * @param boolean $withTranslations Whether to fetch translations or not
     * @return \Krystal\Stdlib\VirtualEntity
     */
    public function fetchById($id, $withTranslations);

    /**
     * Fetch all entities
     * 
     * @return array
     */
    public function fetchAll();
}
