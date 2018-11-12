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

interface ProductMapperInterface
{
    /**
     * Fetches all product ids with their corresponding names
     *
     * @return array
     */
    public function fetchAllNames();

    /**
     * Fetches best sale product ids
     * 
     * @param integer $qty Minimal quantity for a product to be considered as a best sale
     * @param integer $limit
     * @return array
     */
    public function fetchBestSales($qty, $limit);

    /**
     * Find products by attributes and associated category id
     * 
     * @param string $categoryId Category id
     * @param mixed $customerId Optional customer ID
     * @param array $attributes A collection of group IDs and their value IDs
     * @param string $sort Sorting column
     * @param string $page Optional page number
     * @param string $itemsPerPage Optional Per page count filter
     * @return array
     */
    public function findByAttributes($categoryId, $customerId, array $attributes, $sort, $page = null, $itemsPerPage = null);

    /**
     * Count all available stoke products
     * 
     * @return integer
     */
    public function countAllStokes();

    /**
     * Fetch all stokes
     * 
     * @param integer $limit
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
    public function fetchAllPublishedStokesByPage($page, $itemsPerPage, $customerId);

    /**
     * Fetches product name by its associated id
     * 
     * @param string $id Product id
     * @return string
     */
    public function fetchNameById($id);

    /**
     * Fetches latest published products
     * 
     * @param integer $limit
     * @param integer $categoryId Optional category id
     * @return array
     */
    public function fetchLatestPublished($limit, $categoryId = null);

    /**
     * Fetches all published products associated with category id and filtered by pagination
     * 
     * @param string $categoryId
     * @param integer $page Current page number
     * @param integer $itemsPerPage Per page count
     * @param string $sort Sorting type (its constant)
     * @param string $keyword Optional search keyword
     * @param integer $customerId Optional customer ID
     * @return array
     */
    public function fetchAllPublishedByCategoryIdAndPage($categoryId, $page, $itemsPerPage, $sort, $keyword, $customerId);

    /**
     * Fetches all product filtered by pagination
     * 
     * @param integer $page Current page
     * @param integer $itemsPerPage Per page count
     * @param string $categoryId Optional category id filter
     * @return array
     */
    public function fetchAllByPage($page, $itemsPerPage, $categoryId);

    /**
     * Counts total amount of products associated with provided category id
     * 
     * @param string $categoryId
     * @return integer
     */
    public function countAllByCategoryId($categoryId);

    /**
     * Increments view count by product's id
     * 
     * @param string $id Product id
     * @return boolean
     */
    public function incrementViewCount($id);

    /**
     * Update settings
     * 
     * @param array $settings
     * @return boolean
     */
    public function updateSettings(array $settings);

    /**
     * Updates a product
     * 
     * @param array $data
     * @return boolean
     */
    public function update(array $data);

    /**
     * Adds a product
     *  
     * @param array $data
     * @return boolean Depending on success
     */
    public function insert(array $data);

    /**
     * Returns minimal product's price associated with provided category id
     * It's aware only of published products
     * 
     * @param string $categoryId
     * @return string
     */
    public function getMinCategoryPriceCount($categoryId);

    /**
     * Fetches all published products with maximal view counts
     * 
     * @param integer $limit Fetching limit
     * @param string $categoryId Optionally can be filtered by category id
     * @return array
     */
    public function fetchAllPublishedWithMaxViewCount($limit, $categoryId = null);

    /**
     * Fetch published products by a collection of their associated IDs
     * 
     * @param array $ids A collection of product IDs
     * @param string $customerId Optional customer ID
     * @return array
     */
    public function fetchByIds(array $ids, $customerId = null);

    /**
     * Fetches product's data by its associated id
     * 
     * @param string $id Product id
     * @param boolean $junction Whether to grab meta information about its categories
     * @param integer $customerId Optional customer ID
     * @return array
     */
    public function fetchById($id, $junction = true, $customerId = null);

    /**
     * Fetches basic product info by its associated id
     * 
     * @param string $id Product id
     * @return array
     */
    public function fetchBasicById($id);

    /**
     * Counts all available products
     * 
     * @return integer
     */
    public function countAll();

    /** 
     * Deletes a product by its associated id
     * 
     * @param string $id
     * @return boolean
     */
    public function deleteById($id);
}
