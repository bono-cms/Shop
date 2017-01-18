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

interface AttributeGroupMapperInterface
{
    /**
     * Fetch all groups
     * 
     * @return array
     */
    public function fetchAll();

    /**
     * Deletes a group by its associated id
     * 
     * @param string $id
     * @return array
     */
    public function deleteById($id);

    /**
     * Fetches category by its associated id
     * 
     * @param string $id
     * @return array
     */
    public function fetchById($id);

    /**
     * Inserts a group
     * 
     * @param array $input
     * @return boolean
     */
    public function insert(array $input);

    /**
     * Updates a group
     * 
     * @param array $input
     * @return boolean
     */
    public function update(array $input);
}
