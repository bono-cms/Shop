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
use Krystal\Db\Sql\RawSqlFragment;
use Shop\Storage\WishlistMapperInterface;

final class WishlistMapper extends AbstractMapper implements WishlistMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_shop_wishlist');
    }

    /**
     * Removes a product from wishlist
     * 
     * @param string $customerId
     * @param string $productId
     * @return boolean
     */
    public function remove($customerId, $productId)
    {
        return $this->db->delete()
                        ->from(self::getTableName())
                        ->whereEquals('customer_id', $customerId)
                        ->andWhereEquals('product_id', $productId)
                        ->execute();
    }

    /**
     * Adds a product to whishlist
     * 
     * @param string $customerId
     * @param string $productId
     * @return boolean
     */
    public function add($customerId, $productId)
    {
        return $this->persist(array(
            'customer_id' => $customerId,
            'product_id' => $productId
        ));
    }

    /**
     * Count quantity of products associated with customer ID
     * 
     * @param string $customerId
     * @return string
     */
    public function countByCustomerId($customerId)
    {
        return $this->db->select()
                        ->count('wishlist_item_id')
                        ->from(self::getTableName())
                        ->whereEquals('customer_id', $customerId)
                        ->queryScalar();
    }

    /**
     * Fetches all products associated by customer ID
     * 
     * @param string $customerId
     * @return array
     */
    public function fetchAllByCustomerId($customerId)
    {
        return $this->db->select(ProductMapper::getSharedColumns(null, false))
                        ->from(self::getTableName())
                        // Product relation
                        ->leftJoin(ProductMapper::getTableName(), array(
                            self::column('product_id') => ProductMapper::getRawColumn('id')
                        ))
                        // Web page relation
                        ->leftJoin(WebPageMapper::getTableName(), array(
                            WebPageMapper::column('id') => ProductMapper::getRawColumn('web_page_id')
                        ))
                        ->whereEquals(self::column('customer_id'), $customerId)
                        ->orderBy(self::column('wishlist_item_id'))
                        ->desc()
                        ->queryAll();
    }
}
