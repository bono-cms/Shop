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

use Krystal\Stdlib\VirtualEntity;
use Krystal\Tree\AdjacencyList\Render\AbstractRenderer;
use Shop\View\CategoryDropdown;
use Shop\Service\CurrencyManagerInterface;

final class SiteService implements SiteServiceInterface
{
    /**
     * Product manager service
     * 
     * @var \Shop\Service\ProductManagerInterface
     */
    private $productManager;

    /**
     * Category manager service
     * 
     * @var \Shop\Service\CategoryManagerInterface
     */
    private $categoryManager;

    /**
     * Currency service
     * 
     * @var \Shop\Service\CurrencyManagerInterface
     */
    private $currencyManager;

    /**
     * Configuration entity
     * 
     * @var \Krystal\Stdlib\VirtualEntity
     */
    private $config;

    /**
     * A service to deal with recent products
     * 
     * @var \Shop\Service\RecentProductInterface
     */
    private $recentProduct;

    /**
     * State initialization
     * 
     * @param \Shop\Service\ProductManagerInterface $productManager
     * @param \Shop\Service\CategoryManagerInterface $categoryManager
     * @param \Shop\Service\RecentProductInterface $recentProduct
     * @param \Shop\Service\CurrencyManagerInterface $currencyManager
     * @param \Krystal\Stdlib\VirtualEntity $config
     * @return void
     */
    public function __construct(
        ProductManagerInterface $productManager, 
        CategoryManagerInterface $categoryManager, 
        RecentProductInterface $recentProduct, 
        CurrencyManagerInterface $currencyManager, 
        VirtualEntity $config
    ){
        $this->productManager = $productManager;
        $this->categoryManager = $categoryManager;
        $this->recentProduct = $recentProduct;
        $this->currencyManager = $currencyManager;
        $this->config = $config;
    }

    /**
     * Returns currency source string
     * 
     * @param boolean $encode
     * @return string
     */
    public function getCurrencySourceString($encode = true)
    {
        $jsonString = json_encode($this->getCurrencies());

        if ($encode === true) {
            $jsonString = htmlspecialchars($jsonString);
        }

        return $jsonString;
    }

    /**
     * Returns a collection of available currencies
     * 
     * @return array
     */
    public function getCurrencies()
    {
        return $this->currencyManager->fetchList();
    }

    /**
     * Returns top categories (without children)
     * 
     * @return array
     */
    public function getTopCategories()
    {
        return $this->getCategoryChildrenByParentId('0', true);
    }

    /**
     * Returns category top children entities by its associated id
     * 
     * @param string $id Category id
     * @param boolean $top Whether to return by ID or parent ID
     * @return array
     */
    public function getCategoryChildrenByParentId($id, $top = true)
    {
        return $this->categoryManager->fetchChildrenByParentId($id, $top);
    }

    /**
     * Returns tree instance
     * 
     * @return \Krystal\Tree\AdjacencyList\Tree
     */
    public function getTree()
    {
        return $this->categoryManager->getTree();
    }

    /**
     * Renders category tree as array
     * 
     * @return array
     */
    public function renderCategoryTree()
    {
        return $this->categoryManager->getCategoriesTree();
    }

    /**
     * Renders category tree
     * 
     * @param array $options
     * @param \Krystal\Tree\AdjacencyList\Render\AbstractRenderer $walker
     * @return mixed
     */
    public function renderCategoryDropdown(array $options = array(), AbstractRenderer $walker = null)
    {
        if (is_null($walker)) {
            $walker = new CategoryDropdown($options);
        }

        return $this->categoryManager->renderTree($walker);
    }

    /**
     * Returns minimal product's price associated with provided category id
     * 
     * @param string $categoryId
     * @return float
     */
    public function getMinCategoryPriceCount($categoryId)
    {
        return $this->productManager->getMinCategoryPriceCount($categoryId);
    }

    /**
     * Returns an array of entities with products that have maximal view count
     * 
     * @param integer $limit
     * @param string $categoryId Optionally can be filtered by category id
     * @return array
     */
    public function getProductsWithMaxViewCount($limit, $categoryId = null)
    {
        return $this->productManager->fetchAllPublishedWithMaxViewCount($limit, $categoryId);
    }

    /**
     * Count all available stoke products
     * 
     * @return integer
     */
    public function countAllStokes()
    {
        return $this->productManager->countAllStokes();
    }

    /**
     * Return all stoke entities
     * 
     * @param string $limit Limit of records to be returned
     * @return array
     */
    public function getAllStokes($limit)
    {
        return $this->productManager->fetchAllStokes($limit);
    }

    /**
     * Returns best sale product entities
     * 
     * @return array
     */
    public function getBestSales()
    {
        return $this->productManager->fetchBestSales($this->config->getBestSellersApplyCount(), $this->config->getBestSellersLimit());
    }

    /**
     * Returns an array of entities of recent products
     * 
     * @param string $id Current product id to be excluded
     * @return array
     */
    public function getRecentProducts($id)
    {
        // Since the method is usually gets called twice, it would make sense to cache its calls
        static $result = null;

        if ($result === null) {
            if ($this->config->getMaxRecentAmount() > 0) {
                $result = $this->recentProduct->getWithRecent($id);
            } else {
                // If that functionality is disabled, then dummy empty array is used instead
                $result = array();
            }
        }

        return $result;
    }

    /**
     * Returns an array of latest product entities
     * 
     * @param integer $categoryId Optionally can be filtered by category id
     * @return array
     */
    public function getLatest($categoryId = null)
    {
        $count = $this->config->getShowCaseCount();
        return $this->productManager->fetchLatestPublished($count, $categoryId);
    }
}
