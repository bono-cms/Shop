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

interface OrderStatusMapperInterface
{
    /**
     * Fetches a row by its associated ID
     * 
     * @param string $id
     * @param boolean $withTranslations Whether to fetch translations or not
     * @return array
     */
    public function fetchById($id, $withTranslations);

    /**
     * Fetch all rows
     * 
     * @param boolean $sort Whether to sort rows
     * @return array
     */
    public function fetchAll($sort = false);
}
