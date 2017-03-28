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
use Krystal\Stdlib\ArrayUtils;
use Krystal\Db\Sql\QueryBuilderInterface;

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
     * Returns table name for similar products (junction table)
     * 
     * @return string
     */
    public static function getSimilarTableName()
    {
        return self::getWithPrefix('bono_module_shop_product_similar');
    }

    /**
     * Returns table name for recommended products (junction table)
     * 
     * @return string
     */
    public static function getRecommendedTableName()
    {
        return self::getWithPrefix('bono_module_shop_product_recommended');
    }

    /**
     * Fetches all product ids with their corresponding names
     *
     * @return array
     */
    public function fetchAllNames()
    {
        return $this->db->select(array('id', 'name'))
                        ->from(self::getTableName())
                        ->orderBy('name')
                        ->queryAll();
    }

    /**
     * Appends category filter on junction table
     * 
     * @param string $categoryId
     * @return void
     */
    private function appendJunctionCategory($categoryId)
    {
        $this->db->andWhereEquals(sprintf('%s.%s', self::getJunctionTableName(), self::PARAM_JUNCTION_SLAVE_COLUMN), $categoryId)
                 ->andWhereEquals(sprintf('%s.%s', self::getTableName(), 'id'), new RawSqlFragment(self::PARAM_JUNCTION_MASTER_COLUMN));
    }

    /**
     * Shared query set fetcher all product filtered by pagination
     * 
     * @param integer $page Current page
     * @param integer $itemsPerPage Per page count
     * @param boolean $published Whether to filter by "published" attribute
     * @param string $categoryId Optional category id filter
     * @param string $order Sort order
     * @param boolean $boolean Whether to sort by DESC
     * @return array
     */
    private function querySet($page, $itemsPerPage, $published, $categoryId, $order, $desc)
    {
        $db = $this->db->select('*')
                       ->from(self::getTableName());

        if ($categoryId !== null) {
            $this->db->innerJoin(self::getJunctionTableName());
        }

        $db->whereEquals(sprintf('%s.%s', self::getTableName(), 'lang_id'), $this->getLangId());

        if ($published === true) {
            $db->andWhereEquals(sprintf('%s.%s', self::getTableName(), 'published'), '1');
        }

        if ($categoryId !== null) {
            $this->appendJunctionCategory($categoryId);
        }

        $db->orderBy(sprintf('%s.%s', self::getTableName(), $order));

        if ($desc === true) {
            $db->desc();
        }

        return $db->paginate($page, $itemsPerPage)
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
     * Returns shared columns to be selected
     * 
     * @param mixed $customerId
     * @return array
     */
    private function getSharedColumns($customerId)
    {
        // Shared columns to be selected
        $columns = array(
            ProductMapper::getFullColumnName('id'),
            ProductMapper::getFullColumnName('name'),
            ProductMapper::getFullColumnName('lang_id'),
            ProductMapper::getFullColumnName('web_page_id'),
            ProductMapper::getFullColumnName('title'),
            ProductMapper::getFullColumnName('regular_price'),
            ProductMapper::getFullColumnName('stoke_price'),
            ProductMapper::getFullColumnName('special_offer'),
            ProductMapper::getFullColumnName('description'),
            ProductMapper::getFullColumnName('published'),
            ProductMapper::getFullColumnName('order'),
            ProductMapper::getFullColumnName('seo'),
            ProductMapper::getFullColumnName('keywords'),
            ProductMapper::getFullColumnName('meta_description'),
            ProductMapper::getFullColumnName('cover'),
            ProductMapper::getFullColumnName('date'),
            ProductMapper::getFullColumnName('views'),
            ProductMapper::getFullColumnName('in_stock')
        );

        if ($customerId != null) {
            // Columns to be selected
            $columns = array_merge($columns, array(WishlistMapper::getFullColumnName('product_id') => 'product_wishlist_id'));
        }

        return $columns;
    }

    /**
     * Create attribute match queries
     * 
     * @param array $pair
     * @param string $sort Sorting column
     * @return string
     */
    private function createAttributeMatchQueries(array $pair, $categoryId, $sort)
    {
        $qb = $this->db->getQueryBuilder();

        // Amount of registered mappers we have
        $amount = count($pair);

        // Iteration counter
        $i = 0;

        foreach ($pair as $groupId => $valueId) {
            $this->appendAttributeMatchQuery($qb, $categoryId, $groupId, $valueId, $sort);

            ++$i;

            // Comparing iteration against number of mappers, tells whether this iteration is last
            $last = $i == $amount;

            // If we have more that one mapper, then we need to union results
            // And also, we should never append UNION in last iteration
            if ($amount > 1 && !$last) {
                $qb->union();
            }
        }

        return $qb->getQueryString();
    }

    /**
     * Appends attribute match query
     * 
     * @param \Krystal\Db\Sql\QueryBuilderInterface
     * @param string $categoryId
     * @param string $groupId
     * @param string $valueId
     * @param string $sort Sorting column
     * @return void
     */
    private function appendAttributeMatchQuery(QueryBuilderInterface $qb, $categoryId, $groupId, $valueId, $sort)
    {
        // Create sorting rules
        $sortingRules = CategorySortGadget::createSortingRules($sort);

        $qb->openBracket();

        $qb->select($this->getSharedColumns(), true)
           ->from(ProductAttributeMapper::getTableName())
           ->leftJoin(self::getTableName())
           ->on()
           ->equals(self::getFullColumnName('id'), ProductAttributeMapper::getFullColumnName('product_id'))

           // Filter by category ID
           ->innerJoin(self::getJunctionTableName())
           ->on()
           ->equals(sprintf('%s.master_id', self::getJunctionTableName()), self::getFullColumnName('id'))
           ->rawAnd()
           ->equals(sprintf('%s.slave_id', self::getJunctionTableName()), (int) $categoryId)

           // Filter by group and value IDs
           ->whereEquals('group_id', (int) $groupId)
           ->andWhereEquals('value_id', (int) $valueId)
           ->orderBy(ProductMapper::getFullColumnName($sortingRules['column']));

        if ($sortingRules['desc'] === true) {
            $qb->desc();
        }

        $qb->closeBracket();
    }

    /**
     * Append customer relation
     * 
     * @param integer $customerId
     * @return void
     */
    private function appendCustomerRelation($customerId)
    {
        // Wish list relation
        $this->db->leftJoin(WishlistMapper::getTableName())
                 ->on()
                 ->equals(WishlistMapper::getFullColumnName('product_id'), new RawSqlFragment(self::getFullColumnName('id')))
                 ->rawAnd()
                 ->equals(WishlistMapper::getFullColumnName('customer_id'), $customerId);
    }

    /**
     * Find products by attributes and associated category id
     * 
     * @param string $categoryId Category id
     * @param array $attributes A collection of group IDs and their value IDs
     * @param string $sort Sorting column
     * @param string $page Optional page number
     * @param string $itemsPerPage Optional Per page count filter
     * @return array
     */
    public function findByAttributes($categoryId, array $attributes, $sort, $page = null, $itemsPerPage = null)
    {
        $query = $this->createAttributeMatchQueries($attributes, $categoryId, $sort);

        return $this->db->raw($query)->queryAll();
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
                       ->from(static::getTableName());

        if (!empty($input['category_id'])) {
            $this->db->innerJoin(self::getJunctionTableName());
        }

        $db->whereEquals('1', '1');

        $db->andWhereLike('name', '%'.$input['name'].'%', true)
           ->andWhereEquals('date', $input['date'], true)
           ->andWhereEquals('id', $input['id'], true)
           ->andWhereEquals('regular_price', $input['regular_price'], true)
           ->andWhereEquals('published', $input['published'], true)
           ->andWhereEquals('seo', $input['seo'], true);

        if (!empty($input['category_id'])) {
            $this->appendJunctionCategory($input['category_id']);
        }

        $db->orderBy($sortingColumn);

        if ($desc) {
            $db->desc();
        }

        return $db->paginate($page, $itemsPerPage)
                  ->queryAll();
    }

    /**
     * Count all available stoke products
     * 
     * @return integer
     */
    public function countAllStokes()
    {
        return $this->db->select()
                        ->count($this->getPk(), 'count')
                        ->from(self::getTableName())
                        ->whereEquals('lang_id', $this->getLangId())
                        ->andWhereEquals('published', '1')
                        ->andWhereNotEquals('stoke_price', '0')
                        ->query('count');
    }

    /**
     * Fetch all stokes
     * 
     * @param integer $limit Limit of records to be returned
     * @return array
     */
    public function fetchAllStokes($limit)
    {
        return $this->db->select('*')
                        ->from(self::getTableName())
                        ->whereEquals('lang_id', $this->getLangId())
                        ->andWhereEquals('published', '1')
                        ->andWhereNotEquals('stoke_price', '0')
                        ->orderBy($this->getPk())
                        ->desc()
                        ->limit($limit)
                        ->queryAll();
    }

    /**
     * Fetches all published products that have stoke price
     * 
     * @param integer $page Current page
     * @param integer $itemsPerPage Per page count
     * @param mixed $customerId Optional customer ID
     * @return array
     */
    public function fetchAllPublishedStokesByPage($page, $itemsPerPage, $customerId)
    {
        $db = $this->db->select($this->getSharedColumns($customerId))
                       ->from(self::getTableName());

        if ($customerId != null) {
            $this->appendCustomerRelation($customerId);
        }

        return $db->whereEquals('lang_id', $this->getLangId())
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
        $db = $this->db->select()
                        ->min('regular_price', 'min_price')
                        ->from(self::getTableName())
                        ->innerJoin(self::getJunctionTableName())
                        ->whereEquals('published', '1');

        $this->appendJunctionCategory($categoryId);
        return $db->query('min_price');
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
                       ->from(self::getTableName());

        if ($categoryId !== null) {
            $this->db->innerJoin(self::getJunctionTableName());
        }

        $db->whereEquals('lang_id', $this->getLangId())
           ->andWhereEquals('published', '1')
           ->andWhereGreaterThan('views', '0');

        if ($categoryId !== null) {
            $this->appendJunctionCategory($categoryId);
        }

        return $db->limit($limit)
                  ->queryAll();
    }

    /**
     * Fetches product's data by its associated id
     * 
     * @param string $id Product id
     * @param boolean $junction Whether to grab meta information about its categories
     * @param integer $customerId Optional customer ID
     * @return array
     */
    public function fetchById($id, $junction = true, $customerId = null)
    {
        $db = $this->db->select($this->getSharedColumns($customerId))
                       ->from(self::getTableName());

        if ($customerId != null) {
            $this->appendCustomerRelation($customerId);
        }

        $db->whereEquals('id', $id)
           ->andWhereEquals('published', '1');

        if ($junction === true) {
            $columns = array('id', 'name');

            $db->asManyToMany('categories', self::getJunctionTableName(), self::PARAM_JUNCTION_MASTER_COLUMN, CategoryMapper::getTableName(), 'id', $columns);
            $db->asManyToMany('similar', self::getSimilarTableName(), self::PARAM_JUNCTION_MASTER_COLUMN, self::getTableName(), 'id', $columns);
            $db->asManyToMany('recommended', self::getRecommendedTableName(), self::PARAM_JUNCTION_MASTER_COLUMN, self::getTableName(), 'id', $columns);
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
        $columns = array('name', 'regular_price', 'stoke_price', 'in_stock', 'cover');

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
        // To be selected
        $column = 'id';

        return $this->db->select($column)
                        ->from(self::getTableName())
                        ->innerJoin(self::getJunctionTableName())
                        ->whereEquals('lang_id', $this->getLangId())
                        ->appendJunctionCategory($categoryId)
                        ->query($column);
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
     * @param integer $categoryId Optional category id
     * @return array
     */
    public function fetchLatestPublished($limit, $categoryId = null)
    {
        $db = $this->db->select('*')
                       ->from(static::getTableName());

        if ($categoryId !== null) {
            $this->db->innerJoin(self::getJunctionTableName());
        }

        $db->whereEquals('lang_id', $this->getLangId())
           ->andWhereEquals('published', '1');

        if ($categoryId !== null) {
            $this->appendJunctionCategory($categoryId);
        }

        return $db->orderBy('id')
                  ->desc()
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
     * @param string $keyword Optional search keyword
     * @param integer $customerId Optional customer ID
     * @return array
     */
    public function fetchAllPublishedByCategoryIdAndPage($categoryId, $page, $itemsPerPage, $sort, $keyword, $customerId)
    {
        // Grab shared columns to be selected
        $columns = $this->getSharedColumns($customerId);

        $sortingRules = CategorySortGadget::createSortingRules($sort);

        $db = $this->db->select($columns)
                       ->from(self::getTableName());

        if ($customerId != null) {
            $this->appendCustomerRelation($customerId);
        }

        if ($keyword === null) {
            $db->innerJoin(self::getJunctionTableName());
        }

        $db->whereEquals(sprintf('%s.%s', self::getTableName(), 'lang_id'), $this->getLangId())
           ->andWhereEquals(sprintf('%s.%s', self::getTableName(), 'published'), '1');

        if ($keyword === null) {
            $db->andWhereEquals(sprintf('%s.%s', self::getJunctionTableName(), self::PARAM_JUNCTION_SLAVE_COLUMN), $categoryId)
               ->andWhereEquals(sprintf('%s.%s', self::getTableName(), 'id'), new RawSqlFragment(self::PARAM_JUNCTION_MASTER_COLUMN));
        }

        if ($keyword !== null) {
            $db->andWhereLike('name', '%'.$keyword.'%');
        }

        $db->orderBy(sprintf('%s.%s', self::getTableName(), $sortingRules['column']));

        if ($sortingRules['desc'] === true) {
            $db->desc();
        }

        return $db->paginate($page, $itemsPerPage)
                  ->queryAll();
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
        return $this->querySet($page, $itemsPerPage, false, $categoryId, 'id', true);
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
        return count($this->getMasterIdsFromJunction(self::getJunctionTableName(), $categoryId));
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
        // Synchronize relations
        $this->syncWithJunction(self::getJunctionTableName(), $input['id'], $input['category_id']);

        // Update into reccomended junction table
        if (isset($input['recommended_ids'])) {
            $this->syncWithJunction(self::getRecommendedTableName(), $input['id'], $input['recommended_ids']);
        }

        // Update into similar junction table
        if (isset($input['similar_ids'])) {
            $this->syncWithJunction(self::getSimilarTableName(), $input['id'], $input['similar_ids']);
        }

        return $this->persist(ArrayUtils::arrayWithout($input, array('category_id', 'recommended_ids', 'similar_ids')));
    }

    /**
     * Adds a product
     *  
     * @param array $input Raw input data
     * @return boolean Depending on success
     */
    public function insert(array $input)
    {
        // Save the product data first
        $this->persist($this->getWithLang(ArrayUtils::arrayWithout($input, array('category_id', 'recommended_ids', 'similar_ids'))));
        $id = $this->getLastId(); // Last product ID

        // Insert into categories table
        $this->insertIntoJunction(self::getJunctionTableName(), $id, $input['category_id']);

        // Insert into reccomended junction table
        if (isset($input['recommended_ids'])) {
            $this->insertIntoJunction(self::getRecommendedTableName(), $id, $input['recommended_ids']);
        }

        // Insert into similar junction table
        if (isset($input['similar_ids'])) {
            $this->insertIntoJunction(self::getSimilarTableName(), $id, $input['similar_ids']);
        }

        return true;
    }

    /** 
     * Deletes a product by its associated id
     * 
     * @param string $id
     * @return boolean
     */
    public function deleteById($id)
    {
        $this->deleteByPk($id);

        $this->removeFromJunction(self::getJunctionTableName(), $id);
        $this->removeFromJunction(self::getRecommendedTableName(), $id);
        $this->removeFromJunction(self::getSimilarTableName(), $id);

        return true;
    }
}
