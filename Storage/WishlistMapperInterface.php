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

interface WishlistMapperInterface
{
    /**
     * Removes a product from wishlist
     * 
     * @param string $customerId
     * @param string $productId
     * @return boolean
     */
    public function remove($customerId, $productId);

    /**
     * Adds a product to whishlist
     * 
     * @param string $customerId
     * @param string $productId
     * @return boolean
     */
    public function add($customerId, $productId);

    /**
     * Count quantity of products associated with customer ID
     * 
     * @param string $customerId
     * @return string
     */
    public function countByCustomerId($customerId);

    /**
     * Fetches all products associated by customer ID
     * 
     * @param string $customerId
     * @return array
     */
    public function fetchAllByCustomerId($customerId);
}
