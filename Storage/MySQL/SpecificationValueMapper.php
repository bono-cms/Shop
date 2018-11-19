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

use Krystal\Db\Sql\RawSqlFragment;
use Cms\Storage\MySQL\AbstractMapper;
use Shop\Storage\SpecificationValueMapperInterface;

final class SpecificationValueMapper extends AbstractMapper implements SpecificationValueMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_shop_specification_values');
    }

    /**
     * {@inheritDoc}
     */
    public static function getTranslationTable()
    {
        return SpecificationValueTranslationMapper::getTableName();
    }

    /**
     * Find values by product ID
     * 
     * @param int $id Product ID
     * @param boolean $withTranslations Whether to fetch translations or not
     * @param boolean $extended Whether to fetch all columns
     * @return array
     */
    public function findByProduct($id, $withTranslations, $extended)
    {
        // Columns to be selected
        $columns = array(
            SpecificationCategoryMapper::column('id') => 'category_id',
            SpecificationItemMapper::column('front'),
            SpecificationItemTranslationMapper::column('name') => 'item',
            SpecificationItemTranslationMapper::column('hint'),
            SpecificationValueTranslationMapper::column('value')
        );

        if ($extended == true) {
            $columns = array_merge($columns, array(
                SpecificationItemMapper::column('id'),
                SpecificationItemMapper::column('type'),
                SpecificationItemTranslationMapper::column('lang_id'),
                SpecificationCategoryTranslationMapper::column('name') => 'category',
            ));
        }

        $db = $this->db->select($columns)
                       ->from(SpecificationCategoryProductRelationMapper::getTableName())
                       // Item relation
                       ->leftJoin(SpecificationItemMapper::getTableName(), array(
                            SpecificationItemMapper::column('category_id') => SpecificationCategoryProductRelationMapper::getRawColumn('slave_id')
                       ))
                       // Item translation relation
                       ->leftJoin(SpecificationItemTranslationMapper::getTableName(), array(
                            SpecificationItemTranslationMapper::column('id') => SpecificationItemMapper::getRawColumn('id')
                       ))
                       // Value relation
                       ->leftJoin(SpecificationValueMapper::getTableName(), array(
                            SpecificationValueMapper::column('item_id') => SpecificationItemMapper::getRawColumn('id')
                       ))
                       // Category relation
                       ->leftJoin(SpecificationCategoryMapper::getTableName(), array(
                            SpecificationCategoryMapper::column('id') => SpecificationItemMapper::getRawColumn('category_id')
                       ))
                       // Category translation relation
                       ->leftJoin(SpecificationCategoryTranslationMapper::getTableName(), array(
                            SpecificationCategoryTranslationMapper::column('id') => SpecificationCategoryMapper::getRawColumn('id'),
                            SpecificationCategoryTranslationMapper::column('lang_id') => SpecificationItemTranslationMapper::getRawColumn('lang_id')
                       ))
                       // Value translation relation
                       ->leftJoin(SpecificationValueTranslationMapper::getTableName(), array(
                            SpecificationValueTranslationMapper::column('id') => SpecificationValueMapper::getRawColumn('id'),
                            SpecificationValueTranslationMapper::column('lang_id') => SpecificationItemTranslationMapper::getRawColumn('lang_id')
                       ))
                       // Constraint
                       ->whereEquals(SpecificationCategoryProductRelationMapper::column('master_id'), $id);

        if ($withTranslations === false) {
            $db->andWhereEquals(SpecificationItemTranslationMapper::column('lang_id'), $this->getLangId());
        }

        return $db->queryAll();
    }
}
