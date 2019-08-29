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
use Cms\Storage\MySQL\WebPageMapper;
use Shop\Storage\CategoryMapperInterface;
use Krystal\Db\Sql\RawSqlFragment;
use Krystal\Db\Sql\RawBinding;

final class CategoryMapper extends AbstractMapper implements CategoryMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_shop_categories');
    }

    /**
     * {@inheritDoc}
     */
    public static function getTranslationTable()
    {
        return CategoryTranslationMapper::getTableName();
    }

    /**
     * Returns shared columns to be selected
     * 
     * @return array
     */
    private function getColumns()
    {
        return array(
            self::column('cover'),
            self::column('id'),
            self::column('parent_id'),
            self::column('order'),
            self::column('seo'),
            CategoryTranslationMapper::column('lang_id'),
            CategoryTranslationMapper::column('web_page_id'),
            CategoryTranslationMapper::column('description'),
            CategoryTranslationMapper::column('name'),
            CategoryTranslationMapper::column('title'),
            CategoryTranslationMapper::column('keywords'),
            CategoryTranslationMapper::column('meta_description'),
            WebPageMapper::column('slug'),
            WebPageMapper::column('changefreq'),
            WebPageMapper::column('priority')
        );
    }

    /**
     * Fetches category tree with product count and URLs
     * 
     * @return array
     */
    public function fetchTree()
    {
        // Columns to be selected
        $columns = array(
            self::column('id'),
            self::column('parent_id'),
            CategoryTranslationMapper::column('lang_id'),
            CategoryTranslationMapper::column('name'),
            WebPageMapper::column('slug')
        );

        return $this->db->select($columns)
                        ->count(ProductCategoryRelationMapper::getRawColumn(ProductMapper::PARAM_JUNCTION_MASTER_COLUMN), 'product_count')
                        ->from(self::getTableName())
                        // Product relation
                        ->leftJoin(ProductCategoryRelationMapper::getTableName(), array(
                            ProductCategoryRelationMapper::column(ProductMapper::PARAM_JUNCTION_SLAVE_COLUMN) => self::getRawColumn('id')
                        ))
                        // Category translation relation
                        ->innerJoin(CategoryTranslationMapper::getTableName(), array(
                            self::column('id') => CategoryTranslationMapper::getRawColumn('id')
                        ))
                        // Web page relation
                        ->leftJoin(WebPageMapper::getTableName(), array(
                            WebPageMapper::column('id') => CategoryTranslationMapper::getRawColumn('web_page_id')
                        ))
                        ->groupBy(self::column('id'))
                        ->queryAll();
    }

    /**
     * Fetches child rows by associated parent id
     * 
     * @param string $parentId
     * @param boolean $top Whether to return by ID or parent ID
     * @return array
     */
    public function fetchChildrenByParentId($parentId, $top)
    {
        $top = $top ? self::column('id') : self::column('parent_id');

        // Columns to be selected
        $columns = array_merge($this->getColumns(), array(WebPageMapper::column('slug')));

        $db = $this->db->select($columns)
                        // Product counter
                        ->count(self::PARAM_JUNCTION_MASTER_COLUMN, 'product_count')
                        ->from(ProductCategoryRelationMapper::getTableName())
                        // Category relation
                        ->rightJoin(self::getTableName(), array(
                            $top => ProductCategoryRelationMapper::getRawColumn(self::PARAM_JUNCTION_SLAVE_COLUMN)
                        ))
                        // Category translation relation
                        ->leftJoin(CategoryTranslationMapper::getTableName(), array(
                            CategoryTranslationMapper::column('id') => self::getRawColumn('id')
                        ))
                        // Web page relation
                        ->leftJoin(WebPageMapper::getTableName(), array(
                            WebPageMapper::column('id') => CategoryTranslationMapper::getRawColumn('web_page_id'),
                            WebPageMapper::column('lang_id') => CategoryTranslationMapper::getRawColumn('lang_id')
                        ))
                        // Constraints
                        ->whereEquals(self::column('parent_id'), $parentId)
                        ->andWhereEquals(CategoryTranslationMapper::column('lang_id'), $this->getLangId())
                        ->groupBy(self::column('id'))
                        ->orderBy(new RawSqlFragment(sprintf('`order`, CASE WHEN `order` = 0 THEN %s END DESC', self::column('id'))));

        return $db->queryAll();
    }

    /**
     * Fetches breadcrumb's data
     * 
     * @param string $id Category id
     * @return array
     */
    public function fetchBcData()
    {
        // Columns to be selected
        $columns = array(
            CategoryTranslationMapper::column('name'),
            CategoryTranslationMapper::column('web_page_id'), 
            CategoryTranslationMapper::column('lang_id'), 
            self::column('parent_id'), 
            self::column('id')
        );

        return $this->db->select($columns)
                        ->from(self::getTableName())
                        // Category translation relation
                        ->leftJoin(CategoryTranslationMapper::getTableName(), array(
                            CategoryTranslationMapper::column('id') => self::getRawColumn('id')
                        ))
                        ->whereEquals(CategoryTranslationMapper::column('lang_id'), $this->getLangId())
                        ->queryAll();
    }

    /**
     * Fetches category name by its associated id
     * 
     * @param string $id Category id
     * @return string
     */
    public function fetchNameById($id)
    {
        return $this->findColumnByPk($id, 'name');
    }

    /**
     * Fetches all categories
     * 
     * @return array
     */
    public function fetchAll()
    {
        return $this->db->select($this->getColumns())
                        ->from(self::getTableName())
                        // Category translation relation
                        ->leftJoin(CategoryTranslationMapper::getTableName(), array(
                            self::column('id') => CategoryTranslationMapper::getRawColumn('id')
                        ))
                        ->whereEquals('lang_id', $this->getLangId())
                        ->queryAll();
    }

    /**
     * Fetches category's data by its associated id
     * 
     * @param string $id
     * @param boolean $withTranslations Whether to fetch translations or not
     * @return array
     */
    public function fetchById($id, $withTranslations)
    {
        return $this->findWebPage($this->getColumns(), $id, $withTranslations);
    }

    /**
     * Adds a category
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function insert(array $input)
    {
        $category =& $input['category'];
        $translations =& $input['translation'];

        // Substitute with empty if didn't receive
        if (!isset($category['attribute_group_id'])) {
            $category['attribute_group_id'] = array();
        }

        $groups = $category['attribute_group_id'];
        unset($category['attribute_group_id']);

        // Insert a category
        $this->savePage('Shop', 'Shop:Category@indexAction', $category, $translations);

        // If there's at least one selected group, then insert into the junction table
        if (!empty($groups)) {
            return $this->insertIntoJunction(ProductAttributeGroupRelationMapper::getTableName(), $this->getLastId(), $groups);
        }

        return true;
    }

    /**
     * Updates a category
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function update(array $input)
    {
        $category =& $input['category'];
        $translations =& $input['translation'];

        unset($category['attribute_group_id']);
        return $this->savePage('Shop', 'Shop:Category@indexAction', $category, $translations);
    }

    /**
     * Counts all available categories
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
     * Deletes a category by its associated id
     * 
     * @param string $id
     * @return boolean
     */
    public function deleteById($id)
    {
        return $this->deletePage($id);
    }

    /**
     * Deletes a category by its associated parent id
     * 
     * @param string $parentId Category parent id
     * @return boolean
     */
    public function deleteByParentId($parentId)
    {
        return $this->deleteByColumn('parent_id', $parentId);
    }
}
