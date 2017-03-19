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
     * @param string $id
     * @return string
     */
    public function approveAction($id)
    {
        if ($this->getOrderManager()->approveById($id)) {
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
        return $this->invokeRemoval('orderManager', $id);
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
