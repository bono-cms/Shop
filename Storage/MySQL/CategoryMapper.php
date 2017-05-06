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
    public static function getJunctionTableName()
    {
        return self::getWithPrefix('bono_module_shop_categories_attr_groups');
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
            sprintf('%s.id', AttributeGroupMapper::getTableName()) => 'group_id',
            sprintf('%s.name', AttributeGroupMapper::getTableName()) => 'group_name',
            sprintf('%s.dynamic', AttributeGroupMapper::getTableName()) => 'dynamic',
            sprintf('%s.id', AttributeValueMapper::getTableName()) => 'value_id',
            sprintf('%s.name', AttributeValueMapper::getTableName()) => 'value_name'
        );

        $db = $this->db->select($columns)
                        ->from(self::getJunctionTableName())
                        ->leftJoin(AttributeGroupMapper::getTableName())
                        ->on()
                        ->equals(
                            sprintf('%s.id', AttributeGroupMapper::getTableName()), 
                            new RawSqlFragment(sprintf('%s.%s', self::getJunctionTableName(), self::PARAM_JUNCTION_SLAVE_COLUMN))
                        )
                        ->rawAnd()
                        ->equals(sprintf('%s.%s', self::getJunctionTableName(), self::PARAM_JUNCTION_MASTER_COLUMN), $id);

        if ($dynamic === false) {
            $db->rawAnd()
               ->equals(AttributeGroupMapper::getFullColumnName('dynamic'), new RawBinding('0'));
        }

        return $db->innerJoin(AttributeValueMapper::getTableName())
                  ->on()
                  ->equals(
                    sprintf('%s.group_id', AttributeValueMapper::getTableName()), 
                    new RawSqlFragment(sprintf('%s.id', AttributeGroupMapper::getTableName()))
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
        $top = $top ? 'id' : 'parent_id';

        $columns = array(
            self::getFullColumnName('cover'),
            self::getFullColumnName('id'),
            self::getFullColumnName('parent_id'),
            self::getFullColumnName('lang_id'),
            self::getFullColumnName('web_page_id'),
            self::getFullColumnName('description'),
            self::getFullColumnName('order'),
            self::getFullColumnName('seo'),
            self::getFullColumnName('description'),
            self::getFullColumnName('name'),
            self::getFullColumnName('title'),
            self::getFullColumnName('keywords'),
            self::getFullColumnName('meta_description'),
            WebPageMapper::getFullColumnName('slug')
        );

        return $this->db->select($columns)
                        ->append(', ')
                        // Product counter
                        ->count(self::PARAM_JUNCTION_MASTER_COLUMN, 'product_count')
                        
                        ->from(ProductMapper::getJunctionTableName())
                        ->rightJoin(self::getTableName())
                        ->on()
                        ->equals(self::getFullColumnName($top), new RawSqlFragment(sprintf('%s.%s', ProductMapper::getJunctionTableName(), self::PARAM_JUNCTION_SLAVE_COLUMN)))
                        ->leftJoin(WebPageMapper::getTableName())
                        ->on()
                        ->equals(WebPageMapper::getFullColumnName('id'), new RawSqlFragment(self::getFullColumnName('web_page_id')))
                        ->whereEquals(self::getFullColumnName('parent_id'), $parentId)
                        ->groupBy(self::getFullColumnName('id'))
                        ->orderBy(new RawSqlFragment(sprintf('`order`, CASE WHEN `order` = 0 THEN %s END DESC', self::getFullColumnName('id'))))
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
        return $this->db->select(array('name', 'web_page_id', 'lang_id', 'parent_id', 'id'))
                        ->from(static::getTableName())
                        ->whereEquals('lang_id', $this->getLangId())
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
        return $this->db->select('*')
                        ->from(static::getTableName())
                        ->whereEquals('lang_id', $this->getLangId())
                        ->queryAll();
    }

    /**
     * Fetches category's data by its associated id
     * 
     * @param string $id
     * @return array
     */
    public function fetchById($id)
    {
        return array_merge($this->findByPk($id), array('attribute_group_id' => $this->getSlaveIdsFromJunction(self::getJunctionTableName(), $id)));
    }

    /**
     * Adds a category
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function insert(array $data)
    {
        // Substitute with empty if didn't receive
        if (!isset($data['attribute_group_id'])) {
            $data['attribute_group_id'] = array();
        }

        $groups = $data['attribute_group_id'];
        unset($data['attribute_group_id']);

        // Insert a category
        $this->persist($this->getWithLang($data));

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
    public function update(array $data)
    {
        if (!empty($data['attribute_group_id'])) {
            $this->syncWithJunction(self::getJunctionTableName(), $data['id'], $data['attribute_group_id']);
        } else {
            $this->removeFromJunction(self::getJunctionTableName(), $data['id']);
        }

        unset($data['attribute_group_id']);
        return $this->persist($data);
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
        return $this->deleteByPk($id) && $this->removeFromJunction(self::getJunctionTableName(), $id);
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
