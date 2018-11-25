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
use Shop\Storage\ProductAttributeMapperInterface;
use Shop\Service\CategorySortGadget;
use Shop\Service\AttributeProcessor;
use Krystal\Db\Sql\RawSqlFragment;
use Krystal\Db\Sql\RawBinding;

final class ProductAttributeMapper extends AbstractMapper implements ProductAttributeMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_shop_product_attributes');
    }

    /**
     * Find attached dynamic attributes by product ID
     * 
     * @param string $productId
     * @return array
     */
    public function findDynamicAttributes($productId)
    {
        // Columns to be selected
        $columns = array(
            AttributeGroupMapper::column('id') => 'group_id',
            AttributeGroupMapper::column('name') => 'group_name',
            AttributeValueMapper::column('name') => 'value_name',
            AttributeValueMapper::column('id') => 'value_id'
        );

        return $this->db->select($columns)
                        ->from(AttributeGroupMapper::getTableName())
                        // Attribute value relation
                        ->innerJoin(AttributeValueMapper::getTableName(), array(
                            AttributeGroupMapper::column('id') => AttributeValueMapper::getRawColumn('group_id')
                        ))
                        ->innerJoin(self::getTableName(), array(
                            AttributeGroupMapper::column('dynamic') => new RawBinding('1'),
                            self::column('product_id') => $productId,
                            self::column('group_id') => AttributeGroupMapper::getRawColumn('id')
                        ))
                        ->queryAll();
    }

    /**
     * Finds attached static attributes. Primarily used to render attributes on product page
     * 
     * @param string $productId
     * @return array
     */
    public function findStaticAttributes($productId)
    {
        // Columns to be selected
        $columns = array(
            AttributeGroupMapper::column('name') => 'group',
            AttributeValueMapper::column('name') => 'attribute'
        );

        return $this->db->select($columns)
                        ->from(AttributeGroupMapper::getTableName())
                        ->innerJoin(AttributeValueMapper::getTableName(), array(
                            AttributeGroupMapper::column('id') => AttributeValueMapper::getRawColumn('group_id')
                        ))
                        ->innerJoin(self::getTableName(), array(
                            AttributeGroupMapper::column('dynamic') => new RawSqlFragment('0'),
                            self::column('product_id') => $productId,
                            self::column('group_id') => AttributeGroupMapper::getRawColumn('id'),
                            self::column('value_id') => AttributeValueMapper::getRawColumn('id')
                        ))
                        ->queryAll();
    }

    /**
     * Find a collection of attributes
     * 
     * @param string $productId
     * @return array
     */
    public function findAttributesByProductId($productId)
    {
        return $this->db->select(array('group_id', 'value_id'))
                        ->from(self::getTableName())
                        ->whereEquals('product_id', $productId)
                        ->queryAll();
    }

    /**
     * Deletes attributes by associated product id
     * 
     * @param string $productId
     * @return boolean
     */
    public function deleteByProductId($productId)
    {
        return $this->deleteByColumn('product_id', $productId);
    }

    /**
     * Stores attribute relations
     * 
     * @param string $productId Product id
     * @param array $values
     * @return boolean
     */
    public function store($productId, array $values)
    {
        return $this->db->insertMany(self::getTableName(), array('product_id', 'group_id', 'value_id'), AttributeProcessor::normalizeInput($productId, $values))
                        ->execute();
    }
}
