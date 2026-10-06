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
use Krystal\Stdlib\ArrayUtils;

final class Product extends AbstractController
{
    /**
     * Creates a form
     * 
     * @param \Krystal\Stdlib\VirtualEntity|array $product
     * @param string $title
     * @return string
     */
    private function createForm($product, $title)
    {
        $new = !is_array($product); // Whether it's new product form

        if (!$new) {
            $id = $product[0]->getId();
            $categoryIds = $product[0]->getCategoryIds();
        } else {
            $id = $product->getId();
            $categoryIds = $product->getCategoryIds();
        }

        // Load view plugins
        $this->view->getPluginBag()
                   ->load(['preview', 'chosen', $this->getWysiwygPluginName()])
                   ->appendScript('@Shop/admin/product.form.js')
                   ->appendStylesheet('@Shop/admin/product.form.css');

        // Append breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Shop', 'Shop:Admin:Browser@indexAction')
                                       ->addOne($title);

        // If viewing edit form, then grab product photos as well
        if ($id) {
            $photos = $this->getModuleService('productManager')->fetchAllImagesById($id);
            $attributes = $this->getModuleService('productManager')->fetchAttributesById($id, true);
            $variants = $this->getModuleService('variantService')->fetchAllByProductId($id);
        } else {
            $photos = [];
            $attributes = [];
            $variants = [];
        }

        // If not new, then grab attached specification categories
        if (!$new) {
            foreach ($product as &$item) {
                $item->setSpecCatIds($this->getModuleService('specificationCategoryService')->fetchAttachedByProductId($id));
            }
        }

        return $this->view->render('product.form', [
            'new' => $new,
            'variants' => $variants,
            'names' => $this->getModuleService('productManager')->fetchAllNames(),
            'photos' => $photos,
            'product' => $product,
            'categories' => $this->getModuleService('categoryManager')->getCategoriesTree(),
            'config' => $this->getModuleService('configManager')->getEntity(),
            'attributes' => $attributes,
            'activeAttributes' => $id ? $this->getModuleService('productManager')->findAttributesByProductId($id) : [],
            'specCatIds' => $this->getModuleService('specificationCategoryService')->fetchList(), // Specification category IDs
            'features' => $id ? $this->getModuleService('specificationValueService')->findByProduct($id) : [],
            'brands' => $this->getModuleService('brandService')->fetchList(),
            'attributeGroups' => $this->getModuleService('attributeGroupManager')->fetchList()
        ]);
    }

    /**
     * Renders empty form
     * 
     * @return string
     */
    public function addAction()
    {
        // CMS configuration object
        $config = $this->getService('Cms', 'configManager')->getEntity();

        $product = new VirtualEntity();
        $product->setSeo(true)
                ->setPublished(true)
                ->setSpecialOffer(false)
                ->setChangeFreq($config->getSitemapFrequency())
                ->setPriority($config->getSitemapPriority());

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
        $product = $this->getModuleService('productManager')->fetchById($id, true);

        if ($product) {
            $name = $this->getCurrentProperty($product, 'name');
            return $this->createForm($product, $this->translator->translate('Edit the product "%s"', $name));
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
        if ($this->request->hasPost('batch')) {
            $ids = array_keys($this->request->getPost('batch'));

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

        return $this->json([
            'refresh' => true
        ]);
    }

    /**
     * Save updates from the table
     * 
     * @return string
     */
    public function tweakAction()
    {
        if ($this->request->hasPost('regular_price', 'published', 'seo')) {
            $input = $this->request->getPost();
            unset($input['filter']);

            // Update settings
            $this->getModuleService('productManager')->updateSettings($input);

            $this->flashBag->set('success', 'Settings have been updated successfully');

            return $this->json([
                'refresh' => true
            ]);
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
        $input['product'] = ArrayUtils::arrayRecovery($input['product'], ['category_id'], []);

        $validator = $this->createValidation();

        $validator->field('product.category_id')
                  ->required();

        $validator->field('product.regular_price')
                  ->required()
                  ->addRule('numeric')
                  ->addRule('positive');

        $validator->field('translation.*.name')
                  ->required()
                  ->addRule('minlength', null, ['min' => 2]);

        if ($validator->isPassed()) {
            $service = $this->getModuleService('productManager');

            if (!empty($input['product']['id'])) {
                if ($service->update($this->request->getAll())) {
                    $this->flashBag->set('success', 'The element has been updated successfully');

                    return $this->json([
                        'refresh' => true
                    ]);
                }

            } else {
                if ($service->add($this->request->getAll())) {
                    $this->flashBag->set('success', 'The element has been created successfully');

                    return $this->json([
                        'redirect' => $this->createUrl('Shop:Admin:Product@editAction', [$service->getLastId()]),
                    ]);
                }
            }

        } else {
            return $this->json([
                'errors' => $validator->getErrors()
            ]);
        }
    }
}
