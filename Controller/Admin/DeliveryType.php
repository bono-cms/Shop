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
     * Creates the form
     * 
     * @param \Krystal\Stdlib\VirtualEntity|array $deliveryType
     * @param string $title Page title
     * @return string
     */
    private function createForm($deliveryType, $title)
    {
        $new = is_object($deliveryType);

        // Configure breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Shop', 'Shop:Admin:Browser@indexAction')
                                       ->addOne('Delivery types', 'Shop:Admin:DeliveryType@indexAction')
                                       ->addOne($title);

        return $this->view->render('delivery-type/form', array(
            'deliveryType' => $deliveryType,
            'new' => $new
        ));
    }

    /**
     * Renders the grid
     * 
     * @return string
     */
    public function indexAction()
    {
        // Configure breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Shop', 'Shop:Admin:Browser@indexAction')
                                       ->addOne('Delivery types');

        return $this->view->render('delivery-type/index', array(
            'deliveryTypes' => $this->getModuleService('deliveryTypeManager')->fetchAll()
        ));
    }

    /**
     * Renders add form
     * 
     * @return string
     */
    public function addAction()
    {
        return $this->createForm(new VirtualEntity(), 'Add new delivery type');
    }

    /**
     * Edit delivery type
     * 
     * @param string $id
     * @return string
     */
    public function editAction($id)
    {
        $deliveryType = $this->getModuleService('deliveryTypeManager')->fetchById($id, true);

        if ($deliveryType !== false) {
            $name = $this->getCurrentProperty($deliveryType, 'name');
            return $this->createForm($deliveryType, $this->translator->translate('Edit the delivery type "%s"', $name));
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
                    'price' => new Pattern\Price()
                )
            )
        ));

        if ($formValidator->isValid()) {
            // Grab the service
            $deliveryTypeManager = $this->getModuleService('deliveryTypeManager');
            $deliveryTypeManager->save($this->request->getPost());

            if ($input['id']) {
                $this->flashBag->set('success', 'Delivery type has been updated successfully');
                return 1;
            } else {
                $this->flashBag->set('success', 'Delivery type has added successfully');
                return $deliveryTypeManager->getLastId();
            }

        } else {
            return $formValidator->getErrors();
        }
    }
}
