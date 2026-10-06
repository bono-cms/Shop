<?php

/**
 * This file is part of the Bono CMS
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Shop\Controller;

final class Checkout extends AbstractShopController
{
    /**
     * Renders checkout page
     * 
     * @param string $id Page id
     * @return string
     */
    public function indexAction($id)
    {
        $pageManager = $this->getService('Pages', 'pageManager');
        $page = $pageManager->fetchById($id);

        if ($page !== false) {
            // Indicated that this is checkout page
            $page->setCheckoutPage(true);

            // Load view plugins
            $this->loadSitePlugins();
            $this->view->getBreadcrumbBag()
                       ->addOne($page->getName());

            return $this->view->render('shop-checkout', [
                'page' => $page,
                'deliveryTypes' => $this->getModuleService('deliveryTypeManager')->fetchAll(),
                'languages' => $pageManager->getSwitchUrls($id, 'Shop:Checkout@indexAction')
            ]);

        } else {
            return false;
        }
    }

    /**
     * Validates the coupon
     * 
     * @return string
     */
    public function couponAction()
    {
        $validator = $this->createValidation();

        $validator->field('code')
                  ->required();

        if ($validator->isPassed()) {
            $code = $this->request->getQuery('code');

            // Grab required services
            $couponManager = $this->getModuleService('couponManager');
            $basketManager = $this->getModuleService('basketManager');

            $discount = $couponManager->applyDiscountByCode($code, $basketManager->getTotalPrice());

            if ($discount !== false) {
                return $discount;
            } else {
                return 0;
            }

        } else {
            return $this->json([
                'errors' => $validator->getErrors()
            ]);
        }
    }
}
