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

final class Currency extends AbstractController
{
    /**
     * Renders the grid
     * 
     * @param \Krystal\Stdlib\VirtualEntity $currency
     * @return string
     */
    private function createGrid(VirtualEntity $currency)
    {
        // Configure breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Shop', 'Shop:Admin:Browser@indexAction')
                                       ->addOne('Currencies');

        return $this->view->render('currencies-grid', array(
            'currency' => $currency,
            'currencies' => $this->getModuleService('currencyManager')->fetchAll()
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
     * Edits the coupon
     * 
     * @param string $id Coupon ID
     * @return string
     */
    public function editAction($id)
    {
        $currencyManager = $this->getModuleService('currencyManager');
        $currency = $currencyManager->fetchById($id);

        if ($currency !== false) {
            return $this->createGrid($currency);
        } else {
            return false;
        }
    }

    /**
     * Delete a currency by its associated ID
     * 
     * @param string $id
     * @return integer
     */
    public function deleteAction($id)
    {
        $currencyManager = $this->getModuleService('currencyManager');
        $currencyManager->deleteById($id);

        $this->flashBag->set('success', 'The currency has been removed successfully');
        return 1;
    }

    /**
     * Saves a coupon
     * 
     * @return string
     */
    public function saveAction()
    {
        $input = $this->request->getPost('currency');

        $formValidator = $this->createValidator(array(
            'input' => array(
                'source' => $input,
                'definition' => array(
                    'code' => array(
                        'required' => true,
                        'rules' => array(
                            'NotEmpty' => array(
                                'message' => 'The currency code is required'
                            )
                        )
                    ),

                    'value' => array(
                        'required' => true,
                        'rules' => array(
                            'NotEmpty' => array(
                                'message' => 'The currency value is required'
                            )
                        )
                    )
                )
            )
        ));

        if ($formValidator->isValid()) {
            // Grab the service
            $currencyManager = $this->getModuleService('currencyManager');

            if ($input['id']) {
                $currencyManager->update($input);
                $this->flashBag->set('success', 'The currency has been updated successfully');

                return 1;
            } else {
                $currencyManager->add($input);
                $this->flashBag->set('success', 'A currency has been added successfully');

                return $currencyManager->getLastId();
            }

        } else {
            return $formValidator->getErrors();
        }
    }
}
