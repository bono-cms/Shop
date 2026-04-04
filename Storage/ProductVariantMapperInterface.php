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

interface ProductVariantMapperInterface
{
    /**
     * Fetches all variants associated with a product ID
     * 
     * @param int $productId
     * @param boolean $published Whether to fetch only published ones
     * @return array
     */
    public function fetchAllByProductId($productId, $published = false);
}
