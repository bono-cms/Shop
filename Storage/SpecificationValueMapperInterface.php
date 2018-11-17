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

interface SpecificationValueMapperInterface
{
    /**
     * Find values by product ID
     * 
     * @param int $id Product ID
     * @return array
     */
    public function findByProduct($id);
}
