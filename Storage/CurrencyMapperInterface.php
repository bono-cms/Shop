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

interface CurrencyMapperInterface
{
    /**
     * Deletes a currency by its ID
     * 
     * @param string $id
     * @return boolean
     */
    public function deleteById($id);

    /**
     * Fetches currency by its associated ID
     * 
     * @param string $id
     * @return array
     */
    public function fetchById($id);

    /**
     * Fetch all currencies
     * 
     * @return array
     */
    public function fetchAll();
}
