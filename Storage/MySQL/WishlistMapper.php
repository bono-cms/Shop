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
     * Fetches all products associated by customer ID
     * 
     * @param string $customerId
     * @return array
     */
    public function fetchAllByCustomerId($customerId)
    {
        // Columns to be selected
        $columns = array(
            self::getFullColumnName('name'),
            self::getFullColumnName('regular_price'),
            self::getFullColumnName('stoke_price'),
            self::getFullColumnName('cover'),
            self::getFullColumnName('lang_id'),
            WebPageMapper::getFullColumnName('slug'),
        );

        return $this->db->select($columns)
                        ->from(self::getTableName())
                        ->leftJoin(Product::getTableName())
                        ->on()
                        ->equals(self::getFullColumnName('product_id'), new RawSqlFragment(ProductMapper::getFullColumnName('id')))
                        ->leftJoin(WebPageMapper::getTableName())
                        ->on()
                        ->equals(WebPageMapper::getFullColumnName('id'))
                        ->whereEquals(self::getFullColumnName('customer_id'), $customerId)
                        ->queryAll();
    }
}