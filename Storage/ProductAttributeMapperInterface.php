<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Shop\Storage;

interface ProductAttributeMapperInterface
{
    /**
     * Find attached dynamic attributes by product ID
     * 
     * @param string $productId
     * @return array
     */
    public function findDynamicAttributes($productId);

    /**
     * Finds attached attributes. Primarily used to render atttbutes on product page
     * 
     * @param string $productId
     * @return array
     */
    public function findAttachedAttributes($productId);

    /**
     * Find a collection of attributes
     * 
     * @param string $productId
     * @return array
     */
    public function findAttributesByProductId($productId);

    /**
     * Deletes attributes by associated product id
     * 
     * @param string $productId
     * @return boolean
     */
    public function deleteByProductId($productId);

    /**
     * Stores attribute relations
     * 
     * @param string $id Product id
     * @param array $values
     * @return boolean
     */
    public function store($id, array $values);
}
