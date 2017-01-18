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
     * @param string $groupId
     * @return array
     */
    public function fetchAllByCategoryId($groupId);

    /**
     * Deletes a value by its associated id
     * 
     * @param string $id
     * @return array
     */
    public function deleteById($id);

    /**
     * Fetches value by its associated id
     * 
     * @param string $id
     * @return array
     */
    public function fetchById($id);

    /**
     * Inserts a value
     * 
     * @param array $input
     * @return boolean
     */
    public function insert(array $input);

    /**
     * Updates a value
     * 
     * @param array $input
     * @return boolean
     */
    public function update(array $input);
}
