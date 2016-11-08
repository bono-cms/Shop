<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Shop\Controller;

use Site\Controller\AbstractController;
use Krystal\Form\Gadget\LastCategoryKeeper;

abstract class AbstractShopController extends AbstractController
{
    /**
     * Returns configuration entity
     * 
     * @return \Krystal\Stdlib\VirtualEntity
     */
    final protected function getConfig()
    {
        return $this->getModuleService('configManager')->getEntity();
    }

    /**
     * Returns category keeper service
     * 
     * @return \Krystal\Form\Gadget\LastCategoryKeeper
     */
    final protected function getCategoryIdKeeper()
    {
        static $keeper = null;

        if (is_null($keeper)) {
            $keeper = new LastCategoryKeeper($this->sessionBag, 'last_category_id', true);
        }

        return $keeper;
    }
}
