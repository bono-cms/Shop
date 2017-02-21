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
use Shop\Service\CategorySortGadget;
use Krystal\Form\Gadget\LastCategoryKeeper;
use Krystal\Form\Gadget\PerPageCount;

abstract class AbstractShopController extends AbstractController
{
    /**
     * Returns per page count
     * 
     * @return \Shop\Service\PerPageCountProvider
     */
    final protected function getPerPageCountGadget()
    {
        static $gadget = null;

        if (is_null($gadget)) {
            // Get default value
            $config = $this->getModuleService('configManager')->getEntity();
            $default = $config->getDefaultCategoryPerPageCount();

            // Prepare defaults
            $defaults = array($default, 3, 5, 10, 15, 20, 25); // Default collection
            $defaults = array_unique($defaults); // Removed duplicates if any
            asort($defaults, \SORT_NUMERIC); // Sort from lower to highest

            $gadget = new PerPageCount($this->sessionBag, 'cat_pc', $default, $defaults);
        }

        return $gadget;
    }

    /**
     * Returns prepared category sort provider
     * 
     * @return \Krystal\Service\CategorySortGadget
     */
    final protected function getCategorySortGadget()
    {
        static $gadget = null;

        if (is_null($gadget)) {
            $gadget = new CategorySortGadget($this->sessionBag);
        }

        return $gadget;
    }

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
