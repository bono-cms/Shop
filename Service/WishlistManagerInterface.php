<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Shop\Service;

interface WishlistManagerInterface
{
    /**
     * Returns product count associated with customer ID
     * 
     * @param string $customerId
     * @return integer
     */
    public function getCount($customerId);

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
     * Fetch all product entities product entities
     * 
     * @param integer $customerId
     * @return array
     */
    public function fetchAllByCustomerId($customerId);
}
