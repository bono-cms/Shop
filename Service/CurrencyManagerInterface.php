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

interface CurrencyManagerInterface
{
    /**
     * Returns last currency ID
     * 
     * @return string
     */
    public function getLastId();

    /**
     * Deletes a currency by its associated ID
     * 
     * @param string $id Currency ID
     * @return boolean
     */
    public function deleteById($id);

    /**
     * Adds a currency
     * 
     * @param array $input
     * @return boolean
     */
    public function add(array $input);

    /**
     * Updates a currency
     * 
     * @param array $input
     * @return boolean
     */
    public function update(array $input);

    /**
     * Fetch currency entity by its associated ID
     * 
     * @param string $id
     * @return array
     */
    public function fetchById($id);

    /**
     * Fetch all currency entities
     * 
     * @return array
     */
    public function fetchAll();
}
