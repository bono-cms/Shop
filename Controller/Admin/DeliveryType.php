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

final class DeliveryType extends AbstractController
{
    /**
     * Creates the grid
     * 
     * @param \Krystal\Stdlib\VirtualEntity $deliveryType
     * @return string
     */
    private function createGrid(VirtualEntity $deliveryType)
    {
        // Configure breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Shop', 'Shop:Admin:Browser@indexAction')
                                       ->addOne('Delivery types');

        return $this->view->render('delivery-type-grid', array(
            'deliveryTypes' => $this->getModuleService('deliveryTypeManager')->fetchAll(),
            'deliveryType' => $deliveryType
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
        $deliveryType = $this->getModuleService('deliveryTypeManager')->fetchById($id);

        if ($deliveryType !== false) {
            return $this->createGrid($deliveryType);
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
        $deliveryTypeManager = $this->getModuleService('deliveryTypeManager');
        $deliveryTypeManager->deleteById($id);

        $this->flashBag->set('success', 'Delivery type has been removed successfully');
        return 1;
    }

    /**
     * Saves the delivery type
     * 
     * @return string
     */
    public function saveAction()
    {
        $input = $this->request->getPost('deliveryType');

        $formValidator = $this->createValidator(array(
            'input' => array(
                'source' => $input,
                'definition' => array(
                    'name' => new Pattern\Name(),
                    'price' => new Pattern\Price()
                )
            )
        ));

        if ($formValidator->isValid()) {
            // Grab the service
            $deliveryTypeManager = $this->getModuleService('deliveryTypeManager');

            if ($input['id']) {
                $deliveryTypeManager->update($input);
                $this->flashBag->set('success', 'Delivery type has been updated successfully');

                return 1;
            } else {
                $deliveryTypeManager->add($input);
                $this->flashBag->set('success', 'Delivery type has added successfully');

                return $deliveryTypeManager->getLastId();
            }

        } else {
            return $formValidator->getErrors();
        }
    }
}
