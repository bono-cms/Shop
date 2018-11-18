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

final class SpecificationCategory extends AbstractController
{
    /**
     * Renders a form
     * 
     * @param \Krystal\Stdlib\VirtualEntity|array $category
     * @return string
     */
    private function createForm($category)
    {
        $new = is_object($category);

        // Append breadcrumb
        $this->view->getBreadcrumbBag()->addOne('Shop', 'Shop:Admin:Browser@indexAction')
                                       ->addOne('Specifications', $this->createUrl('Shop:Admin:SpecificationItem@indexAction', array(null)))
                                       ->addOne($new ? 'Add new category' : 'Edit the category');
        
        return $this->view->render('specification/category.form', array(
            'category' => $category,
            'new' => $new
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
     * @param int $id Category ID
     * @return string
     */
    public function editAction($id)
    {
        $category = $this->getModuleService('specificationCategoryService')->fetchById($id, true);

        if ($category !== false) {
            return $this->createForm($category);
        } else {
            return false;
        }
    }

    /**
     * Deletes a category
     * 
     * @param int $id Category ID
     * @return mixed
     */
    public function deleteAction($id)
    {
        $this->getModuleService('specificationCategoryService')->deleteById($id);

        $this->flashBag->set('success', 'Selected element has been removed successfully');
        return 1;
    }

    /**
     * Saves category
     * 
     * @return mixed
     */
    public function saveAction()
    {
        $data = $this->request->getPost();

        $new = !$data['category']['id'];
        $specificationCategoryService = $this->getModuleService('specificationCategoryService');

        if ($specificationCategoryService->save($data)) {
            $this->flashBag->set('success', !$new ? 'The element has been updated successfully' : 'The element has been created successfully');
        }

        if ($new) {
            return $specificationCategoryService->getLastId();
        } else {
            return 1;
        }
    }
}
