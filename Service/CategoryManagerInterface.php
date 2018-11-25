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

use Krystal\Tree\AdjacencyList\Render\AbstractRenderer;

interface CategoryManagerInterface
{
    /**
     * Returns a collection of switching URLs
     * 
     * @param string $id Category ID
     * @return array
     */
    public function getSwitchUrls($id);

    /**
     * Returns a tree with prompt placeholder
     * 
     * @param string $text
     * @return array
     */
    public function getPromtWithCategoriesTree($text);

    /**
     * Creates Tree builder instance
     * 
     * @param \Krystal\Tree\AdjacencyList\Render\AbstractRenderer $walker
     * @return string
     */
    public function renderTree(AbstractRenderer $walker);

    /**
     * Returns tree instance
     * 
     * @return \Krystal\Tree\AdjacencyList\Tree
     */
    public function getTree();

    /**
     * Fetches all categories as a tree
     * 
     * @param boolean $extended Whether to return extended tree or not
     * @return array
     */
    public function getCategoriesTree($extended = false);

    /**
     * Fetches child rows by associated parent id
     * 
     * @param string $parentId
     * @param boolean $top Whether to return by ID or parent ID
     * @return array
     */
    public function fetchChildrenByParentId($parentId, $top = true);

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
     * @param boolean $withTranslations Whether to fetch translations or not
     * @return \Shop\Service\CategoryEntity|boolean
     */
    public function fetchById($id, $withTranslations);
}
