<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Shop\Controller\Customer;

use Site\Controller\AbstractController;
use Krystal\Stdlib\VirtualEntity;
use RuntimeException;
use LogicException;

final class Order extends AbstractController
{
    /**
     * {@inheritDoc}
     */
    protected function bootstrap()
    {
        // Make sure "Members" module is loaded
        if (!$this->moduleManager->isLoaded('Members')) {
            throw new RuntimeException('Customer order controller required "Members" module to be properly installed');
        }

        // Make sure, the customer is logged in
        if (!$this->getService('Members', 'memberManager')->isLoggedIn()) {
            throw new LogicException('A customer must be logged in to access their orders');
        }

        parent::bootstrap();
    }

    /**
     * Returns customer ID
     * 
     * @return string
     */
    private function getCustomerId()
    {
        return $this->getService('Members', 'memberManager')->getMember('id');
    }

    /**
     * List all orders
     * 
     * @return string
     */
    public function listAction()
    {
        // Append a breadcrumb
        $this->view->getBreadcrumbBag()
                   ->addOne('Orders');

        // Configure page entity
        $page = new VirtualEntity();
        $page->setTitle($this->translator->translate('My orders'))
             ->setSeo(false);

        $this->loadSitePlugins();

        return $this->view->render('shop-order-list', array(
            'page' => $page,
            'orders' => $this->getModuleService('orderManager')->fetchAllByCustomerId($this->getCustomerId())
        ));
    }

    /**
     * Render order details
     * 
     * @param string $id Order ID
     * @return string
     */
    public function detailAction($id)
    {
        $orderManager = $this->getModuleService('orderManager');
        $order = $orderManager->fetchById($id);

        if ($order !== false) {
            // Append breadcrumbs
            $this->view->getBreadcrumbBag()
                       ->addOne('Orders', 'Shop:Customer:Order@listAction')
                       ->addOne('Order details');

            $page = new VirtualEntity();
            $page->setTitle(sprintf('%s #%s', $this->translator->translate('View order details'), $id))
                 ->setSeo(false);

            $this->loadSitePlugins();

            return $this->view->render('shop-order-details', array(
                'page' => $page,
                'products' => $orderManager->fetchAllDetailsByOrderId($id, $this->getCustomerId()),
                'order' => $order
            ));

        } else {
            return false;
        }
    }
}
