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
     * @param int $productId
     * @return array
     */
    public function findDynamicAttributes($productId)
    {
        // Columns to be selected
        $columns = [
            AttributeGroupMapper::column('id') => 'group_id',
            AttributeGroupTranslationMapper::column('name') => 'group_name',
            AttributeValueTranslationMapper::column('name') => 'value_name',
            AttributeValueMapper::column('id') => 'value_id'
        ];

        $db = $this->db->select($columns)
                       ->from(AttributeGroupMapper::getTableName())
                       // Attribute group translations
                       ->leftJoin(AttributeGroupTranslationMapper::getTableName(), [
                            AttributeGroupTranslationMapper::column('id') => AttributeGroupMapper::getRawColumn('id'),
                            AttributeGroupTranslationMapper::column('lang_id') => $this->getLangId()
                       ])
                       // Attribute value relation
                       ->innerJoin(AttributeValueMapper::getTableName(), [
                           AttributeGroupMapper::column('id') => AttributeValueMapper::getRawColumn('group_id')
                       ])
                       // Attribute value translation
                       ->leftJoin(AttributeValueTranslationMapper::getTableName(), [
                            AttributeValueTranslationMapper::column('id') => AttributeValueMapper::getRawColumn('id'),
                            AttributeValueTranslationMapper::column('lang_id') => $this->getLangId()
                       ])
                       ->innerJoin(self::getTableName(), [
                           AttributeGroupMapper::column('dynamic') => new RawBinding('1'),
                           self::column('product_id') => $productId,
                           self::column('group_id') => AttributeGroupMapper::getRawColumn('id')
                       ]);

        return $db->queryAll();
    }

    /**
     * Finds attached static attributes. Primarily used to render attributes on product page
     * 
     * @param int $productId
     * @return array
     */
    public function findStaticAttributes($productId)
    {
        // Columns to be selected
        $columns = [
            AttributeGroupTranslationMapper::column('name') => 'group',
            AttributeValueTranslationMapper::column('name') => 'attribute'
        ];

        $db = $this->db->select($columns)
                       ->from(AttributeGroupMapper::getTableName())
                       // Attribute group translations
                       ->leftJoin(AttributeGroupTranslationMapper::getTableName(), [
                           AttributeGroupTranslationMapper::column('id') => AttributeGroupMapper::getRawColumn('id'),
                           AttributeGroupTranslationMapper::column('lang_id') => $this->getLangId()
                       ])
                       ->innerJoin(AttributeValueMapper::getTableName(), [
                           AttributeGroupMapper::column('id') => AttributeValueMapper::getRawColumn('group_id')
                       ])
                       // Attribute value translation mapper
                       ->leftJoin(AttributeValueTranslationMapper::getTableName(), [
                            AttributeValueTranslationMapper::column('id') => AttributeValueMapper::getRawColumn('id'),
                            AttributeValueTranslationMapper::column('lang_id') => $this->getLangId()
                       ])
                       ->innerJoin(self::getTableName(), [
                           AttributeGroupMapper::column('dynamic') => new RawSqlFragment('0'),
                           self::column('product_id') => $productId,
                           self::column('group_id') => AttributeGroupMapper::getRawColumn('id'),
                           self::column('value_id') => AttributeValueMapper::getRawColumn('id')
                       ]);

        return $db->queryAll();
    }

    /**
     * Find a collection of attributes
     * 
     * @param int $productId
     * @return array
     */
    public function findAttributesByProductId($productId)
    {
        $db = $this->db->select([
                            'group_id',
                            'value_id'
                        ])
                        ->from(self::getTableName())
                        ->whereEquals('product_id', $productId);

        return $db->queryAll();
    }

    /**
     * Deletes attributes by associated product id
     * 
     * @param int $productId
     * @return boolean
     */
    public function deleteByProductId($productId)
    {
        return $this->deleteByColumn('product_id', $productId);
    }

    /**
     * Stores attribute relations
     * 
     * @param int $productId
     * @param array $values
     * @return boolean
     */
    public function store($productId, array $values)
    {
        return $this->db->insertMany(self::getTableName(), array('product_id', 'group_id', 'value_id'), AttributeProcessor::normalizeInput($productId, $values))
                        ->execute();
    }
}
