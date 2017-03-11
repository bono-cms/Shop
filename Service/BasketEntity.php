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

use Krystal\Stdlib\VirtualEntity;

final class BasketEntity extends VirtualEntity
{
    /**
     * Basket service
     * 
     * @var \Shop\Service\BasketManagerInterface
     */
    private $basketManager;

    /**
     * State initialization
     * 
     * @param \Shop\Service\BasketManagerInterface $basketManager
     * @return void
     */
    public function __construct(BasketManagerInterface $basketManager = null)
    {
        $this->basketManager = $basketManager;
        $this->once = true;
    }

    /**
     * Returns all product entities stored in the basket
     * 
     * @param integer $limit Whether to limit output
     * @return array
     */
    public function getProducts($limit = false)
    {
        return $this->basketManager->getProducts($limit);
    }

    /**
     * Checks whether basket is disabled
     * 
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->getEnabled();
    }

    /**
     * Returns image URL
     * 
     * @param string $size
     * @return string
     */
    public function getImageUrl($size)
    {
        return $this->getImageBag()->getUrl($size);
    }

    /**
     * Checks if there's at least one product
     * 
     * @return boolean
     */
    public function hasProducts()
    {
        return $this->getTotalQty() != 0;
    }
}
