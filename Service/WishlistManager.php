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
use Krystal\Stdlib\VirtualEntity;

final class WishlistManager implements WishlistManagerInterface
{
    /**
     * Any compliant mapper
     * 
     * @var \Shop\Storage\WishlistMapperInterface
     */
    private $wishlistMapper;

    /**
     * Product service
     * 
     * @var \Shop\Service\ProductManagerInterface
     */
    private $productManager;

    /**
     * State initialization
     * 
     * @param \Shop\Storage\WishlistMapperInterface $wishlistMapper
     * @param \Shop\Service\ProductManagerInterface $productManager
     * @return void
     */
    public function __construct(WishlistMapperInterface $wishlistMapper, ProductManagerInterface $productManager)
    {
        $this->wishlistMapper = $wishlistMapper;
        $this->productManager = $productManager;
    }

    /**
     * Returns product count associated with customer ID
     * 
     * @param string $customerId
     * @return integer
     */
    public function getCount($customerId)
    {
        return $this->wishlistMapper->countByCustomerId($customerId);
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
        return $this->productManager->hydrateCollection($this->wishlistMapper->fetchAllByCustomerId($customerId));
    }
}
