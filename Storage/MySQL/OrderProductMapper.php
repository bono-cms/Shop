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

use Cms\Storage\MySQL\WebPageMapper;
use Cms\Storage\MySQL\AbstractMapper;
use Shop\Storage\OrderMapperInterface;
use Shop\Storage\OrderProductMapperInterface;
use Krystal\Db\Sql\RawSqlFragment;

final class OrderProductMapper extends AbstractMapper implements OrderProductMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_shop_orders_products');
    }

    /**
     * Counts the sum of sold products
     * 
     * @return float
     */
    public function getPriceSumCount()
    {
        return (float) $this->db->select()
                                ->sum('price', 'count')
                                ->from(self::getTableName())
                                ->query('count');
    }

    /**
     * Counts total amount of sold products
     * 
     * @return integer
     */
    public function getQtySumCount()
    {
        return (int) $this->db->select()
                              ->sum('qty', 'count')
                              ->from(self::getTableName())
                              ->query('count');
    }

    /**
     * Deletes all products associated with provided order's id
     * 
     * @param string $id Order's id
     * @return boolean
     */
    public function deleteAllByOrderId($id)
    {
        return $this->db->delete()
                        ->from(self::getTableName())
                        ->whereEquals('order_id', $id)
                        ->execute();
    }

    /**
     * Adds an order
     * 
     * @param array $data
     * @return boolean
     */
    public function insert(array $data)
    {
        return $this->persist($data);
    }

    /**
     * Decrements product in stock qty
     * 
     * @param string $id
     * @return boolean
     */
    public function decrementProductInStockQtyById($id)
    {
        return $this->db->decrement(ProductMapper::getTableName(), 'in_stock', 1)
                        ->whereEquals('id', $id)
                        ->execute();
    }

    /**
     * Find product ids by associated order id
     * 
     * @param string $id Order id
     * @return array
     */
    public function findProductIdsByOrderId($id)
    {
        $column = 'product_id';

        return $this->db->select($column)
                        ->from(self::getTableName())
                        ->whereEquals('order_id', $id)
                        ->queryAll($column);
    }

    /**
     * Fetches all details by associated order ID
     * 
     * @param string $id Order's ID
     * @param string $customerId Optional filter by customer ID
     * @return array
     */
    public function fetchAllDetailsByOrderId($id, $customerId = null)
    {
        // Columns to be selected
        $columns = array(
            self::getFullColumnName('order_id'),
            self::getFullColumnName('product_id'),
            self::getFullColumnName('name'),
            self::getFullColumnName('price'),
            self::getFullColumnName('sub_total_price'),
            self::getFullColumnName('qty'),
            ProductMapper::getFullColumnName('cover'),
            WebPageMapper::getFullColumnName('slug'),
            WebPageMapper::getFullColumnName('lang_id')
        );

        // Select by order id
        $db = $this->db->select($columns, true)
                       ->from(self::getTableName())
                       ->leftJoin(ProductMapper::getTableName())
                       ->on()
                       ->equals(self::getFullColumnName('product_id'), new RawSqlFragment(ProductMapper::getFullColumnName('id')));

        // If provided, filter also by customer ID
        if ($customerId !== null) {
            $db->innerJoin(OrderInfoMapper::getTableName())
               ->on()
               ->equals(OrderInfoMapper::getFullColumnName('customer_id'), $customerId);
        }

        $db->leftJoin(WebPageMapper::getTableName())
           ->on()
           ->equals(WebPageMapper::getFullColumnName('id'), new RawSqlFragment(ProductMapper::getFullColumnName('web_page_id')))
           ->whereEquals(self::getFullColumnName('order_id'), $id);

        return $db->queryAll();
    }
}
