<?php

/**
 * This file is part of the Bono CMS
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Shop\Controller;

final class Order extends AbstractShopController
{
    /**
     * Makes an orders
     * 
     * @return string
     */
    public function orderAction()
    {
        $validator = $this->createValidation();

        $validator->field('name')
                  ->required()
                  ->addRule('minlength', null, ['min' => 2]);

        $validator->field('phone')
                  ->required();

        $validator->field('email')
                  ->required()
                  ->addRule('email');

        $validator->field('address')
                  ->required();

        $validator->field('captcha')
                  ->addRule('captcha', null, ['expected' => $this->captcha]);

        if ($validator->isPassed()) {
            $input = $this->request->getPost();

            if ($this->makeOrder($input)) {
                // Do not remember current discount for next orders
                $this->getModuleService('couponManager')->clearIfApplied();

                // Success back to client
                $this->flashBag->set('success', 'Your order has been sent! We will contact you soon. Thank you!');

                return $this->json([
                    'success' => true
                ]);
            }

        } else {
            return $this->json([
                'errors' => $validator->getErrors()
            ]);
        }
    }

    /**
     * Makes an order
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    private function makeOrder(array $input)
    {
        $orderManager = $this->getModuleService('orderManager');

        // Override delivery ID with its corresponding name
        $input['delivery'] = $this->getModuleService('deliveryTypeManager')->createDeliveryStatus($input['delivery']);
        $input['customer_id'] = $this->createCustomerId();
        $input['discount'] = $this->getModuleService('couponManager')->getAppliedDiscount();

        // Prepare a message first
        $message = $this->view->renderRaw($this->moduleName, 'messages', 'order', [
            'basketManager' => $this->getModuleService('basketManager'),
            'currency' => $this->getModuleService('configManager')->getEntity()->getCurrency(),
            'input' => $input
        ]);

        if ($orderManager->make($input)) {
            // Prepare the subject
            $subject = $this->translator->translate('You have a new order from %s', $input['name']);

            // Grab mailer service
            $mailer = $this->getService('Cms', 'mailer');
            return $mailer->send($subject, $message);

        } else {
            return false;
        }
    }
}
