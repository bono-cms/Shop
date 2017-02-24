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

final class ProductEntity extends VirtualEntity
{
    /**
     * Returns image URL
     * 
     * @param string $size
     * @return string
     */
    public function getImageUrl($size)
    {
        // The image bag is only available on valid IDs
        if ($this->getId()) {
            return $this->getImageBag()->getUrl($size);
        }
    }

    /**
     * Checks whether a product has attached similar products
     * 
     * @return boolean
     */
    public function hasRecommendedProducts()
    {
        return (bool) $this->getRecommendedProducts();
    }

    /**
     * Checks whether a product has attached similar products
     * 
     * @return boolean
     */
    public function hasSimilarProducts()
    {
        return (bool) $this->getSimilarProducts();
    }

    /**
     * Returns sale percentage
     * 
     * @return integer
     */
    public function getSalePercentage()
    {
        if ($this->hasStokePrice()) {
            return (int) $this->getStokePrice() * 100 / $this->getPrice();
        } else {
            return 0;
        }
    }

    /**
     * Checks whether a product is not of out stoke
     * 
     * @return boolean
     */
    public function isAvailable()
    {
        return $this->getInStock() > 0;
    }

    /**
     * Checks whether this product is marked as a special offer
     * 
     * @return boolean
     */
    public function isSpecialOffer()
    {
        return $this->getSpecialOffer();
    }

    /**
     * Checks whether product has its stoke price
     * 
     * @return boolean
     */
    public function hasStokePrice()
    {
        return $this->getStokePrice() > 0;
    }

    /**
     * Tells whether product is in stoke's state
     * 
     * @return boolean
     */
    public function inStoke()
    {
        return $this->hasStokePrice();
    }
}
