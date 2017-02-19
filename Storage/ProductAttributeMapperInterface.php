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
     * Find products by attributes and associated category id
     * 
     * @param string $categoryId Category id
     * @param array $attributes A collection of group IDs and their value IDs
     * @param string $page Optional page number
     * @param string $itemsPerPage Optional Per page count filter
     * @return array
     */
    public function findByAttributes($categoryId, array $attributes, $page = null, $itemsPerPage = null);

    /**
     * Find attached dynamic attributes by product ID
     * 
     * @param string $productId
     * @return array
     */
    public function findDynamicAttributes($productId);

    /**
     * Finds attached static attributes. Primarily used to render atttbutes on product page
     * 
     * @param string $productId
     * @return array
     */
    public function findStaticAttributes($productId);

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
