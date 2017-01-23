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
use Shop\Storage\ProductAttributeMapperInterface;

final class ProductAttributeMapper extends AbstractMapper implements ProductAttributeMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_shop_product_attributes');
    }

    /**
     * Find a collection of attributes
     * 
     * @param string $productId
     * @return array
     */
    public function findAttributesByProductId($productId)
    {
        return $this->db->select(array('group_id', 'value_id'))
                        ->from(self::getTableName())
                        ->whereEquals('product_id', $productId)
                        ->queryAll();
    }

    /**
     * Deletes attributes by associated product id
     * 
     * @param string $productId
     * @return boolean
     */
    public function deleteByProductId($productId)
    {
        return $this->deleteByColumn('product_id', $productId);
    }

    /**
     * Stores attribute relations
     * 
     * @param string $id Product id
     * @param array $values
     * @return boolean
     */
    public function store($id, array $values)
    {
        $collection = array();

        foreach ($values as $groupId => $valueId) {
            $collection[] = array($id, $groupId, $valueId);
        }

        return $this->db->insertMany(self::getTableName(), array('product_id', 'group_id', 'value_id'), $collection)
                        ->execute();
    }
}
