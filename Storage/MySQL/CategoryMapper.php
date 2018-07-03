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
     * {@inheritDoc}
     */
    public static function getJunctionTableName()
    {
        return self::getWithPrefix('bono_module_shop_categories_attr_groups');
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
                        ->count(ProductMapper::column(ProductMapper::PARAM_JUNCTION_MASTER_COLUMN, ProductMapper::getJunctionTableName()), 'product_count')
                        ->from(self::getTableName())
                        // Product relation
                        ->leftJoin(ProductMapper::getJunctionTableName())
                        ->on()
                        ->equals(
                            ProductMapper::column(ProductMapper::PARAM_JUNCTION_SLAVE_COLUMN, ProductMapper::getJunctionTableName()),
                            self::getRawColumn('id')
                        )
                        // Category translation relation
                        ->innerJoin(CategoryTranslationMapper::getTableName())
                        ->on()
                        ->equals(
                            self::column('id'),
                            CategoryTranslationMapper::getRawColumn('id')
                        )
                        // Web page relation
                        ->leftJoin(WebPageMapper::getTableName())
                        ->on()
                        ->equals(
                            WebPageMapper::column('id'), 
                            CategoryTranslationMapper::getRawColumn('web_page_id')
                        )
                        ->groupBy(self::column('id'))
                        ->queryAll();
    }

    /**
     * Finds category attributes by its associated id
     * 
     * @param string $id Category id
     * @param boolean $dynamic Whether to include dynamic attributes
     * @return array
     */
    public function findAttributesById($id, $dynamic)
    {
        // Data to be selected
        $columns = array(
            AttributeGroupMapper::column('id') => 'group_id',
            AttributeGroupMapper::column('name') => 'group_name',
            AttributeGroupMapper::column('dynamic') => 'dynamic',
            AttributeValueMapper::column('id') => 'value_id',
            AttributeValueMapper::column('name') => 'value_name'
        );

        $db = $this->db->select($columns)
                        ->from(self::getJunctionTableName())
                        ->leftJoin(AttributeGroupMapper::getTableName())
                        ->on()
                        ->equals(
                            AttributeGroupMapper::column('id'),
                            self::getRawColumn(self::PARAM_JUNCTION_SLAVE_COLUMN, self::getJunctionTableName())
                        )
                        ->rawAnd()
                        ->equals(self::column(self::PARAM_JUNCTION_MASTER_COLUMN, self::getJunctionTableName()), $id);

        if ($dynamic === false) {
            $db->rawAnd()
               ->equals(AttributeGroupMapper::column('dynamic'), new RawBinding('0'));
        }

        return $db->innerJoin(AttributeValueMapper::getTableName())
                  ->on()
                  ->equals(
                    AttributeValueMapper::column('group_id'),
                    AttributeGroupMapper::getRawColumn('id')
                  )
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

        return $this->db->select($columns)
                        // Product counter
                        ->count(self::PARAM_JUNCTION_MASTER_COLUMN, 'product_count')
                        ->from(ProductMapper::getJunctionTableName())
                        ->rightJoin(self::getTableName())
                        ->on()
                        ->equals(
                            self::column($top), 
                            new RawSqlFragment(ProductMapper::column(self::PARAM_JUNCTION_SLAVE_COLUMN, ProductMapper::getJunctionTableName()))
                        )
                        // Web page relation
                        ->leftJoin(WebPageMapper::getTableName())
                        ->on()
                        ->equals(WebPageMapper::column('id'), new RawSqlFragment(self::column('web_page_id')))
                        ->whereEquals(self::column('parent_id'), $parentId)
                        ->groupBy(self::column('id'))
                        ->orderBy(new RawSqlFragment(sprintf('`order`, CASE WHEN `order` = 0 THEN %s END DESC', self::column('id'))))
                        ->queryAll();
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
                        ->leftJoin(CategoryMapper::getTableName())
                        ->on()
                        ->equals(
                            self::column('id'), 
                            CategoryTranslationMapper::getRawColumn('id')
                        )
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
                        ->leftJoin(CategoryTranslationMapper::getTableName())
                        ->on()
                        ->equals(
                            self::column('id'),
                            CategoryTranslationMapper::getRawColumn('id')
                        )
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
        $category = $this->findWebPage($this->getColumns(), $id, $withTranslations);
        $attrs = $this->getSlaveIdsFromJunction(self::getJunctionTableName(), $id);

        if ($withTranslations === true) {
            foreach ($category as $index => $entity) {
                $category[$index]['attribute_group_id'] = $attrs;
            }

            return $category;

        } else {
            return array_merge($category, array('attribute_group_id' => $attrs));
        }
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
            return $this->insertIntoJunction(self::getJunctionTableName(), $this->getLastId(), $groups);
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

        if (!empty($category['attribute_group_id'])) {
            $this->syncWithJunction(self::getJunctionTableName(), $category['id'], $category['attribute_group_id']);
        } else {
            $this->removeFromJunction(self::getJunctionTableName(), $category['id']);
        }

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
        return $this->deletePage($id) && $this->removeFromJunction(self::getJunctionTableName(), $id);
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
