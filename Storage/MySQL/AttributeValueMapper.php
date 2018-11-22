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
use Shop\Storage\AttributeValueMapperInterface;

final class AttributeValueMapper extends AbstractMapper implements AttributeValueMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_shop_attribute_values');
    }

    /**
     * {@inheritDoc}
     */
    public static function getTranslationTable()
    {
        return AttributeValueTranslationMapper::getTableName();
    }

    /**
     * Returns shared columns to be selected
     * 
     * @return array
     */
    private function getColumns()
    {
        return array(
            self::column('id'),
            self::column('group_id'),
            AttributeValueTranslationMapper::column('lang_id'),
            AttributeValueTranslationMapper::column('name')
        );
    }

    /**
     * Fetch all values filtered by group id
     * 
     * @param int $groupId
     * @return array
     */
    public function fetchAllByCategoryId($groupId)
    {
        $db = $this->createEntitySelect($this->getColumns())
                   ->whereEquals('group_id', $groupId)
                   ->andWhereEquals(AttributeValueTranslationMapper::column('lang_id'), $this->getLangId())
                   ->orderBy('id')
                   ->desc();

        return $db->queryAll();
    }

    /**
     * Fetches value by its associated id
     * 
     * @param string $id
     * @param boolean $withTranslations Whether to fetch translations or not
     * @return array
     */
    public function fetchById($id, $withTranslations)
    {
        return $this->findEntity($this->getColumns(), $id, $withTranslations);
    }
}
