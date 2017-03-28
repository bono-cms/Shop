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

interface ProductManagerInterface
{
    /**
     * Fetches all product ids with their corresponding names
     * 
     * @return array
     */
    public function fetchAllNames();

    /**
     * Find products by attributes and associated category id
     * 
     * @param string $categoryId Category id
     * @param mixed $customerId Optional customer ID
     * @param array $attributes A collection of group IDs and their value IDs
     * @param string|boolean $sort Sorting column
     * @param string $page Optional page number
     * @param string $itemsPerPage Optional Per page count filter
     * @return array
     */
    public function findByAttributes($categoryId, $customerId = null, array $attributes, $sort = null, $page = null, $itemsPerPage = null);

    /**
     * Fetches best sales
     * 
     * @param integer $qty Minimal quantity for a product to be considered as a best sale
     * @param integer $limit
     * @return array
     */
    public function fetchBestSales($qty, $limit);

    /**
     * Count all available stoke products
     * 
     * @return integer
     */
    public function countAllStokes();

    /**
     * Fetch all stoke entities
     * 
     * @param integer $limit Limit of records to be returned
     * @return array
     */
    public function fetchAllStokes($limit);

    /**
     * Fetches all published products that have stoke price
     * 
     * @param integer $page Current page
     * @param integer $itemsPerPage Per page count
     * @param mixed $customerId Optional customer ID
     * @return array
     */
    public function fetchAllPublishedStokesByPage($page, $itemsPerPage, $customerId = null);

    /**
     * Returns product's breadcrumbs collection
     * 
     * @param \Shop\Service\ProductEntity $product
     * @return array
     */
    public function getBreadcrumbs(ProductEntity $product);

    /**
     * Fetches all product's photo entities by its associated id
     * 
     * @param string $id Product id
     * @return array
     */
    public function fetchAllImagesById($id);

    /**
     * Fetches all published product's photo entities by its associated id
     * 
     * @param string $id Product id
     * @return array
     */
    public function fetchAllPublishedImagesById($id);

    /**
     * Increments view count by product's id
     * 
     * @param string $id Product id
     * @return boolean
     */
    public function incrementViewCount($id);

    /**
     * Updates prices by their associated ids and values
     * 
     * @param array $pair
     * @return boolean
     */
    public function updatePrices(array $pair);

    /**
     * Updates published state by their associated ids
     * 
     * @param array $pair
     * @return boolean
     */
    public function updatePublished(array $pair);

    /**
     * Update SEO state by their associated ids
     * 
     * @param array $pair
     * @return boolean
     */
    public function updateSeo(array $pair);

    /**
     * Removes products by their associated ids
     * 
     * @param array $ids
     * @return boolean
     */
    public function deleteByIds(array $ids);

    /**
     * Deletes a product by its associated id
     * 
     * @param string $id Product's id
     * @return boolean
     */
    public function deleteById($id);

    /**
     * Returns prepared paginator's instance
     * 
     * @return \Krystal\Paginate\Paginator
     */
    public function getPaginator();

    /**
     * Returns last product's id
     * 
     * @return integer
     */
    public function getLastId();

    /**
     * Adds a product
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function add(array $input);

    /**
     * Updates a product
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function update(array $input);

    /**
     * Fetches all published product entities with maximal view counts
     * 
     * @param integer $limit Fetching limit
     * @param string $categoryId Optionally can be filtered by category id
     * @return array
     */
    public function fetchAllPublishedWithMaxViewCount($limit, $categoryId = null);

    /**
     * Returns minimal product's price associated with provided category id
     * It's aware only of published products
     * 
     * @param string $categoryId
     * @return float
     */
    public function getMinCategoryPriceCount($categoryId);

    /**
     * Fetches all published product entities associated with given category id
     * 
     * @param string $categoryId
     * @param integer $page Current page
     * @param integer $itemsPerPage Per page count
     * @param string $sort Sorting constant
     * @param string $keyword Optional keyword filter
     * @param integer $customerId Optional customer ID
     * @return array
     */
    public function fetchAllPublishedByCategoryIdAndPage($categoryId, $page, $itemsPerPage, $sort, $keyword = null, $customerId = null);

    /**
     * Fetches all product entities filtered by pagination
     * 
     * @param integer $page
     * @param integer $itemsPerPage Per page count
     * @param string $categoryId Optional category id filter
     * @return array
     */
    public function fetchAllByPage($page, $itemsPerPage, $categoryId);

    /**
     * Fetches all published product entities associated with given category id
     * 
     * @param string $categoryId
     * @return array
     */
    public function fetchAllPublishedByCategoryId($categoryId);

    /**
     * Fetches product's entity by its associated id with its associated attachments
     * 
     * @param string $id
     * @param mixed $customerId Optional customer ID
     * @return \Krystal\Stdlib\VirtualEntity|boolean
     */
    public function fetchFullById($id, $customerId = null);

    /**
     * Fetches product's entity by its associated id
     * 
     * @param string $id
     * @return \Krystal\Stdlib\VirtualEntity|boolean
     */
    public function fetchById($id);

    /**
     * Fetches basic product info by its associated id
     * 
     * @param string $id Product id
     * @return \Shop\Service\ProductEntity|boolean
     */
    public function fetchBasicById($id);

    /**
     * Counts all available products
     * 
     * @return integer
     */
    public function countAll();

    /**
     * Fetches latest product entities
     * 
     * @param integer $limit Limit for fetching
     * @param integer $categoryId Optionally can be filtered by category id
     * @return array
     */
    public function fetchLatestPublished($limit, $categoryId = null);
}
