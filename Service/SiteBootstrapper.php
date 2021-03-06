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

use Cms\Service\AbstractSiteBootstrapper;
use Shop\Service\BasketEntity;

final class SiteBootstrapper extends AbstractSiteBootstrapper
{
    /**
     * Loads basket service
     * 
     * @return void
     */
    private function loadBasket()
    {
        // For brevity
        $mm = $this->moduleManager;

        $webPageManager = $mm->getModule('Cms')->getService('webPageManager');
        $pageManager = $mm->getModule('Pages')->getService('pageManager');

        $shop = $mm->getModule('Shop');

        // Grab basket manager and load data from a storage
        $basketManager = $shop->getService('basketManager');
        $config = $shop->getService('configManager')->getEntity();

        $basketWebPageId = $pageManager->fetchWebPageIdById($config->getBasketPageId());
        $basketUrl = $webPageManager->getUrlByWebPageId($basketWebPageId);

        // Now tweak basket's entity
        $basket = new BasketEntity($basketManager);
        $basket->setUrl($basketUrl);
        $basket->setTotalPrice($basketManager->getTotalPrice());
        $basket->setTotalQty($basketManager->getTotalQuantity());
        $basket->setCurrency($config->getCurrency());
        $basket->setEnabled($config->getBasketEnabled());

        // Finally add $basket entity and append a script which handles a basket
        $this->view->addVariable('basket', $basket)
                   ->getPluginBag()
                   ->appendScript('@Shop/site.module.js');
    }

    /**
     * Loads shop service
     * 
     * @return void
     */
    private function loadShop()
    {
        $siteService = $this->moduleManager->getModule('Shop')->getService('siteService');
        $this->view->addVariable('shop', $siteService);
    }

    /**
     * {@inheritDoc}
     */
    public function bootstrap()
    {
        $this->loadBasket();
        $this->loadShop();
    }
}
