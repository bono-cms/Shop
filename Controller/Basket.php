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
        if ($this->request->isPost()) {
            $quantities = $this->request->getPost('qty', []);

            $this->getBasketManager()->recountBatch($quantities);
            $this->flashBag->set('success', 'Basket has been updated');
        }

        return $this->response->back($this->createUrl('Shop:Basket@indexAction'));
    }

    /**
     * Returns common basket statistic as JSON string (so that we can easily read it on client-side)
     * 
     * @return string
     */
    public function getStatAction()
    {
        return $this->json($this->getBasketManager()->getAllStat());
    }

    /**
     * Adds a product variant to the basket via AJAX
     * Expects the following POST parameters:
     * - id (int|string): The base product ID
     * - variant_id (int|string): The specific variant ID
     * - qty (int): The quantity to add
     * 
     * @return string JSON response containing status code, error flag, and updated basket/product data
     */    
    public function addVariant()
    {
        if ($this->request->hasPost('id', 'variant_id', 'qty')) {

            // Get HTTP POST variables
            $id = $this->request->getPost('id');
            $variantId = $this->request->getPost('variant_id');
            $qty = $this->request->getPost('qty');

            $product = $this->getModuleService('productManager')->fetchBasicById($id);
            $variant = $this->getModuleService('variantService')->fetchById($variantId);

            // Make sure the valid product id supplied
            if ($variant !== false && $product !== false) {
                // Make sure, that quantity cannot be greater than a stocking value
                if ($qty > $variant->getStock()) {
                    return $this->json([
                        'code' => -1,
                        'error' => true,
                        'message' => 'Out of stock'
                    ]);
                } else {
                    // Grab basket manager to add it
                    $basketManager = $this->getBasketManager();
                    $basketManager->addVariant($id, $variantId, $qty, $variant->getPrice());

                    return $this->json([
                        'code' => 1,
                        'error' => false,
                        'basket' => $basketManager->getAllStat(),
                        'product' => [
                            'id' => $product->getId(),
                            'variant_id' => $variantId,
                            'regularPrice' => $variant->getPrice(),
                            'stokePrice' => null,
                            'name' => $product->getName(),
                            'cover' => $product->getImageUrl('450x450'),
                            'qty' => $qty
                        ]
                    ]);
                }

            } else {
                return $this->json([
                    'error' => true,
                    'code' => 0,
                    'message' => 'The selected product or variant is no longer available'
                ]);
            }
        }
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
            $attributes = $this->request->getPost('attributes', []); // Optional attributes

            $productManager = $this->getModuleService('productManager');
            $product = $productManager->fetchBasicById($id);

            // Make sure the valid product id supplied
            if ($product !== false) {
                // Make sure, that quantity cannot be greater than a stocking value
                if ($qty > $product->getInStock()) {
                    return $this->json([
                        'code' => -1,
                        'error' => true,
                        'message' => 'Out of stock'
                    ]);
                } else {
                    // Grab basket manager to add it
                    $basketManager = $this->getBasketManager();
                    $basketManager->add($id, $qty, $attributes);

                    return $this->json([
                        'code' => 1,
                        'error' => false,
                        'basket' => $basketManager->getAllStat(),
                        'product' => [
                            'id' => $product->getId(),
                            'regularPrice' => $product->getRegularPrice(),
                            'stokePrice' => $product->getStokePrice(),
                            'name' => $product->getName(),
                            'cover' => $product->getImageUrl('450x450'),
                            'qty' => $qty
                        ]
                    ]);
                }

            } else {
                return $this->json([
                    'error' => true,
                    'code' => 0,
                    'description' => sprintf('The product with ID %s does not exist', $id)
                ]);
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
            $basketManager->remove($id);

            // Then add it to wishlist
            $wishlistManager = $this->getModuleService('wishlistManager');
            $wishlistManager->add($customerId, $id);

            // Return new statistic
            return $this->json([
                'wishlistCount' => $wishlistManager->getCount($customerId),
                'basket' => $basketManager->getAllStat()
            ]);
        }
    }

    /**
     * Removes a product by its associated id
     * 
     * @return string
     */
    public function deleteAction()
    {
        if ($this->request->hasQuery('product_id')) {
            $id = $this->request->getQuery('product_id');
            $variantId = $this->request->getQuery('variant_id', null);

            $basketManager = $this->getBasketManager();

            if ($variantId) {
                $basketManager->removeVariant($id, $variantId);
            } else {
                $basketManager->remove($id);
            }

            $this->flashBag->set('success', 'Selected product has been removed from your basket');
            return $this->response->back();

        } else {
            return false;
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

        $this->flashBag->set('success', 'Your basket has been cleared successfully');

        return $this->json($basketManager->getAllStat());
    }

    /**
     * Returns basket manager
     * 
     * @return \Shop\Service\BasketManager
     */
    private function getBasketManager()
    {
        return $this->getModuleService('basketManager');
    }
}
