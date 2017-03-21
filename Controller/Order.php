<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Shop\Controller;

use Krystal\Validate\Pattern;

final class Order extends AbstractShopController
{
    /**
     * Makes an orders
     * 
     * @return string
     */
    public function orderAction()
    {
        $input = $this->request->getPost();

        // Create form validator
        $formValidator = $this->createValidator(array(
            'input' => array(
                'source' => $input,
                'definition' => array(
                    'name' => new Pattern\Name(),
                    'phone' => new Pattern\Phone(),
                    'email' => new Pattern\Email(),
                    'address' => new Pattern\Address(),
                    'comment' => new Pattern\Comment(),
                    'captcha' => new Pattern\Captcha($this->captcha)
                )
            )
        ));

        if ($formValidator->isValid()) {
            if ($this->makeOrder($input)) {
                $this->flashBag->set('success', 'Your order has been sent! We will contact you soon. Thank you!');
                return '1';
            }

        } else {
            return $formValidator->getErrors();
        }
    }

    /**
     * Creates customer ID if possible
     * 
     * @return string
     */
    private function createCustomerId()
    {
        if ($this->moduleManager->isLoaded('Members')) {
            $memberManager = $this->getService('Members', 'memberManager');
            return $memberManager->getMember('id');
        } else {
            return null;
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
        $message = $this->view->renderRaw($this->moduleName, 'messages', 'order', array(
            'basketManager' => $this->getModuleService('basketManager'),
            'currency' => $this->getModuleService('configManager')->getEntity()->getCurrency(),
            'input' => $input
        ));

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
