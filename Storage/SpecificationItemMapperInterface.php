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

interface SpecificationItemMapperInterface
{
    /**
     * Fetch all items
     * 
     * @return array
     */
    public function fetchAll();

    /**
     * Fetches item by its ID
     * 
     * @param int $id Item id
     * @param boolean $withTranslations Whether to fetch translations or not
     * @return array
     */
    public function fetchById($id, $withTranslations);
}
