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
use Shop\Storage\OrderStatusMapperInterface;
use Krystal\Db\Sql\RawSqlFragment;

final class OrderStatusMapper extends AbstractMapper implements OrderStatusMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_shop_orders_status');
    }

    /**
     * {@inheritDoc}
     */
    public static function getTranslationTable()
    {
        return OrderStatusTranslationMapper::getTableName();
    }

    /**
     * {@inheritDoc}
     */
    private function getColumns()
    {
        return array(
            self::column('id'),
            self::column('order'),
            OrderStatusTranslationMapper::column('lang_id'),
            OrderStatusTranslationMapper::column('name'),
            OrderStatusTranslationMapper::column('description')
        );
    }

    /**
     * Fetches a row by its associated ID
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
     * Fetch all rows
     * 
     * @param boolean $sort Whether to sort rows
     * @return array
     */
    public function fetchAll($sort = false)
    {
        $db = $this->createEntitySelect($this->getColumns())
                   ->whereEquals(OrderStatusTranslationMapper::column('lang_id'), $this->getLangId());

        // Whether to sort
        if ($sort === true) {
            $db->orderBy(array(
                'order',
                new RawSqlFragment(sprintf('CASE WHEN `order` = 0 THEN %s END DESC', self::column('id')))
            ));
        } else {
            $db->orderBy($this->getPk())
               ->desc();
        }

        return $db->queryAll();
    }
}
