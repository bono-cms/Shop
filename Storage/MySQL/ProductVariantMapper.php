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
use Shop\Storage\ProductVariantMapperInterface;

final class ProductVariantMapper extends AbstractMapper implements ProductVariantMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_shop_product_variants');
    }

    /**
     * Fetches all variants associated with a product ID
     * 
     * @param int $productId
     * @param boolean $published Whether to fetch only published ones
     * @return array
     */
    public function fetchAllByProductId($productId, $published = false)
    {
        $db = $this->db->select('*')
                   ->from(self::getTableName())
                   ->whereEquals('product_id', $productId);

        if ($published) {
            $db->andWhereEquals('published', (bool) $published);
        }

        $db->orderBy('id')
           ->desc();

        return $db->queryAll();
    }
}
