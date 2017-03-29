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

use Shop\Storage\WishlistMapperInterface;
use Cms\Service\AbstractManager;
use Krystal\Stdlib\VirtualEntity;

final class WishlistManager extends AbstractManager implements WishlistManagerInterface
{
    /**
     * Any compliant mapper
     * 
     * @var \Shop\Storage\WishlistMapperInterface
     */
    private $wishlistMapper;

    /**
     * State initialization
     * 
     * @param \Shop\Storage\WishlistMapperInterface $wishlistMapper
     * @return void
     */
    public function __construct(WishlistMapperInterface $wishlistMapper)
    {
        $this->wishlistMapper = $wishlistMapper;
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
        return $this->wishlistMapper->remove($customerId, $productId);
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
        return $this->wishlistMapper->add($customerId, $productId);
    }

    /**
     * Fetch all product entities product entities
     * 
     * @param integer $customerId
     * @return array
     */
    public function fetchAllByCustomerId($customerId)
    {
        return ($this->wishlistMapper->fetchAllByCustomerId($customerId));
    }
}
