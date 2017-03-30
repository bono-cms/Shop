<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Shop\Controller\Customer;

use Shop\Controller\AbstractShopController;

final class Wishlist extends AbstractShopController
{
    /**
     * Renders the wishlist page
     * 
     * @param string $id Customer ID
     * @return string
     */
    public function indexAction($id)
    {
        $pageManager = $this->getService('Pages', 'pageManager');
        $page = $pageManager->fetchById($id);

        if ($page !== false) {
            $this->loadSitePlugins();
            $this->loadBreadcrumbs();

            $products = $this->getModuleService('wishlistManager')->fetchAllByCustomerId($this->createCustomerId());

            return $this->view->render('shop-wishlist', array(
                'page' => $page,
                'products' => $products
            ));

        } else {
            return false;
        }
    }

    /**
     * Load breadcrumbs
     * 
     * @return void
     */
    private function loadBreadcrumbs()
    {
        // Different caption for logged in and non-logged in users
        if (!$this->getService('Members', 'memberManager')->isLoggedIn()) {
            $caption = 'Wishlist';
        } else {
            $caption = 'My wishlist';
        }

        // Append a breadcrumb
        $this->view->getBreadcrumbBag()
                   ->addOne($this->translator->translate($caption));
    }

    /**
     * Shared handler
     * 
     * @param string $method Method name to be invoked
     * @return mixed
     */
    private function handleAction($method)
    {
        if ($this->request->hasPost('id')) {
            // Grab product ID from request
            $id = $this->request->getPost('id');
            $customerId = $this->createCustomerId();

            $wishlistManager = $this->getModuleService('wishlistManager');
            call_user_func(array($wishlistManager, $method), $customerId, $id);

            // Indicate success back to client
            return $wishlistManager->getCount($customerId);
        }
    }

    /**
     * Adds a product to wishlist
     * 
     * @return string A new product count in wishlist
     */
    public function addAction()
    {
        return $this->handleAction('add');
    }

    /**
     * Deletes a product from wishlist
     * 
     * @return string A new product count in wishlist
     */
    public function deleteAction()
    {
        return $this->handleAction('remove');
    }
}
