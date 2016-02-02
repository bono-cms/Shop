<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Shop\Controller\Admin;

use Cms\Controller\Admin\AbstractController;
use Krystal\Tree\AdjacencyList\TreeBuilder;
use Krystal\Tree\AdjacencyList\Render\PhpArray;
use Krystal\Validate\Pattern;
use Krystal\Stdlib\VirtualEntity;

final class Product extends AbstractController
{
    /**
     * Returns product manager
     * 
     * @return \Shop\Service\ProductManager
     */
    private function getProductManager()
    {
        return $this->getModuleService('productManager');
    }

    /**
     * Returns product manager
     * 
     * @return \Shop\Service\ProductManager
     */
    private function getCategoryManager()
    {
        return $this->getModuleService('categoryManager');
    }

    /**
     * Creates a form
     * 
     * @param \Krystal\Stdlib\VirtualEntity $product
     * @param string $title
     * @return string
     */
    private function createForm(VirtualEntity $product, $title)
    {
        // Load view plugins
        $this->view->getPluginBag()
                   ->load(array('preview', $this->getWysiwygPluginName()))
                   ->appendScript('@Shop/admin/product.form.js')
                   ->appendStylesheet('@Shop/admin/product.form.css');

        // Append breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Shop', 'Shop:Admin:Browser@indexAction')
                                       ->addOne($title);

        // If viewing edit form, then grab product photos as well
        if ($product->getId()) {
            $photos = $this->getProductManager()->fetchAllImagesById($product->getId());
        } else {
            $photos = array();
        }

        return $this->view->render('product.form', array(
            'photos' => $photos,
            'product' => $product,
            'categories' => $this->getCategoryManager()->getCategoriesTree(),
            'config' => $this->getModuleService('configManager')->getEntity()
        ));
    }

    /**
     * Renders empty form
     * 
     * @return string
     */
    public function addAction()
    {
        $product = new VirtualEntity();
        $product->setSeo(true)
                ->setPublished(true)
                ->setSpecialOffer(false);

        return $this->createForm($product, 'Add a product');
    }

    /**
     * Renders edit form
     * 
     * @param string $id
     * @return string
     */
    public function editAction($id)
    {
        $product = $this->getProductManager()->fetchById($id);

        if ($product !== false) {
            return $this->createForm($product, 'Edit the product');
        } else {
            return false;
        }
    }

    /**
     * Deletes a product
     * 
     * @return string
     */
    public function deleteAction()
    {
        return $this->invokeRemoval('productManager');
    }

    /**
     * Save updates from the table
     * 
     * @return string
     */
    public function tweakAction()
    {
        if ($this->request->hasPost('price', 'published', 'seo')) {
            // Grab request data
            $prices = $this->request->getPost('price');
            $published = $this->request->getPost('published');
            $seo = $this->request->getPost('seo');

            // Grab a manager
            $productManager = $this->getProductManager();

            $productManager->updatePrices($prices);
            $productManager->updatePublished($published);
            $productManager->updateSeo($seo);

            $this->flashBag->set('success', 'Settings have been updated successfully');
            return '1';
        }
    }

    /**
     * Persist a product
     * 
     * @return string
     */
    public function saveAction()
    {
        $input = $this->request->getPost();

        return $this->invokeSave('productManager', $input['product']['id'], $this->request->getAll(), array(
            'input' => array(
                'source' => $input['product'],
                'definition' => array(
                    'title' => new Pattern\Title(),
                    'regular_price' => new Pattern\Price(),
                    'description' => new Pattern\Description()
                )
            )
        ));
    }
}
