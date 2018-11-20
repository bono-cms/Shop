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

final class Brand extends AbstractController
{
    /**
     * Renders all brands
     * 
     * @return string
     */
    public function indexAction()
    {
        // Append breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Shop', 'Shop:Admin:Browser@indexAction')
                                       ->addOne('Brands');

        return $this->view->render('brand/index', array(
            'brands' => $this->getModuleService('brandService')->fetchAll()
        ));
    }

    /**
     * Creates a form
     * 
     * @param Krystal\Stdlib\VirtualEntity $brand
     * @return string
     */
    private function createForm(VirtualEntity $brand)
    {
        $new = !$brand->getId();

        // Append breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Shop', 'Shop:Admin:Browser@indexAction')
                                       ->addOne('Brands', 'Shop:Admin:Brand@indexAction')
                                       ->addOne($new ? 'Add new brand' : 'Edit brand');

        return $this->view->render('brand/form', array(
            'new' => $new,
            'brand' => $brand
        ));
    }

    /**
     * Renders add form
     * 
     * @return string
     */
    public function addAction()
    {
        return $this->createForm(new VirtualEntity);
    }

    /**
     * Renders edit form
     * 
     * @param int $id
     * @return string
     */
    public function editAction($id)
    {
        $brand = $this->getModuleService('brandService')->fetchById($id);

        if ($brand !== false) {
            return $this->createForm($brand);
        } else {
            return false;
        }
    }

    /**
     * Saves a brand
     * 
     * @return mixed
     */
    public function saveAction()
    {
        $input = $this->request->getPost('brand');
        $new = !$input['id'];

        $brandService = $this->getModuleService('brandService');

        if ($brandService->save($input)) {
            $this->flashBag->set('success', !$new ? 'The element has been updated successfully' : 'The element has been created successfully');
        }

        if ($new) {
            return $brandService->getLastId();
        } else {
            return 1;
        }
    }

    /**
     * Deletes a brand
     * 
     * @param int $id Brand Id
     * @return mixed
     */
    public function deleteAction($id)
    {
        $this->getModuleService('brandService')->deleteById($id);

        $this->flashBag->set('success', 'Selected element has been removed successfully');
        return 1;
    }
}
