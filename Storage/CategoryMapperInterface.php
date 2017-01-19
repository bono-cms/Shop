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

interface CategoryMapperInterface
{
    /**
     * Finds category attributes by its associated id
     * 
     * @param string $id Category id
     * @return array
     */
    public function findAttributesById($id);

    /**
     * Fetches children by parent id
     * 
     * @param string $parentId
     * @return array
     */
    public function fetchChildrenByParentId($parentId);

    /**
     * Fetches breadcrumb's data
     * 
     * @param string $id Category id
     * @return array
     */
    public function fetchBcData();

    /**
     * Fetches category name by its associated id
     * 
     * @param string $id Category id
     * @return string
     */
    public function fetchNameById($id);

    /**
     * Fetches all categories
     * 
     * @return array
     */
    public function fetchAll();

    /**
     * Fetches category's data by its associated id
     * 
     * @param string $id Category's id
     * @return array
     */
    public function fetchById($id);

    /**
     * Adds a category
     * 
     * @param array $data
     * @return boolean
     */
    public function insert(array $data);

    /**
     * Updates a category
     * 
     * @param array $data
     * @return boolean
     */
    public function update(array $data);

    /**
     * Counts all available categories
     * 
     * @return integer
     */
    public function countAll();

    /**
     * Deletes a category by its associated id
     * 
     * @param string $id Category id
     * @return boolean
     */
    public function deleteById($id);

    /**
     * Deletes a category by its associated parent id
     * 
     * @param string $parentId Category parent id
     * @return boolean
     */
    public function deleteByParentId($parentId);
}
