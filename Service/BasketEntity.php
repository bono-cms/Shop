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
