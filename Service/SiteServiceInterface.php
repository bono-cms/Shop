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

interface SiteServiceInterface
{
    /**
     * Returns wishlist count
     * 
     * @param integer $customerId
     * @return integer
     */
    public function getWishlistCount($customerId);

    /**
     * Returns currency source string
     * 
     * @return string
     */
    public function getCurrencySourceString();

    /**
     * Returns a collection of available currencies
     * 
     * @return array
     */
    public function getCurrencies();

    /**
     * Returns top categories (without children)
     * 
     * @return array
     */
    public function getTopCategories();

    /**
     * Returns category top children entities by its associated id
     * 
     * @param string $id Category id
     * @param boolean $top Whether to return by ID or parent ID
     * @return array
     */
    public function getCategoryChildrenByParentId($id, $top = true);

    /**
     * Returns tree instance
     * 
     * @return \Krystal\Tree\AdjacencyList\Tree
     */
    public function getTree();

    /**
     * Renders category tree as array
     * 
     * @return array
     */
    public function renderCategoryTree();

    /**
     * Renders category tree
     * 
     * @param array $options
     * @param \Krystal\Tree\AdjacencyList\Render\AbstractRenderer $walker
     * @return mixed
     */
    public function renderCategoryDropdown(array $options = array(), AbstractRenderer $walker = null);

    /**
     * Returns minimal product's price associated with provided category id
     * 
     * @param string $categoryId
     * @return float
     */
    public function getMinCategoryPriceCount($categoryId);

    /**
     * Returns an array of entities with products that have maximal view count
     * 
     * @param integer $limit
     * @param string $categoryId Optionally can be filtered by category id
     * @return array
     */
    public function getProductsWithMaxViewCount($limit, $categoryId = null);

    /**
     * Count all available stoke products
     * 
     * @return integer
     */
    public function countAllStokes();

    /**
     * Return all stoke entities
     * 
     * @param string $limit Limit of records to be returned
     * @return array
     */
    public function getAllStokes($limit);

    /**
     * Returns best sale product entities
     * 
     * @return array
     */
    public function getBestSales();

    /**
     * Returns an array of entities of recent products
     * 
     * @param string $id Current product id to be excluded
     * @return array
     */
    public function getRecentProducts($id);

    /**
     * Returns an array of latest product entities
     * 
     * @param integer $categoryId Optionally can be filtered by category id
     * @return array
     */
    public function getLatest($categoryId = null);
}
