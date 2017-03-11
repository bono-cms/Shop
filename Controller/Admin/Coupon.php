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

final class Coupon extends AbstractController
{
    /**
     * Renders the grid
     * 
     * @param \Krystal\Stdlib\VirtualEntity $coupon
     * @return string
     */
    private function createGrid(VirtualEntity $coupon)
    {
        // Configure breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Shop', 'Shop:Admin:Browser@indexAction')
                                       ->addOne('Coupons');

        return $this->view->render('coupons-grid', array(
            'coupon' => $coupon,
            'coupons' => $this->getModuleService('couponManager')->fetchAll()
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
        $couponManager = $this->getModuleService('couponManager');
        $coupon = $couponManager->fetchById($id);

        if ($coupon !== false) {
            return $this->createGrid($coupon);
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
        $couponManager = $this->getModuleService('couponManager');
        $couponManager->deleteById($id);

        $this->flashBag->set('success', 'The coupon has been removed successfully');
        return 1;
    }

    /**
     * Saves a coupon
     * 
     * @return string
     */
    public function saveAction()
    {
        $input = $this->request->getPost('coupon');

        $formValidator = $this->createValidator(array(
            'input' => array(
                'source' => $input,
                'definition' => array(
                    'code' => array(
                        'required' => true,
                        'rules' => array(
                            'NotEmpty' => array(
                                'message' => 'The discount code is required'
                            )
                        )
                    ),

                    'percentage' => array(
                        'required' => true,
                        'rules' => array(
                            'NotEmpty' => array(
                                'message' => 'The discount percentage is required'
                            )
                        )
                    )
                )
            )
        ));

        if ($formValidator->isValid()) {
            // Grab the service
            $couponManager = $this->getModuleService('couponManager');

            if ($input['id']) {
                $couponManager->update($input);
                $this->flashBag->set('success', 'The coupon has been updated successfully');

                return 1;
            } else {
                $couponManager->add($input);
                $this->flashBag->set('success', 'A coupon has added successfully');

                return $couponManager->getLastId();
            }

        } else {
            return $formValidator->getErrors();
        }
    }
}
