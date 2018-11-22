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
use Shop\Storage\AttributeGroupMapperInterface;

final class AttributeGroupMapper extends AbstractMapper implements AttributeGroupMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_shop_attribute_groups');
    }

    /**
     * {@inheritDoc}
     */
    public static function getTranslationTable()
    {
        return AttributeGroupTranslationMapper::getTableName();
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
            self::column('dynamic'),
            AttributeGroupTranslationMapper::column('lang_id'),
            AttributeGroupTranslationMapper::column('name')
        );
    }

    /**
     * Fetch all groups
     * 
     * @return array
     */
    public function fetchAll()
    {
        $db = $this->createEntitySelect($this->getColumns())
                   ->whereEquals(AttributeGroupTranslationMapper::column('lang_id'), $this->getLangId())
                   ->orderBy('id')
                   ->desc();

        return $db->queryAll();
    }

    /**
     * Fetches category by its associated id
     * 
     * @param string $id
     * @param boolean $withTranslation Whether to fetch translations or not
     * @return array
     */
    public function fetchById($id, $withTranslation)
    {
        return $this->findEntity($this->getColumns(), $id, $withTranslation);
    }
}
