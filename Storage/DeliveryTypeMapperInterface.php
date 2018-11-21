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

interface DeliveryTypeMapperInterface
{
    /**
     * Fetches delivery type name by its associated ID
     * 
     * @param string $id
     * @return string
     */
    public function fetchNameById($id);

    /**
     * Fetches delivery type meta data by its associated id
     * 
     * @param string $id
     * @param boolean $withTranslations Whether to fetch translations or not
     * @return array
     */
    public function fetchById($id, $withTranslations);

    /**
     * Fetches all delivery types
     * 
     * @return array
     */
    public function fetchAll();
}
