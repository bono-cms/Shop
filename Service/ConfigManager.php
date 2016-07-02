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

use Krystal\Config\ConfigModuleService;
use Krystal\Stdlib\VirtualEntity;

final class ConfigManager extends ConfigModuleService
{
    /**
     * {@inheritDoc}
     */
    public function getEntity()
    {
        $entity = new VirtualEntity();

        $entity->setShowCaseCount($this->get('showcase_count', 3), VirtualEntity::FILTER_INT)
               ->setSpecialSupport($this->get('special_support', true), VirtualEntity::FILTER_BOOL)
               ->setStokePriceEnabled($this->get('stoke_price_enabled', false), VirtualEntity::FILTER_BOOL)
               ->setDefaultCategoryPerPageCount($this->get('default_category_per_page_count', 5), VirtualEntity::FILTER_INT)
               ->setCurrency($this->get('currency', '$'), VirtualEntity::FILTER_TAGS)
               ->setMaxRecentAmount($this->get('recent_max_amount', 3), VirtualEntity::FILTER_INT)
               ->setBasketPageId($this->get('basket_page_id', 0), VirtualEntity::FILTER_INT)
               ->setStokePerPageCount($this->get('stoke_per_page_count', 10), VirtualEntity::FILTER_INT);

        $entity->setBasketEnabled($this->get('basket_enabled', true), VirtualEntity::FILTER_BOOL);
        $entity->setBasketStorageType($this->get('basket_storage_type', 'cookies'), VirtualEntity::FILTER_TAGS);
        $entity->setBasketStorageTypes(array(
            'session' => 'Save data until a user closes a browser (In session)',
            'cookies' => 'Save data forever (In cookies)'
        ));

        $entity->setCoverHeight($this->get('cover_height', 300), VirtualEntity::FILTER_FLOAT)
               ->setCoverWidth($this->get('cover_width', 300), VirtualEntity::FILTER_FLOAT)
               ->setThumbHeight($this->get('thumb_height', 300), VirtualEntity::FILTER_FLOAT)
               ->setThumbWidth($this->get('thumb_width', 300), VirtualEntity::FILTER_FLOAT)
               ->setCategoryCoverHeight($this->get('category_cover_height', 300), VirtualEntity::FILTER_FLOAT)
               ->setCategoryCoverWidth($this->get('category_cover_width', 300), VirtualEntity::FILTER_FLOAT);

        return $entity;
    }
}
