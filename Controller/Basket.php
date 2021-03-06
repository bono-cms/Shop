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

final class Basket extends AbstractShopController
{
    /**
     * Shows a basket page
     * 
     * @param string $id Page id
     * @return string
     */
    public function indexAction($id)
    {
        $pageManager = $this->getService('Pages', 'pageManager');
        $page = $pageManager->fetchById($id);

        if ($page !== false) {
            // Declare that the page is basket
            $page->setBasketPage(true);

            // Load view plugins
            $this->loadSitePlugins();
            $this->view->getBreadcrumbBag()
                       ->addOne($page->getName());

            return $this->view->render('shop-basket', array(
                'products' => $this->getBasketManager()->getProducts(),
                'page' => $page,
                'deliveryTypes' => $this->getModuleService('deliveryTypeManager')->fetchAll(true),
                'languages' => $pageManager->getSwitchUrls($id, 'Shop:Basket@indexAction')
            ));

        } else {
            return false;
        }
    }

    /**
     * Recounts the price with its new quantity for one product
     * 
     * @return string
     */
    public function recountAction()
    {
        if ($this->request->hasPost('id', 'qty')) {
            $id = $this->request->getPost('id');
            $qty = $this->request->getPost('qty');

            $basketManager = $this->getBasketManager();
            $basketManager->recount($id, $qty);
            $basketManager->save();

            return json_encode(array(
                'product' => $basketManager->getProductStat($id),
                'all' => $basketManager->getAllStat()
            ));
        }
    }

    /**
     * Returns common basket statistic as JSON string (so that we can easily read it on client-side)
     * 
     * @return string
     */
    public function getStatAction()
    {
        return json_encode($this->getBasketManager()->getAllStat());
    }

    /**
     * Adds a product id into a basket with its quantity
     * 
     * @return string
     */
    public function addAction()
    {
        if ($this->request->hasPost('id', 'qty')) {
            // Get HTTP POST variables
            $id = $this->request->getPost('id');
            $qty = $this->request->getPost('qty');
            $attributes = $this->request->getPost('attributes', array()); // Optional attributes

            $productManager = $this->getModuleService('productManager');
            $product = $productManager->fetchBasicById($id);

            // Make sure the valid product id supplied
            if ($product !== false) {
                // Grab basket manager to add it
                $basketManager = $this->getBasketManager();

                // Make sure, that quantity cannot be greater than a stocking value
                if ($qty > $product->getInStock()) {
                    // Error code that indicates aforementioned error
                    return -1;
                } else {
                    $basketManager->add($id, $qty, $attributes);
                    $basketManager->save();

                    return json_encode(array(
                        'basket' => $basketManager->getAllStat(),
                        'product' => array(
                            'id' => $product->getId(),
                            'regularPrice' => $product->getRegularPrice(),
                            'stokePrice' => $product->getStokePrice(),
                            'name' => $product->getName(),
                            'cover' => $product->getImageUrl('450x450'),
                            'qty' => $qty
                        )
                    ));
                }

            } else {
                // Failure
                return 0;
            }
        }
    }

    /**
     * Removes a product from the basket and adds it to wishlist
     * 
     * @return string
     */
    public function wishlistAction()
    {
        // Validate requirement
        $this->validateCustomerRequirement();

        if ($this->request->hasPost('id')) {
            // Request variables
            $id = $this->request->getPost('id');
            $customerId = $this->createCustomerId();

            // Remove a product from basket
            $basketManager = $this->getBasketManager();
            $basketManager->removeById($id);
            $basketManager->save();

            // Then add it to wishlist
            $wishlistManager = $this->getModuleService('wishlistManager');
            $wishlistManager->add($customerId, $id);

            // Return new statistic
            return json_encode(array(
                'wishlistCount' => $wishlistManager->getCount($customerId),
                'basket' => $basketManager->getAllStat()
            ));
        }
    }

    /**
     * Removes a product by its associated id
     * 
     * @return string
     */
    public function deleteAction()
    {
        if ($this->request->hasPost('id')) {
            $id = $this->request->getPost('id');

            $basketManager = $this->getBasketManager();
            $basketManager->removeById($id);
            $basketManager->save();

            return json_encode($basketManager->getAllStat());
        }
    }

    /**
     * Clears the basket
     * 
     * @return string
     */
    public function clearAction()
    {
        $basketManager = $this->getBasketManager();
        $basketManager->clear();
        $basketManager->save();

        $this->flashBag->set('success', 'Your basket has been cleared successfully');

        return json_encode($basketManager->getAllStat());
    }

    /**
     * Returns basket manager
     * Just a shortcut
     * 
     * @return \Shop\Service\BasketManager
     */
    private function getBasketManager()
    {
        return $this->getModuleService('basketManager');
    }
}
