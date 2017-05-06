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
use Krystal\Stdlib\ArrayUtils;

final class Product extends AbstractController
{
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
                   ->load(array('preview', 'chosen' ,$this->getWysiwygPluginName()))
                   ->appendScript('@Shop/admin/product.form.js')
                   ->appendStylesheet('@Shop/admin/product.form.css');

        // Append breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Shop', 'Shop:Admin:Browser@indexAction')
                                       ->addOne($title);

        // If viewing edit form, then grab product photos as well
        if ($product->getId()) {
            $photos = $this->getModuleService('productManager')->fetchAllImagesById($product->getId());
        } else {
            $photos = array();
        }

        if ($product->getCategoryIds()) {
            $attributes = $this->getModuleService('categoryManager')->fetchAttributesByIds($product->getCategoryIds(), true);
        } else {
            $attributes = array();
        }

        return $this->view->render('product.form', array(
            'names' => $this->getModuleService('productManager')->fetchAllNames(),
            'photos' => $photos,
            'product' => $product,
            'categories' => $this->getModuleService('categoryManager')->getCategoriesTree(),
            'config' => $this->getModuleService('configManager')->getEntity(),
            'attributes' => $attributes,
            'activeAttributes' => $product->getId() ? $this->getModuleService('productManager')->findAttributesByProductId($product->getId()) : array()
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
        $product = $this->getModuleService('productManager')->fetchById($id);

        if ($product !== false) {
            return $this->createForm($product, 'Edit the product');
        } else {
            return false;
        }
    }

    /**
     * Deletes a product
     * 
     * @param string $id
     * @return string
     */
    public function deleteAction($id)
    {
        $service = $this->getModuleService('productManager');

        // Batch removal
        if ($this->request->hasPost('toDelete')) {
            $ids = array_keys($this->request->getPost('toDelete'));

            $service->deleteByIds($ids);
            $this->flashBag->set('success', 'Selected elements have been removed successfully');

        } else {
            $this->flashBag->set('warning', 'You should select at least one element to remove');
        }

        // Single removal
        if (!empty($id)) {
            $service->deleteById($id);
            $this->flashBag->set('success', 'Selected element has been removed successfully');
        }

        return '1';
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
            $productManager = $this->getModuleService('productManager');

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

        // Recovery missing keys if not received
        $input['product'] = ArrayUtils::arrayRecovery($input['product'], array('category_id'), array());

        $formValidator = $this->createValidator(array(
            'input' => array(
                'source' => $input['product'],
                'definition' => array(
                    'name' => new Pattern\Name(),

                    // Custom case for category id
                    'category_id' => array(
                        'required' => true,
                        'rules' => array(
                            'NotEmpty' => array(
                                'message' => 'Attach at least one category'
                            )
                        )
                    ),
                    
                    'regular_price' => new Pattern\Price(),
                    'description' => new Pattern\Description()
                )
            )
        ));

        if ($formValidator->isValid()) {
            $service = $this->getModuleService('productManager');

            if (!empty($input['product']['id'])) {
                if ($service->update($this->request->getAll())) {
                    $this->flashBag->set('success', 'The element has been updated successfully');
                    return '1';
                }

            } else {
                if ($service->add($this->request->getAll())) {
                    $this->flashBag->set('success', 'The element has been created successfully');
                    return $service->getLastId();
                }
            }

        } else {
            return $formValidator->getErrors();
        }
    }
}
