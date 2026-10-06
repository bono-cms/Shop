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

        return $this->view->render('coupons-grid', [
            'coupon' => $coupon,
            'coupons' => $this->getModuleService('couponManager')->fetchAll()
        ]);
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

        return $this->json([
            'refresh' => true
        ]);
    }

    /**
     * Saves a coupon
     * 
     * @return string
     */
    public function saveAction()
    {
        $validator = $this->createValidation();

        $validator->field('coupon.code')
                  ->required();

        $validator->field('coupon.percentage')
                  ->required()
                  ->addRule('numeric')
                  ->addRule('between', null, ['min' => 1, 'max' => 100]);

        if ($validator->isPassed()) {
            $input = $this->request->getPost('coupon');

            // Grab the service
            $couponManager = $this->getModuleService('couponManager');

            if ($input['id']) {
                $couponManager->update($input);
                $this->flashBag->set('success', 'The coupon has been updated successfully');

                return $this->json([
                    'refresh' => true
                ]);
            } else {
                $couponManager->add($input);
                $this->flashBag->set('success', 'A coupon has added successfully');

                return $this->json([
                    'redirect' => $this->createUrl('Shop:Admin:Coupon@editAction', [$couponManager->getLastId()]),
                ]);
            }

        } else {
            return $this->json([
                'errors' => $validator->getErrors()
            ]);
        }
    }
}
