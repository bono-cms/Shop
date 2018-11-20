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

use Cms\Storage\MySQL\WebPageMapper;
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
    public static function getTranslationTable()
    {
        return ProductTranslationMapper::getTableName();
    }

    /**
     * Returns shared columns to be selected
     * 
     * @param mixed $customerId
     * @param boolean $extraColumns Whether to selected extra columns or not
     * @return array
     */
    public static function getSharedColumns($customerId = null, $extraColumns = true)
    {
        // Basic columns to be selected (required for most selections)
        $columns = array(
            ProductMapper::column('id'),
            ProductMapper::column('brand_id'),
            ProductTranslationMapper::column('lang_id'),
            ProductTranslationMapper::column('web_page_id'),
            ProductTranslationMapper::column('name'),
            ProductMapper::column('regular_price'),
            ProductMapper::column('stoke_price'),
            ProductMapper::column('in_stock'),
            ProductMapper::column('special_offer'),
            ProductMapper::column('cover'),
            WebPageMapper::column('slug'),
        );

        // Do extra columns need to be appended?
        if ($extraColumns === true) {
            $columns = array_merge($columns, array(
                ProductTranslationMapper::column('title'),
                ProductTranslationMapper::column('description'),
                ProductMapper::column('published'),
                ProductMapper::column('order'),
                ProductMapper::column('seo'),
                ProductTranslationMapper::column('keywords'),
                ProductTranslationMapper::column('meta_description'),
                ProductMapper::column('date'),
                ProductMapper::column('views'),
            ));
        }

        if ($customerId != null) {
            // Columns to be selected
            $columns = array_merge($columns, array(
                WishlistMapper::column('product_id') => 'product_wishlist_id'
            ));
        }

        return $columns;
    }

    /**
     * Fetches all product ids with their corresponding names
     *
     * @return array
     */
    public function fetchAllNames()
    {
        $db = $this->db->select(array('id', 'name'))
                        ->from(ProductTranslationMapper::getTableName())
                        // Language constraint
                        ->whereEquals(ProductTranslationMapper::column('lang_id'), $this->getLangId())
                        ->orderBy('name');

        return $db->queryAll();
    }

    /**
     * Appends category filter on junction table
     * 
     * @param string $categoryId
     * @return void
     */
    private function appendJunctionCategory($categoryId)
    {
        $this->db->andWhereEquals(ProductCategoryRelationMapper::column(self::PARAM_JUNCTION_SLAVE_COLUMN), $categoryId)
                 ->andWhereEquals(self::column('id'), new RawSqlFragment(self::PARAM_JUNCTION_MASTER_COLUMN));
    }

    /**
     * Shared query set fetcher all product filtered by pagination
     * 
     * @param integer $page Current page
     * @param integer $itemsPerPage Per page count
     * @param boolean $published Whether to filter by "published" attribute
     * @param string $categoryId Optional category id filter
     * @param string $order Sort order
     * @param boolean $desc Whether to sort by DESC
     * @return array
     */
    private function querySet($page, $itemsPerPage, $published, $categoryId, $order, $desc)
    {
        $db = $this->db->select(self::getSharedColumns(null, true))
                       ->from(self::getTableName());

        $this->appendTranslationRelation();
        $this->appendWebPageRelation();

        if ($categoryId !== null) {
            $this->db->innerJoin(ProductCategoryRelationMapper::getTableName());
        }

        $db->whereEquals(ProductTranslationMapper::column('lang_id'), $this->getLangId());

        if ($published === true) {
            $db->andWhereEquals(self::column('published'), '1');
        }

        if ($categoryId !== null) {
            $this->appendJunctionCategory($categoryId);
        }

        $db->orderBy(self::column($order));

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
        return $this->db->select(array(OrderProductMapper::column('product_id') => 'id'), true)
                        ->from(OrderProductMapper::getTableName())
                        ->innerJoin(OrderInfoMapper::getTableName())
                        ->whereEquals(OrderInfoMapper::column('id'), OrderProductMapper::getRawColumn('order_id'))
                        ->andWhereEquals(OrderInfoMapper::column('approved'), new RawSqlFragment("'1'"))
                        ->groupBy('id')
                        ->having('SUM', OrderProductMapper::column('qty'), '>=', $qty)
                        ->limit($limit)
                        ->queryAll('id');
    }

    /**
     * Create attribute match queries
     * 
     * @param array $pair
     * @param string $categoryId
     * @param mixed $customerId Optional customer ID
     * @param string $sort Sorting column
     * @return string
     */
    private function createAttributeMatchQueries(array $pair, $categoryId, $customerId, $sort)
    {
        $qb = $this->db->getQueryBuilder();

        // Amount of registered mappers we have
        $amount = count($pair);

        // Iteration counter
        $i = 0;

        foreach ($pair as $groupId => $valueId) {
            $this->appendAttributeMatchQuery($qb, $categoryId, $customerId, $groupId, $valueId, $sort);

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
     * @param mixed $customerId
     * @param string $groupId
     * @param string $valueId
     * @param string $sort Sorting column
     * @return void
     */
    private function appendAttributeMatchQuery(QueryBuilderInterface $qb, $categoryId, $customerId, $groupId, $valueId, $sort)
    {
        $qb->openBracket();

        $qb->select(self::getSharedColumns($customerId), true)
           ->from(ProductAttributeMapper::getTableName())
           ->leftJoin(self::getTableName(), array(
                self::column('id') =>ProductAttributeMapper::column('product_id')
           ))
           // Filter by category ID
           ->innerJoin(ProductCategoryRelationMapper::getTableName(), array(
                sprintf('%s.master_id', ProductCategoryRelationMapper::getTableName()) => self::column('id'),
                sprintf('%s.slave_id', ProductCategoryRelationMapper::getTableName()) => (int) $categoryId
            ));

            if ($customerId != null) {
                $qb->leftJoin(WishlistMapper::getTableName(), array(
                    WishlistMapper::column('product_id') => self::column('id'),
                    WishlistMapper::column('customer_id') => $customerId
                ));
            }

        // Slug
        $qb->leftJoin(WebPageMapper::getTableName(), array(
            self::column('web_page_id') => WebPageMapper::column('id')
        ));

        // Filter by group and value IDs
        $qb->whereEquals('group_id', (int) $groupId)
           ->andWhereEquals('value_id', (int) $valueId);

        // Create sorting rules
        $sortingRules = CategorySortGadget::createSortingRules($sort);

        // Prepend table name to sorting columns
        foreach ($sortingRules['columns'] as &$sortingColumn) {
            $sortingColumn = self::column($sortingColumn);
        }

        $qb->orderBy(implode(', ', $sortingRules['columns']));

        if ($sortingRules['desc'] === true) {
            $qb->desc();
        }

        $qb->closeBracket();
    }

    /**
     * Appends translation relation
     * 
     * @return void
     */
    private function appendTranslationRelation()
    {
        $this->db->leftJoin(ProductTranslationMapper::getTableName(), array(
            self::column('id') => ProductTranslationMapper::getRawColumn('id')
        ));
    }

    /**
     * Append web page relation by linked IDs
     * 
     * @return void
     */
    private function appendWebPageRelation()
    {
        $this->db->leftJoin(WebPageMapper::getTableName(), array(
            ProductTranslationMapper::column('web_page_id') => WebPageMapper::getRawColumn('id')
        ));
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
        $this->db->leftJoin(WishlistMapper::getTableName(), array(
            WishlistMapper::column('product_id') => self::getRawColumn('id'),
            WishlistMapper::column('customer_id') => $customerId
        ));
    }

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
    public function findByAttributes($categoryId, $customerId, array $attributes, $sort, $page = null, $itemsPerPage = null)
    {
        $query = $this->createAttributeMatchQueries($attributes, $categoryId, $customerId, $sort);

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
            $sortingColumn = self::column('id');
        }

        $db = $this->db->select(self::getSharedColumns(null, true))
                       ->from(self::getTableName());

        $this->appendTranslationRelation();
        $this->appendWebPageRelation();

        if (!empty($input['category_id'])) {
            $this->db->innerJoin(ProductCategoryRelationMapper::getTableName());
        }

        $db->whereEquals('1', '1');

        $db->andWhereLike(ProductTranslationMapper::column('name'), '%'.$input['name'].'%', true)
           ->andWhereEquals(self::column('date'), $input['date'], true)
           ->andWhereEquals(self::column('id'), $input['id'], true)
           ->andWhereEquals(self::column('regular_price'), $input['regular_price'], true)
           ->andWhereEquals(self::column('published'), $input['published'], true)
           ->andWhereEquals(self::column('seo'), $input['seo'], true);

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
        $db = $this->db->select()
                        ->count($this->getPk())
                        ->from(self::getTableName());

        $this->appendTranslationRelation();

        return $db->whereEquals(ProductTranslationMapper::column('lang_id'), $this->getLangId())
                  ->andWhereEquals(self::column('published'), '1')
                  ->andWhereNotEquals(self::column('stoke_price'), '0')
                  ->queryScalar();
    }

    /**
     * Fetch all stokes
     * 
     * @param integer $limit Limit of records to be returned
     * @return array
     */
    public function fetchAllStokes($limit)
    {
        $db = $this->db->select(self::getSharedColumns(null, true))
                        ->from(self::getTableName());

        $this->appendTranslationRelation();
        $this->appendWebPageRelation();

        return $db->whereEquals(ProductTranslationMapper::column('lang_id'), $this->getLangId())
                  ->andWhereEquals(self::column('published'), '1')
                  ->andWhereNotEquals(self::column('stoke_price'), '0')
                  ->orderBy(self::column($this->getPk()))
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
        $db = $this->db->select(self::getSharedColumns($customerId))
                       ->from(self::getTableName());

        $this->appendTranslationRelation();
        $this->appendWebPageRelation();

        if ($customerId != null) {
            $this->appendCustomerRelation($customerId);
        }

        return $db->whereEquals(ProductTranslationMapper::column('lang_id'), $this->getLangId())
                  ->andWhereEquals(self::column('published'), '1')
                  ->andWhereNotEquals(self::column('stoke_price'), '0')
                  ->orderBy(self::column('id'))
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
                        ->min(self::column('regular_price'), 'min_price')
                        ->from(self::getTableName())
                        ->innerJoin(ProductCategoryRelationMapper::getTableName())
                        ->whereEquals(self::column('published'), '1');

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

        $this->appendTranslationRelation();
        $this->appendWebPageRelation();

        if ($categoryId !== null) {
            $this->db->innerJoin(ProductCategoryRelationMapper::getTableName());
        }

        $db->whereEquals(self::column('lang_id'), $this->getLangId())
           ->andWhereEquals(self::column('published'), '1')
           ->andWhereGreaterThan(self::column('views'), '0');

        if ($categoryId !== null) {
            $this->appendJunctionCategory($categoryId);
        }

        $db->orderBy(self::column('views'))
           ->desc();

        return $db->limit($limit)
                  ->queryAll();
    }

    /**
     * Fetch published products by a collection of their associated IDs
     * 
     * @param array $ids A collection of product IDs
     * @param string $customerId Optional customer ID
     * @return array
     */
    public function fetchByIds(array $ids, $customerId = null)
    {
        $db = $this->db->select(self::getSharedColumns($customerId, false))
                       ->from(self::getTableName());

        $this->appendTranslationRelation();
        $this->appendWebPageRelation();

        if ($customerId != null) {
            $this->appendCustomerRelation($customerId);
        }

        $db->whereIn(self::column('id'), $ids)
           ->andWhereEquals(self::column('published'), '1');

        return $db->queryAll();
    }

    /**
     * Find attached category names by product ID
     * 
     * @param int $id Product ID
     * @return array
     */
    private function queryCategoryRelation($id)
    {
        // To be selected
        $columns = array(
            CategoryMapper::column('id'),
            CategoryTranslationMapper::column('name')
        );

        $db = $this->db->select($columns)
                       ->from(ProductCategoryRelationMapper::getTableName())
                       // Category relation
                       ->innerJoin(CategoryMapper::getTableName(), array(
                            CategoryMapper::column('id') => ProductCategoryRelationMapper::getRawColumn('slave_id')
                       ))
                       // Category translation relation
                       ->leftJoin(CategoryTranslationMapper::getTableName(), array(
                            CategoryTranslationMapper::column('id') => CategoryMapper::getRawColumn('id')
                       ))
                       // Constraints
                       ->whereEquals(ProductCategoryRelationMapper::column('master_id'), $id)
                       ->andWhereEquals(CategoryTranslationMapper::column('lang_id'), $this->getLangId());

        return $db->queryAll();
    }

    /**
     * Find similar attached products
     * 
     * @param int $id Product ID
     * @return array
     */
    private function querySimilarRelation($id)
    {
        // Columns to be selected
        $columns = array(
            self::column('id'), 
            ProductTranslationMapper::column('name')
        );

        $db = $this->db->select($columns)
                       ->from(ProductSimilarRelationMapper::getTableName())
                       // Product relation
                       ->innerJoin(self::getTableName(), array(
                            self::column('id') => ProductSimilarRelationMapper::getRawColumn('slave_id')
                       ))
                       // Product translation relation
                       ->leftJoin(ProductTranslationMapper::getTableName(), array(
                            ProductTranslationMapper::column('id') => self::getRawColumn('id')
                       ))
                       // Constraints
                       ->whereEquals(ProductSimilarRelationMapper::column('master_id'), $id)
                       ->andWhereEquals(ProductTranslationMapper::column('lang_id'), $this->getLangId());

        return $db->queryAll();
    }

    /**
     * Find recommended products
     * 
     * @param int $id Product ID
     * @return array
     */
    private function queryRecommendedRelation($id)
    {
        // Columns to be selected
        $columns = array(
            self::column('id'), 
            ProductTranslationMapper::column('name')
        );

        $db = $this->db->select($columns)
                       ->from(ProductRecommendedMapper::getTableName())
                       // Product relation
                       ->innerJoin(self::getTableName(), array(
                            self::column('id') => ProductRecommendedMapper::getRawColumn('slave_id')
                       ))
                       // Product translation relation
                       ->leftJoin(ProductTranslationMapper::getTableName(), array(
                            ProductTranslationMapper::column('id') => self::getRawColumn('id')
                       ))
                       // Constraints
                       ->whereEquals(ProductRecommendedMapper::column('master_id'), $id)
                       ->andWhereEquals(ProductTranslationMapper::column('lang_id'), $this->getLangId());

        return $db->queryAll();
    }

    /**
     * Fetches product's data by its associated id
     * 
     * @param string $id Product id
     * @param boolean $junction Whether to grab meta information about its relation data
     * @param integer $customerId Optional customer ID
     * @param boolean $withTranslations Whether to fetch translations or not
     * @return array
     */
    public function fetchById($id, $junction = true, $customerId = null, $withTranslations = false)
    {
        $columns = array_merge(self::getSharedColumns($customerId), array(
            BrandMapper::column('name') => 'brand'
        ));

        $db = $this->createWebPageSelect($columns)
                   // Brand relation
                   ->leftJoin(BrandMapper::getTableName(), array(
                        BrandMapper::column('id') => self::getRawColumn('brand_id')
                   ));

        if ($customerId != null) {
            $this->appendCustomerRelation($customerId);
        }

        $db->whereEquals(self::column('id'), $id)
           ->andWhereEquals(self::column('published'), '1');

        $rows = $withTranslations === true ? $db->queryAll() : array($db->query());

        // Append relation data if required
        if ($rows && $junction === true) {
            foreach ($rows as &$row) {
                $row['categories'] = $this->queryCategoryRelation($id);
                $row['recommended'] = $this->queryRecommendedRelation($id);
                $row['similar'] = $this->querySimilarRelation($id);
            }
        }

        if ($withTranslations === false && isset($rows[0])) {
            return $rows[0];
        } else if ($withTranslations === true) {
            return $rows;
        } else {
            return false;
        }
    }

    /**
     * Fetches basic product info by its associated id
     * 
     * @param string $id Product id
     * @return array
     */
    public function fetchBasicById($id)
    {
        // To be selected
        $columns = array(
            ProductTranslationMapper::column('name'), 
            self::column('regular_price'), 
            self::column('stoke_price'), 
            self::column('in_stock'), 
            self::column('cover')
        );

        $db = $this->db->select($columns)
                        ->from(self::getTableName())
                        // Translation relation
                        ->leftJoin(ProductTranslationMapper::getTableName(), array(
                            ProductTranslationMapper::column('id') => self::getRawColumn('id')
                        ))
                        ->whereEquals(self::column('id'), $id)
                        ->andWhereEquals(ProductTranslationMapper::column('lang_id'), $this->getLangId());

        return $db->query();
    }

    /**
     * Counts all available products
     * 
     * @return integer
     */
    public function countAll()
    {
        return (int) $this->db->select()
                              ->count(self::column('id'))
                              ->from(self::getTableName())
                              ->queryScalar();
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
        $column = self::column('id');

        $db = $this->db->select($column)
                       ->from(self::getTableName());

        $this->appendTranslationRelation();

        return $db->innerJoin(ProductCategoryRelationMapper::getTableName())
                  ->whereEquals(
                    ProductTranslationMapper::column('lang_id'), 
                    $this->getLangId()
                )
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
        $db = $this->db->select(self::getSharedColumns(null, true))
                       ->from(self::getTableName());

        $this->appendTranslationRelation();
        $this->appendWebPageRelation();

        if ($categoryId !== null) {
            $this->db->innerJoin(ProductCategoryRelationMapper::getTableName());
        }

        $db->whereEquals(self::column('lang_id'), $this->getLangId())
           ->andWhereEquals(self::column('published'), '1');

        if ($categoryId !== null) {
            $this->appendJunctionCategory($categoryId);
        }

        return $db->orderBy(self::column('id'))
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
        $columns = self::getSharedColumns($customerId);

        $db = $this->db->select($columns)
                       ->from(self::getTableName());

        $this->appendTranslationRelation();
        $this->appendWebPageRelation();

        if ($customerId != null) {
            $this->appendCustomerRelation($customerId);
        }

        if ($keyword === null) {
            $db->innerJoin(ProductCategoryRelationMapper::getTableName());
        }

        $db->whereEquals(ProductTranslationMapper::column('lang_id'), $this->getLangId())
           ->andWhereEquals(self::column('published'), '1');

        if ($keyword === null) {
            $db->andWhereEquals(sprintf('%s.%s', ProductCategoryRelationMapper::getTableName(), self::PARAM_JUNCTION_SLAVE_COLUMN), $categoryId)
               ->andWhereEquals(sprintf('%s.%s', self::getTableName(), 'id'), new RawSqlFragment(self::PARAM_JUNCTION_MASTER_COLUMN));
        }

        if ($keyword !== null) {
            $db->andWhereLike(self::column('name'), '%'.$keyword.'%');
        }

        $sortingRules = CategorySortGadget::createSortingRules($sort);

        // Prepend table name to sorting columns
        foreach ($sortingRules['columns'] as &$sortingColumn) {
            $sortingColumn = self::column($sortingColumn);
        }

        $db->orderBy(implode(', ', $sortingRules['columns']));

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
        return count($this->getMasterIdsFromJunction(ProductCategoryRelationMapper::getTableName(), $categoryId));
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
     * Update settings
     * 
     * @param array $settings
     * @return boolean
     */
    public function updateSettings(array $settings)
    {
        return $this->updateColumns($settings, array('regular_price', 'published', 'seo'));
    }

    /**
     * Updates or inserts a product (depending on value of PK)
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function save(array $input)
    {
        // References
        $data = $input['data'];
        $product =& $data['product'];
        $translations =& $data['translation'];

        // Save data
        $this->savePage('Shop', 'Shop:Product@indexAction', ArrayUtils::arrayWithout($product, array('features', 'attributes', 'slug', 'spec_cat_id', 'category_id', 'recommended_ids', 'similar_ids')), $translations);

        // Last product ID
        $id = !empty($product['id']) ? $product['id'] : $this->getLastId();

        // Synchronize relations
        $this->syncWithJunction(ProductCategoryRelationMapper::getTableName(), $id, $product['category_id']);

        // Update into recommended junction table
        if (isset($product['recommended_ids'])) {
            $this->syncWithJunction(ProductRecommendedMapper::getTableName(), $id, $product['recommended_ids']);
        }

        // Update into similar junction table
        if (isset($product['similar_ids'])) {
            $this->syncWithJunction(ProductSimilarRelationMapper::getTableName(), $id, $product['similar_ids']);
        }

        // Save features, of present
        if (isset($data['features'])) {
            $this->saveFeatures($id, $data['features']['translation']);
        }

        // Specification category relation
        $this->syncWithJunction(SpecificationCategoryProductRelationMapper::getTableName(), $id, isset($product['spec_cat_id']) ? $product['spec_cat_id'] : array());

        return true;
    }

    /**
     * Save features
     * 
     * @param int $id Product ID
     * @param array $translations Translations
     * @return array
     */
    private function saveFeatures($id, array $translations)
    {
        // Delete previous records, if any
        $this->db->delete()
                 ->from(SpecificationValueMapper::getTableName())
                 ->whereEquals('product_id', $id)
                 ->execute();

        // Extract item IDs
        $itemIds = array_values($translations);
        $itemIds = array_keys($itemIds[0]);

        foreach ($itemIds as $key) {
            // Data for columns
            $primaryValues = array(
                'product_id' => $id,
                'item_id' => $key
            );

            // 1. Insert first a new value
            $this->db->insert(SpecificationValueMapper::getTableName(), $primaryValues)
                     ->execute();

            foreach ($translations as $langId => $translation) {
                foreach ($translation as $itemId => $value) {
                    if ($itemId == $key) {
                        $translationValues = array(
                            'id' => $this->getLastPk(SpecificationValueMapper::getTableName()),
                            'lang_id' => $langId,
                            'value' => $value
                        );

                        // 3. Insert into translations
                        $this->db->insert(SpecificationValueTranslationMapper::getTableName(), $translationValues)
                                 ->execute();
                    }
                }
            }
        }

        // Insert values with their translations now
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

        $this->removeFromJunction(ProductCategoryRelationMapper::getTableName(), $id);
        $this->removeFromJunction(ProductRecommendedMapper::getTableName(), $id);
        $this->removeFromJunction(ProductSimilarRelationMapper::getTableName(), $id);

        return true;
    }
}
