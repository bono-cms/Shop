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

            return $this->view->render('shop-wishlist', array(
                'page' => $page,
                'products' => $this->getModuleService('wishlistManager')->fetchAllByCustomerId($this->createCustomerId())
            ));

        } else {
            return false;
        }
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

            $wishlistManager = $this->getModuleService('wishlistManager');
            call_user_func(array($wishlistManager, $method), $this->createCustomerId(), $id);

            // Indicate success back to client
            return 1;
        }
    }

    /**
     * Adds a product to wishlist
     * 
     * @return string
     */
    public function addAction()
    {
        return $this->handleAction('add');
    }

    /**
     * Deletes a product from wishlist
     * 
     * @return string
     */
    public function deleteAction()
    {
        return $this->handleAction('remove');
    }
}
