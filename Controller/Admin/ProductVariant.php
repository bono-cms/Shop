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
use Krystal\Stdlib\VirtualEntity;

final class ProductVariant extends AbstractController
{
    /**
     * Creates a form
     *
     * @param mixed $variant
     * @return string
     */
    private function createForm($variant)
    {
        $new = is_array($variant) ? true : !$variant->getId();

        $product = $this->getModuleService('productManager')->fetchById($variant->getProductId(), false);
        $title = $new ? 'Add new variant' : 'Edit variant';

        // Append breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Shop', 'Shop:Admin:Browser@indexAction')
                                       ->addOne($product->getName(), $this->createUrl('Shop:Admin:Product@editAction', [$product->getId()]))
                                       ->addOne($title);

        return $this->view->render('product.variant.form', array(
            'variant' => $variant,
            'new' => $new,
            'title' => $title,
        ));
    }

    /**
     * Renders empty add form
     *
     * @param int $productId Attached product ID
     * @return string
     */
    public function addAction($productId)
    {
        $variant = new VirtualEntity();
        $variant->setProductId($productId);

        return $this->createForm($variant);
    }

    /**
     * Renders edit form
     *
     * @param int $id Variant ID
     * @return string
     */
    public function editAction($id)
    {
        $variant = $this->getModuleService('variantService')->fetchById($id);

        if ($variant !== false) {
            return $this->createForm($variant);
        } else {
            return false;
        }
    }

    /**
     * Saves a product variant
     *
     * @return string
     */
    public function saveAction()
    {
        $input = $this->request->getPost('variant');
        $new = !isset($input['id']) || !$input['id'];

        $variantService = $this->getModuleService('variantService');
        $variantService->save($input);
        
        $this->flashBag->set('success', $new ? 'The variant has been created successfully' : 'The variant has been updated successfully');

        if ($new) {
            return $variantService->getLastId();
        } else {
            return 1;
        }
    }

    /**
     * Deletes a product variant
     *
     * @param int $id Variant ID
     * @return string
     */
    public function deleteAction($id)
    {
        $this->getModuleService('variantService')->deleteById($id);

        $this->flashBag->set('success', 'Selected variant has been removed successfully');
        return 1;
    }
}