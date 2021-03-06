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

use Shop\Service\ProductEntity;

final class Product extends AbstractShopController
{
    /**
     * Renders product information in JSON format
     * 
     * @return string
     */
    public function quickViewAction()
    {
        if ($this->request->hasQuery('id') && $this->request->hasQuery('size')) {
            // Request variables
            $id = $this->request->getQuery('id');
            $coverSize = $this->request->getQuery('size');
            $thumbsLimit = (int) $this->request->getQuery('thumbsLimit', 4);

            // Grab the service
            $productManager = $this->getModuleService('productManager');

            // Load view
            $this->loadSitePlugins();

            // Render partial
            return $this->view->disableLayout()->render('quick-view-modal', array(
                'product' => $productManager->fetchFullById($id, $this->createCustomerId()),
                'images' => $productManager->fetchAllPublishedImagesById($id, $thumbsLimit),
                'coverSize' => $coverSize,
                'basketManager' => $this->getModuleService('basketManager')
            ));
        }
    }

    /**
     * Fetches a product by its associated id
     * 
     * @param string $id Product id
     * @return string
     */
    public function indexAction($id)
    {
        // Grab a service
        $productManager = $this->getModuleService('productManager');
        $product = $productManager->fetchFullById($id, $this->createCustomerId());

        // If $product isn't false, then its an entity
        if ($product !== false) {
            // Indicate that this is product page
            $product->setProductPage(true);

            // Add getSpecifications() and getFrontSpecifications() on product entity
            $this->getModuleService('specificationValueService')->addSpecifications($product);

            // Configure breadcrumbs
            $this->configureBreadcrumbs($product);

            // Load required plugins for view
            $this->loadPlugins();

            $response = $this->view->render('shop-product', array(
                // Image bags of current product
                'images' => $productManager->fetchAllPublishedImagesById($id),
                'page' => $product,
                'product' => $product,
                'languages' => $productManager->getSwitchUrls($id)
            ));

            // After product is viewed, it's time to increment its view count
            $productManager->incrementViewCount($id);

            return $response;
        } else {
            // Returning false will trigger 404 error automatically
            return false;
        }
    }

    /**
     * Configure breadcrumbs for view
     * 
     * @param \Shop\Service\ProductEntity $product
     * @return void
     */
    private function configureBreadcrumbs(ProductEntity $product)
    {
        $keeper = $this->getCategoryIdKeeper();

        if ($keeper->hasLastCategoryId()) {
            // Set the last persisted category id
            $product->setCategoryId($keeper->getLastCategoryId(), ProductEntity::FILTER_INT);

            // Append breadcrumbs
            $this->view->getBreadcrumbBag()
                       ->add($this->getModuleService('productManager')->getBreadcrumbs($product));

        } else {
            // No last id? Then make sure no breadcrumbs displayed
            $this->view->getBreadcrumbBag()->clear();
        }
    }

    /**
     * Loads view plugins
     * 
     * @return void
     */
    private function loadPlugins()
    {
        $this->loadSitePlugins();

        // Load zoom plugin
        $this->view->getPluginBag()
                   ->load(array('zoom'));
    }
}
