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

interface CategoryManagerInterface
{
    /**
     * Returns a tree with prompt placeholder
     * 
     * @param string $text
     * @return array
     */
    public function getPromtWithCategoriesTree($text);

    /**
     * Fetches all categories as a tree
     * 
     * @return array
     */
    public function getCategoriesTree();

    /**
     * Fetches children by parent id
     * 
     * @param string $parentId
     * @return array
     */
    public function fetchChildrenByParentId($parentId);

    /**
     * Returns category's breadcrumbs
     * 
     * @param \Shop\Service\CategoryEntity $category
     * @return array
     */
    public function getBreadcrumbs(CategoryEntity $category);

    /**
     * Fetches all categories
     * 
     * @return array
     */
    public function fetchAll();

    /**
     * Counts all available categories
     * 
     * @return integer
     */
    public function countAll();

    /**
     * Returns last category's id
     * 
     * @return integer
     */
    public function getLastId();

    /**
     * Updates a category
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function update(array $input);

    /**
     * Adds a category
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function add(array $input);

    /**
     * Removes a category by its associated id
     * 
     * @param string $id Category id
     * @return boolean
     */
    public function deleteById($id);

    /**
     * Fetches category's entity by its associated id
     * 
     * @param string $id Category id
     * @return \Shop\Service\CategoryEntity|boolean
     */
    public function fetchById($id);
}