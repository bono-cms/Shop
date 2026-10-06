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

final class OrderStatus extends AbstractController
{
    /**
     * Creates the grid
     * 
     * @param \Krystal\Stdlib\VirtualEntity|array $orderStatus
     * @param string $title Page title
     * @return string
     */
    private function createForm($orderStatus, $title)
    {
        // Whether form is new
        $new = !is_array($orderStatus);

        // Append breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Shop', 'Shop:Admin:Browser@indexAction')
                                       ->addOne('Order statuses', 'Shop:Admin:OrderStatus@indexAction')
                                       ->addOne($title);

        return $this->view->render('order-status/form', [
            'orderStatus' => $orderStatus,
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
                                       ->addOne('Order statuses');

        return $this->view->render('order-status/index', [
            'orderStatuses' => $this->getModuleService('orderStatusManager')->fetchAll()
        ]);
    }

    /**
     * Renders add form
     * 
     * @return string
     */
    public function addAction()
    {
        return $this->createForm(new VirtualEntity(), 'Add order status');
    }

    /**
     * Edit delivery type
     * 
     * @param string $id
     * @return string
     */
    public function editAction($id)
    {
        $orderStatus = $this->getModuleService('orderStatusManager')->fetchById($id, true);

        if ($orderStatus !== false) {
            $name = $this->getCurrentProperty($orderStatus, 'name');
            return $this->createForm($orderStatus, $this->translator->translate('Edit the order status "%s"', $name));
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

        $validator->field('translation.*.name')
                  ->required()
                  ->addRule('minlength', null, ['min' => 2]);

        if ($validator->isPassed()) {
            $input = $this->request->getPost('orderStatus');

            // Grab the service
            $service = $this->getModuleService('orderStatusManager');

            if ($input['id']) {
                $service->save($this->request->getPost());
                $this->flashBag->set('success', 'Order status has been updated successfully');

                return $this->json([
                    'refresh' => true
                ]);
            } else {
                $service->save($this->request->getPost());
                $this->flashBag->set('success', 'Order status has been added successfully');

                return $this->json([
                    'redirect' => $this->createUrl('Shop:Admin:OrderStatus@editAction', [$service->getLastId()]),
                ]);
            }

        } else {
            return $this->json([
                'errors' => $validator->getErrors()
            ]);
        }
    }
}
