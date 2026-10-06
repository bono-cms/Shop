<?php

/**
 * This file is part of the Bono CMS
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Shop\Storage;

interface BrandMapperInterface
{
    /**
     * Fetch all brands
     * 
     * @return array
     */
    public function fetchAll();
}
