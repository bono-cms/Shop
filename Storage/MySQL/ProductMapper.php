<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Shop\Storage\MySQL;

use Cms\Storage\MySQL\AbstractMapper;
use Shop\Storage\ProductMapperInterface;
use Shop\Service\CategorySortGadget;
use Krystal\Db\Sql\RawSqlFragment;

final class ProductMapper extends AbstractMapper implements ProductMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_shop_products');
    }

    /**
     * {@inheritDoc}
     */
    public static function getJunctionTableName()
    {
        return self::getWithPrefix('bono_module_shop_product_category_relations');
    }

    /**
     * Returns shared select
     * 
     * @param boolean $published
     * @param string $categoryId Can be filtered by category id
     * @param string $order
     * @return \Krystal\Db\Sql\Db
     */
    private function getSelectQuery($published, $categoryId = null, $order = 'id', $desc = true)
    {
        $db = $this->db->select('*')
                       ->from(static::getTableName())
                       ->whereEquals('lang_id', $this->getLangId());

        if ($published === true) {
            $db->andWhereEquals('published', '1');
        }

        if ($categoryId !== null) {
            $db->andWhereEquals('category_id', $categoryId);
        }

        $db->orderBy($order);

        if ($desc == true) {
            $db->desc();
        }

        return $db;
    }

    /**
     * Queries for a result
     * 
     * @param integer $page Current page number
     * @param integer $itemsPerPage Per page count
     * @param boolean $published Whether to sort only published records
     * @param string $sort Column name to sort by
     * @param string $categoryId Optional category id
     * @return array
     */
    private function getResults($page, $itemsPerPage, $published, $categoryId = null, $order = 'id', $desc = true)
    {
        return $this->getSelectQuery($published, $categoryId, $order, $desc)
                    ->paginate($page, $itemsPerPage)
                    ->queryAll();
    }

    /**
     * Fetches best sale product ids
     * 
     * @param integer $qty Minimal quantity for a product to be considered as a best sale
     * @param integer $limit
     * @return array
     */
    public function fetchBestSales($qty, $limit)
    {
        return $this->db->select(array(sprintf('%s.%s', OrderProductMapper::getTableName(), 'product_id') => 'id'), true)
                        ->from(OrderProductMapper::getTableName())
                        ->innerJoin(OrderInfoMapper::getTableName())
                        ->whereEquals(sprintf('%s.%s', OrderInfoMapper::getTableName(), 'id'), new RawSqlFragment(sprintf('%s.%s', OrderProductMapper::getTableName(), 'order_id')))
                        ->andWhereEquals(sprintf('%s.%s', OrderInfoMapper::getTableName(), 'approved'), new RawSqlFragment("'1'"))
                        ->groupBy('id')
                        ->having('SUM', sprintf('%s.%s', OrderProductMapper::getTableName(), 'qty'), '>=', $qty)
                        ->limit($limit)
                        ->queryAll('id');
    }

    /**
     * Filters the raw input
     * 
     * @param array|\ArrayAccess $input Raw input data
     * @param integer $page Current page number
     * @param integer $itemsPerPage Items per page to be displayed
     * @param string $sortingColumn Column name to be sorted
     * @param string $desc Whether to sort in DESC order
     * @return array
     */
    public function filter($input, $page, $itemsPerPage, $sortingColumn, $desc)
    {
        if (!$sortingColumn) {
            $sortingColumn = 'id';
        }

        $db = $this->db->select('*')
                        ->from(static::getTableName())
                        ->whereLike('name', '%'.$input['name'].'%', true)
                        ->andWhereEquals('date', $input['date'], true)
                        ->andWhereEquals('id', $input['id'], true)
                        ->andWhereEquals('regular_price', $input['regular_price'], true)
                        ->andWhereEquals('category_id', $input['category_id'], true)
                        ->andWhereEquals('published', $input['published'], true)
                        ->andWhereEquals('seo', $input['seo'], true)
                        ->orderBy($sortingColumn);

        if ($desc) {
            $db->desc();
        }

        return $db->paginate($page, $itemsPerPage)
                  ->queryAll();
    }

    /**
     * Fetches all published products that have stoke price
     * 
     * @param integer $page Current page
     * @param integer $itemsPerPage Per page count
     * @return array
     */
    public function fetchAllPublishedStokesByPage($page, $itemsPerPage)
    {
        return $this->db->select('*')
                        ->from(static::getTableName())
                        ->whereEquals('lang_id', $this->getLangId())
                        ->andWhereEquals('published', '1')
                        ->andWhereNotEquals('stoke_price', '0')
                        ->orderBy('id')
                        ->desc()
                        ->paginate($page, $itemsPerPage)
                        ->queryAll();
    }

    /**
     * Returns minimal product's price associated with provided category id
     * It's aware only of published products
     * 
     * @param string $categoryId
     * @return string
     */
    public function getMinCategoryPriceCount($categoryId)
    {
        return $this->db->select()
                        ->min('regular_price', 'min_price')
                        ->from(static::getTableName())
                        ->whereEquals('published', '1')
                        ->andWhereEquals('category_id', $categoryId)
                        ->query('min_price');
    }

    /**
     * Fetches all published products with maximal view counts
     * 
     * @param integer $limit Fetching limit
     * @param string $categoryId Optionally can be filtered by category id
     * @return array
     */
    public function fetchAllPublishedWithMaxViewCount($limit, $categoryId = null)
    {
        $db = $this->db->select('*')
                       ->from(static::getTableName())
                       ->whereEquals('lang_id', $this->getLangId())
                       ->andWhereEquals('published', '1');

        if ($categoryId !== null) {
            $db->andWhereEquals('category_id', $categoryId);
        }

        return $db->andWhereGreaterThan('views', '0')
                  ->limit($limit)
                  ->queryAll();
    }

    /**
     * Fetches product's data by its associated id
     * 
     * @param string $id Product id
     * @param boolean $published Whether to fetch only published one
     * @param boolean $junction Whether to grab meta information about its categories
     * @return array
     */
    public function fetchById($id, $published = false, $junction = true)
    {
        $db = $this->db->select('*')
                      ->from(self::getTableName())
                      ->whereEquals('id', $id);

        if ($published === true) {
            $db->andWhereEquals('published', '1');
        }

        if ($junction === true) {
            $db->asManyToMany('categories', self::getJunctionTableName(), self::PARAM_JUNCTION_MASTER_COLUMN, CategoryMapper::getTableName(), 'id', array('id', 'name'));
        }

        return $db->query();
    }

    /**
     * Fetches basic product info by its associated id
     * 
     * @param string $id Product id
     * @return array
     */
    public function fetchBasicById($id)
    {
        $columns = array('name', 'regular_price', 'stoke_price', 'cover');

        return $this->db->select($columns)
                        ->from(self::getTableName())
                        ->whereEquals('id', $id)
                        ->query();
    }

    /**
     * Counts all available products
     * 
     * @return integer
     */
    public function countAll()
    {
        return (int) $this->db->select()
                              ->count('id', 'count')
                              ->from(self::getTableName())
                              ->query('count');
    }

    /**
     * Fetches all product ids associated with provided category id
     * 
     * @param string $categoryId
     * @return array
     */
    public function fetchProductIdsByCategoryId($categoryId)
    {
        return $this->fetchOneColumn('id', 'category_id', $categoryId);
    }

    /**
     * Fetches product name by its associated id
     * 
     * @param string $id Product id
     * @return string
     */
    public function fetchNameById($id)
    {
        return $this->findColumnByPk($id, 'name');
    }

    /**
     * Fetches latest published products
     * 
     * @param integer $limit
     * @return array
     */
    public function fetchLatestPublished($limit)
    {
        return $this->getSelectQuery(true)
                    ->limit($limit)
                    ->queryAll();
    }

    /**
     * Fetch latest products by associated category id
     * 
     * @param string $categoryId
     * @param integer $limit
     * @return array
     */
    public function fetchLatestByPublishedCategoryId($categoryId, $limit)
    {
        return $this->getSelectQuery(true, $categoryId)
                    ->limit($limit)
                    ->queryAll();
    }

    /**
     * Fetches all published products associated with category id and filtered by pagination
     * 
     * @param string $categoryId
     * @param integer $page Current page number
     * @param integer $itemsPerPage Per page count
     * @param string $sort Sorting type (its constant)
     * @return array
     */
    public function fetchAllPublishedByCategoryIdAndPage($categoryId, $page, $itemsPerPage, $sort)
    {
        $desc = false;

        switch ($sort) {

            case CategorySortGadget::SORT_ORDER:
                $order = 'order';
            break;

            case CategorySortGadget::SORT_TITLE:
                $order = 'title';
            break;

            case CategorySortGadget::SORT_PRICE_DESC:
                $order = 'regular_price';
                $desc = true;
            break;

            case CategorySortGadget::SORT_PRICE_ASC:
                $order = 'regular_price';
            break;

            case CategorySortGadget::SORT_DATE_DESC:
                $order = 'date';
                $desc = true;
            break;

            case CategorySortGadget::SORT_DATE_ASC:
                $order = 'date';
            break;

            default:
                // Invalid sorting constant's value provided. Probably a user attempted to do XSS
                // We'd simply return empty result
                return array();
        }

        $ids = $this->getMasterIdsFromJunction($categoryId);
        $result = array();

        foreach ($ids as $id) {
            $product = $this->fetchById($id, true, false);

            if ($product) {
                $result[] = $product;
            }
        }

        return $result;
    }

    /**
     * Fetches all product filtered by pagination
     * 
     * @param integer $page Current page
     * @param integer $itemsPerPage Per page count
     * @param string $categoryId Optional category id filter
     * @return array
     */
    public function fetchAllByPage($page, $itemsPerPage, $categoryId = null)
    {
        if ($categoryId === null) {
            return $this->db->select('*')
                         ->from(self::getTableName())
                         ->whereEquals('lang_id', $this->getLangId())
                         ->orderBy('id')
                         ->desc()
                         ->paginate($page, $itemsPerPage)
                         ->queryAll();
        } else {
            $ids = $this->getMasterIdsFromJunction($categoryId);
            $result = array();

            foreach ($ids as $id) {
                $result[] = $this->fetchById($id, false, false);
            }

            return $result;
        }
    }

    /**
     * Counts total amount of products associated with provided category id
     * 
     * @param string $categoryId
     * @return integer
     */
    public function countAllByCategoryId($categoryId)
    {
        //@TODO Optimize
        return count($this->getMasterIdsFromJunction($categoryId));
    }

    /**
     * Increments view count by product's id
     * 
     * @param string $id Product id
     * @return boolean
     */
    public function incrementViewCount($id)
    {
        return $this->incrementColumnByPk($id, 'views');
    }

    /**
     * Updates a price by associated id
     * 
     * @param string $id Product's id
     * @param string $price New price
     * @return boolean
     */
    public function updatePriceById($id, $price)
    {
        return $this->updateColumnByPk($id, 'regular_price', $price);
    }

    /**
     * Updates published state by associated product's id
     * 
     * @param string $id Product's id
     * @param string $published New state, either 0 or 1
     * @return boolean
     */
    public function updatePublishedById($id, $published)
    {
        return $this->updateColumnByPk($id, 'published', $published);
    }

    /**
     * Updates SEO state by associated product's id
     * 
     * @param integer $id Product id
     * @param string $published New state, either 0 or 1
     * @return boolean
     */
    public function updateSeoById($id, $seo)
    {
        return $this->updateColumnByPk($id, 'seo', $seo);
    }

    /**
     * Updates a product
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function update(array $input)
    {
        $this->syncWithJunction($input['id'], $input['category_id']);

        unset($input['category_id']);
        return $this->persist($input);
    }

    /**
     * Adds a product
     *  
     * @param array $input Raw input data
     * @return boolean Depending on success
     */
    public function insert(array $input)
    {
        $categories = $input['category_id'];
        unset($input['category_id']);

        $this->persist($this->getWithLang($input));

        return $this->insertIntoJunction($this->getLastId(), $categories);
    }

    /**
     * Deletes all products associated with provided category id
     * 
     * @param string $categoryId
     * @return boolean
     */
    public function deleteByCategoryId($categoryId)
    {
        return $this->deleteByColumn('category_id', $categoryId);
    }

    /** 
     * Deletes a product by its associated id
     * 
     * @param string $id
     * @return boolean
     */
    public function deleteById($id)
    {
        return $this->deleteByPk($id) && $this->removeFromJunction($id);
    }
}
