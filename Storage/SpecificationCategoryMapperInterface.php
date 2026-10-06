<?php

/**
 * This file is part of the Bono CMS
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Shop\Storage;

interface SpecificationCategoryMapperInterface
{
    /**
     * Fetch attached specification category IDs by product ID
     * 
     * @param int $id Product ID
     * @return array
     */
    public function fetchAttachedByProductId($id);
}
