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
use Shop\Storage\DeliveryTypeMapperInterface;

final class DeliveryTypeMapper extends AbstractMapper implements DeliveryTypeMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_shop_delivery_types');
    }

    /**
     * {@inheritDoc}
     */
    public static function getTranslationTable()
    {
        return DeliveryTypeTranslationMapper::getTableName();
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
            self::column('price'),
            self::column('order'),
            DeliveryTypeTranslationMapper::column('lang_id'),
            DeliveryTypeTranslationMapper::column('name')
        );
    }

    /**
     * Fetches delivery type name by its associated ID
     * 
     * @param string $id
     * @return string
     */
    public function fetchNameById($id)
    {
        return $this->findColumnByPk($id, 'name');
    }

    /**
     * Fetches delivery type meta data by its associated id
     * 
     * @param string $id
     * @param boolean $withTranslations Whether to fetch translations or not
     * @return array
     */
    public function fetchById($id, $withTranslations)
    {
        return $this->findEntity($this->getColumns(), $id, $withTranslations);
    }

    /**
     * Fetches all delivery types
     * 
     * @param boolean $sort Whether to sort by order
     * @return array
     */
    public function fetchAll($sort)
    {
        $db = $this->createEntitySelect($this->getColumns())
                   ->whereEquals(DeliveryTypeTranslationMapper::column('lang_id'), $this->getLangId());

        if ($sort === false) {
            $db->orderBy($this->getPk())
               ->desc();
        } else {
            $db->orderBy(array(
                self::column('order'), 
                new RawSqlFragment(sprintf('CASE WHEN %s = 0 THEN %s END DESC', self::column('order'), self::column('id')))
            ));
        }

        return $db->queryAll();
    }
}
