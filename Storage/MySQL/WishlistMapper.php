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
     * Fetches all products associated by customer ID
     * 
     * @param string $customerId
     * @return array
     */
    public function fetchAllByCustomerId($customerId)
    {
        // Columns to be selected
        $columns = array(
            ProductMapper::getFullColumnName('id'),
            ProductMapper::getFullColumnName('lang_id'),
            ProductMapper::getFullColumnName('web_page_id'),
            ProductMapper::getFullColumnName('name'),
            ProductMapper::getFullColumnName('regular_price'),
            ProductMapper::getFullColumnName('stoke_price'),
            ProductMapper::getFullColumnName('special_offer'),
            ProductMapper::getFullColumnName('cover'),
            WebPageMapper::getFullColumnName('slug'),
        );

        return $this->db->select($columns)
                        ->from(self::getTableName())
                        ->leftJoin(ProductMapper::getTableName())
                        ->on()
                        ->equals(self::getFullColumnName('product_id'), new RawSqlFragment(ProductMapper::getFullColumnName('id')))
                        ->leftJoin(WebPageMapper::getTableName())
                        ->on()
                        ->equals(WebPageMapper::getFullColumnName('id'), new RawSqlFragment(ProductMapper::getFullColumnName('web_page_id')))
                        ->whereEquals(self::getFullColumnName('customer_id'), $customerId)
                        ->orderBy(self::getFullColumnName('wishlist_item_id'))
                        ->desc()
                        ->queryAll();
    }
}
