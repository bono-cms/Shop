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
use Krystal\Db\Filter\QueryContainer;

final class Order extends AbstractController
{
    /**
     * Create and send confirmation message
     * 
     * @param string $receiver Email of receiver
     * @param string $name Customer name
     * @return string
     */
    private function sendConfirmationMessage($receiver, $name)
    {
        // Prepare a message first
        $body = $this->view->renderRaw($this->moduleName, 'messages', 'order-approved', array(
            'name' => $name
        ));

        // Grab the service and do email
        $mailer = $this->getService('Cms', 'mailer');
        $mailer->sendTo($receiver, $this->translator->translate('Your order has been approved'), $body);
    }

    /**
     * Applies the filter
     * 
     * @return string
     */
    public function filterAction()
    {
        $orders = $this->getFilter($this->getOrderManager(), $this->createUrl('Shop:Admin:Order@filterAction', array(null)));

        if ($orders !== false) {
            return $this->createGrid($orders);
        } else {
            return $this->indexAction();
        }
    }

    /**
     * Shows order's grid
     * 
     * @param string $page Current page number
     * @return string
     */
    public function indexAction($page = 1)
    {
        $orders = $this->getOrderManager()->fetchAllByPage($page, $this->getSharedPerPageCount());
        $url = $this->createUrl('Shop:Admin:Order@indexAction', array(), 1);

        return $this->createGrid($orders, $url);
    }

    /**
     * Approves an order by its id
     * 
     * @param string $id Order ID
     * @return string
     */
    public function approveAction($id)
    {
        $orderManager = $this->getOrderManager();
        $order = $orderManager->fetchById($id);

        if ($order !== false && $orderManager->approveById($id)) {

            // Notify a customer via their email
            $this->sendConfirmationMessage($order->getEmail(), $order->getName());

            $this->flashBag->set('success', 'Selected order marked as approved now');
            return '1';
        }
    }

    /**
     * Save incoming configuration
     * 
     * @return string
     */
    public function tweakAction()
    {
        if ($this->request->hasPost('order_status_id')) {
            $relations = $this->request->getPost('order_status_id');

            // Update relations
            $this->getOrderManager()->updateOrderStatuses($relations);

            $this->flashBag->set('success', 'Settings have been saved successfully');
            return 1;
        }
    }

    /**
     * Deletes an order by its associated id
     * 
     * @param string $id
     * @return string
     */
    public function deleteAction($id)
    {
        $service = $this->getModuleService('orderManager');

        // Batch removal
        if ($this->request->hasPost('toDelete')) {
            $ids = array_keys($this->request->getPost('toDelete'));

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

        return '1';
    }

    /**
     * Shows details for a given order id
     * 
     * @param string $id Order id
     * @return string
     */
    public function detailsAction($id)
    {
        $details = $this->getOrderManager()->fetchAllDetailsByOrderId($id);

        return $this->view->disableLayout()->render('order-details', array(
            'id' => $id,
            'currency' => $this->getConfig()->getCurrency(),
            'summary' => $this->getOrderManager()->createSummary($details),
            'details' => $details
        ));
    }

    /**
     * Creates a grid
     * 
     * @param array $orders
     * @param string $url
     * @return string
     */
    private function createGrid(array $orders, $url = null)
    {
        $paginator = $this->getOrderManager()->getPaginator();

        if ($url !== null) {
            $paginator->setUrl($url);
        }

        // Load view plugins
        $this->view->getPluginBag()
                   ->load('datepicker')
                   ->appendScript('@Shop/admin/orders.js');

        // Append breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Shop', 'Shop:Admin:Browser@indexAction')
                                       ->addOne('List of orders');

        return $this->view->render('orders', array(
            'orderStatuses' => $this->getModuleService('orderStatusManager')->fetchList(),
            'orders' => $orders,
            'paginator' => $paginator,
            'config' => $this->getConfig(),
            'title' => 'Orders',
            'filter' => new QueryContainer($this->request->getQuery(), $this->createUrl('Shop:Admin:Order@filterAction', array(null)))
        ));
    }

    /**
     * Returns order manager
     * 
     * @return \Shop\Service\OrderManager
     */
    private function getOrderManager()
    {
        return $this->getModuleService('orderManager');
    }

    /**
     * Returns configuration entity
     * 
     * @return \Krystal\Stdlib\VirtualEntity
     */
    private function getConfig()
    {
        return $this->getModuleService('configManager')->getEntity();
    }
}
