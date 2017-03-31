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

use Shop\Controller\AbstractShopController;
use Krystal\Stdlib\VirtualEntity;
use RuntimeException;
use LogicException;

final class Order extends AbstractShopController
{
    /**
     * {@inheritDoc}
     */
    protected function bootstrap()
    {
        $this->validateCustomerRequirement();
        parent::bootstrap();
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
            'orders' => $this->getModuleService('orderManager')->fetchAllByCustomerId($this->createCustomerId())
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
                'products' => $orderManager->fetchAllDetailsByOrderId($id, $this->createCustomerId()),
                'order' => $order
            ));

        } else {
            return false;
        }
    }
}
