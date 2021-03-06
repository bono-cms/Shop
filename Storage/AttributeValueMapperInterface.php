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

interface AttributeValueMapperInterface
{
    /**
     * Fetch all values filtered by group id
     * 
     * @param int $groupId
     * @return array
     */
    public function fetchAllByCategoryId($groupId);

    /**
     * Fetches value by its associated id
     * 
     * @param string $id
     * @param boolean $withTranslations Whether to fetch translations or not
     * @return array
     */
    public function fetchById($id, $withTranslations);
}
