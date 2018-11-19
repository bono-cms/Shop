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
use Shop\Storage\SpecificationItemMapperInterface;

final class SpecificationItemMapper extends AbstractMapper implements SpecificationItemMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_shop_specification_item');
    }

    /**
     * {@inheritDoc}
     */
    public static function getTranslationTable()
    {
        return SpecificationItemTranslationMapper::getTableName();
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
            self::column('category_id'),
            self::column('order'),
            self::column('front'),
            self::column('type'),
            SpecificationItemTranslationMapper::column('lang_id'),
            SpecificationItemTranslationMapper::column('name'),
            SpecificationItemTranslationMapper::column('hint')
        );
    }

    /**
     * Fetch all items
     * 
     * @param int $categoryId Optional category ID filter
     * @return array
     */
    public function fetchAll($categoryId = null)
    {
        $db = $this->createEntitySelect($this->getColumns())
                   ->whereEquals(SpecificationItemTranslationMapper::column('lang_id'), $this->getLangId());

        // Apply on demand
        if ($categoryId !== null) {
            $db->andWhereEquals(self::column('category_id'), $categoryId);
        }

        $db->orderBy(self::column('id'))
           ->desc();

        return $db->queryAll();
    }

    /**
     * Fetches item by its ID
     * 
     * @param int $id Item id
     * @param boolean $withTranslations Whether to fetch translations or not
     * @return array
     */
    public function fetchById($id, $withTranslations)
    {
        return $this->findEntity($this->getColumns(), $id, $withTranslations);
    }
}
