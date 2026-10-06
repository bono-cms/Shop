<?php

/**
 * This file is part of the Bono CMS
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Shop\Storage;

interface AttributeGroupMapperInterface
{
    /**
     * Fetch all groups
     * 
     * @return array
     */
    public function fetchAll();

    /**
     * Fetches category by its associated id
     * 
     * @param string $id
     * @param boolean $withTranslation Whether to fetch translations or not
     * @return array
     */
    public function fetchById($id, $withTranslation);
}
