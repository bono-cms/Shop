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
use Krystal\Form\Gadget\LastCategoryKeeper;

final class SpecificationItem extends AbstractController
{
    /**
     * Renders grid
     * 
     * @param int $categoryId Optional category ID filter
     * @return string
     */
    public function indexAction($categoryId)
    {
        $categoryId = $this->getCategoryId($categoryId);

        // Append breadcrumb
        $this->view->getBreadcrumbBag()->addOne('Shop', 'Shop:Admin:Browser@indexAction')
                                       ->addOne('Specifications');

        return $this->view->render('specification/index', array(
            'categories' => $this->getModuleService('specificationCategoryService')->fetchAll(),
            'categoryId' => $categoryId,
            'items' => $this->getModuleService('specificationItemService')->fetchAll($categoryId)
        ));
    }

    /**
     * Returns current category ID
     * 
     * @param mixed $value Current raw category ID
     * @return mixed
     */
    private function getCategoryId($value)
    {
        // Last category ID keeper
        $keeper = new LastCategoryKeeper($this->sessionBag, 'last_shop_item_category_id');

        // If not provided, then try to get previous one if available. If not, then fallback to last category
        if (!$value) {
            if ($keeper->hasLastCategoryId()) {
                $value = $keeper->getLastCategoryId();
            } else {
                $value = $this->getModuleService('specificationCategoryService')->getLastId();
                $keeper->persistLastCategoryId($value);
            }
        } else {
            // Category ID is provided, so save it
            $keeper->persistLastCategoryId($value);
        }

        return $value;
    }

    /**
     * Renders a form
     * 
     * @param \Krystal\Stdlib\VirtualEntity|array $category
     * @return string
     */
    private function createForm($item)
    {
        $new = is_object($item);

        // Append breadcrumb
        $this->view->getBreadcrumbBag()->addOne('Shop', 'Shop:Admin:Browser@indexAction')
                                       ->addOne('Specifications', $this->createUrl('Shop:Admin:SpecificationItem@indexAction', array(null)))
                                       ->addOne($new ? 'Add new item' : 'Edit the item');
        
        return $this->view->render('specification/item.form', array(
            'item' => $item,
            'new' => $new,
            'categories' => $this->getModuleService('specificationCategoryService')->fetchList()
        ));
    }

    /**
     * Renders add form
     * 
     * @return string
     */
    public function addAction()
    {
        return $this->createForm(new VirtualEntity());
    }

    /**
     * Renders edit form
     * 
     * @param int $id Item ID
     * @return string
     */
    public function editAction($id)
    {
        $item = $this->getModuleService('specificationItemService')->fetchById($id, true);

        if ($item !== false) {
            return $this->createForm($item);
        } else {
            return false;
        }
    }

    /**
     * Deletes an item
     * 
     * @param int $id Category ID
     * @return mixed
     */
    public function deleteAction($id)
    {
        $this->getModuleService('specificationItemService')->deleteById($id);

        $this->flashBag->set('success', 'Selected element has been removed successfully');
        return 1;
    }

    /**
     * Saves item
     * 
     * @return mixed
     */
    public function saveAction()
    {
        $data = $this->request->getPost();

        $new = !$data['item']['id'];
        $specificationItemService = $this->getModuleService('specificationItemService');
        
        if ($specificationItemService->save($data)) {
            $this->flashBag->set('success', !$new ? 'The element has been updated successfully' : 'The element has been created successfully');
        }

        if ($new) {
            return $specificationItemService->getLastId();
        } else {
            return 1;
        }
    }
}
