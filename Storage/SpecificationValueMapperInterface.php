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
     * @param boolean $withTranslations Whether to fetch translations or not
     * @param boolean $extended Whether to fetch all columns
     * @return array
     */
    public function findByProduct($id, $withTranslations, $extended);
}
