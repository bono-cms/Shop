<?php

/**
 * This file is part of the Bono CMS
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
                                       ->addOne($this->translator->translate('Edit the product "%s"', $product->getName()), $this->createUrl('Shop:Admin:Product@editAction', [$product->getId()]))
                                       ->addOne($title);

        return $this->view->render('product.variant.form', [
            'variant' => $variant,
            'new' => $new,
            'title' => $title,
        ]);
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
        $validator = $this->createValidation();

        $validator->field('variant.price')
                  ->required()
                  ->addRule('numeric')
                  ->addRule('positive');

        $validator->field('variant.stock')
                  ->addRule('numeric')
                  ->addRule('greaterthan', null, ['min' => -1]);

        if ($validator->isPassed()) {
            $input = $this->request->getPost('variant');
            $new = !isset($input['id']) || !$input['id'];

            $variantService = $this->getModuleService('variantService');
            $variantService->save($input);
            
            $this->flashBag->set('success', $new ? 'The variant has been created successfully' : 'The variant has been updated successfully');

            if ($new) {
                return $this->json([
                    'redirect' => $this->createUrl('Shop:Admin:ProductVariant@editAction', [$variantService->getLastId()]),
                ]);
            } else {
                return $this->json([
                    'refresh' => true
                ]);
            }

        } else {
            return $this->json([
                'errors' => $validator->getErrors()
            ]);
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

        return $this->json([
            'refresh' => true
        ]);
    }
}
