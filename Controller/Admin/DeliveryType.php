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

        return $this->view->render('delivery-type/form', [
            'deliveryType' => $deliveryType,
            'new' => $new
        ]);
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

        return $this->view->render('delivery-type/index', [
            'deliveryTypes' => $this->getModuleService('deliveryTypeManager')->fetchAll()
        ]);
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

        return $this->json([
            'refresh' => true
        ]);
    }

    /**
     * Saves the delivery type
     * 
     * @return string
     */
    public function saveAction()
    {
        $validator = $this->createValidation();

        $validator->field('deliveryType.price')
                  ->required()
                  ->addRule('numeric');

        $validator->field('translation.*.name')
                  ->required()
                  ->addRule('minlength', null, ['min' => 2]);

        if ($validator->isPassed()) {
            $input = $this->request->getPost('deliveryType');

            // Grab the service
            $deliveryTypeManager = $this->getModuleService('deliveryTypeManager');
            $deliveryTypeManager->save($this->request->getPost());

            if ($input['id']) {
                $this->flashBag->set('success', 'Delivery type has been updated successfully');

                return $this->json([
                    'refresh' => true
                ]);
            } else {
                $this->flashBag->set('success', 'Delivery type has added successfully');

                return $this->json([
                    'redirect' => $this->createUrl('Shop:Admin:DeliveryType@editAction', [$deliveryTypeManager->getLastId()]),
                ]);
            }

        } else {
            return $this->json([
                'errors' => $validator->getErrors()
            ]);
        }
    }
}
