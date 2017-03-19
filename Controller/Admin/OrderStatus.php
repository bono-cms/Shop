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
use Krystal\Validate\Pattern;

final class OrderStatus extends AbstractController
{
    /**
     * Creates the grid
     * 
     * @param \Krystal\Stdlib\VirtualEntity $orderStatus
     * @return string
     */
    private function createGrid(VirtualEntity $orderStatus)
    {
        // Configure breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Shop', 'Shop:Admin:Browser@indexAction')
                                       ->addOne('Order statuses');

        return $this->view->render('order-statuses-grid', array(
            'orderStatuses' => $this->getModuleService('orderStatusManager')->fetchAll(),
            'orderStatus' => $orderStatus
        ));
    }

    /**
     * Renders the grid
     * 
     * @return string
     */
    public function indexAction()
    {
        return $this->createGrid(new VirtualEntity());
    }

    /**
     * Edit delivery type
     * 
     * @param string $id
     * @return string
     */
    public function editAction($id)
    {
        $entity = $this->getModuleService('orderStatusManager')->fetchById($id);

        if ($entity !== false) {
            return $this->createGrid($entity);
        } else {
            return false;
        }
    }

    /**
     * Delete delivery type by its associated ID
     * 
     * @param string $id
     * @return integer
     */
    public function deleteAction($id)
    {
        $service = $this->getModuleService('orderStatusManager');
        $service->deleteById($id);

        $this->flashBag->set('success', 'Order status has been removed successfully');
        return 1;
    }

    /**
     * Saves the delivery type
     * 
     * @return string
     */
    public function saveAction()
    {
        $input = $this->request->getPost('orderStatus');

        $formValidator = $this->createValidator(array(
            'input' => array(
                'source' => $input,
                'definition' => array(
                    'name' => new Pattern\Name(),
                    'description' => new Pattern\Description()
                )
            )
        ));

        if ($formValidator->isValid()) {
            // Grab the service
            $service = $this->getModuleService('orderStatusManager');

            if ($input['id']) {
                $service->update($input);
                $this->flashBag->set('success', 'Order status has been updated successfully');

                return 1;
            } else {
                $service->add($input);
                $this->flashBag->set('success', 'Order status has been added successfully');

                return $service->getLastId();
            }

        } else {
            return $formValidator->getErrors();
        }
    }
}
