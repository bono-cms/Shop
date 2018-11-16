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
use Shop\Storage\SpecificationCategoryMapperInterface;

final class SpecificationCategoryMapper extends AbstractMapper implements SpecificationCategoryMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_shop_specification_category');
    }

    /**
     * {@inheritDoc}
     */
    public static function getTranslationTable()
    {
        return SpecificationCategoryTranslationMapper::getTableName();
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
            self::column('order'),
            SpecificationCategoryTranslationMapper::column('lang_id'),
            SpecificationCategoryTranslationMapper::column('name')
        );
    }

    /**
     * Fetch all categories
     * 
     * @return array
     */
    public function fetchAll()
    {
        $db = $this->createEntitySelect($this->getColumns())
                   ->whereEquals(SpecificationCategoryTranslationMapper::column('lang_id'), $this->getLangId())
                   ->orderBy(self::column('id'))
                   ->desc();

        return $db->queryAll();
    }

    /**
     * Fetches category by its ID
     * 
     * @param int $id Category id
     * @param boolean $withTranslations Whether to fetch translations or not
     * @return array
     */
    public function fetchById($id, $withTranslations)
    {
        return $this->findEntity($this->getColumns(), $id, $withTranslations);
    }
}
