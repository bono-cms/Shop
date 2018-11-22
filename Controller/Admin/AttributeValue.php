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

final class AttributeValue extends AbstractController
{
    /**
     * Renders the form
     * 
     * @param \Krystal\Stdlib\VirtualEntity|array $value
     * @param string $title
     * @return string
     */
    private function createForm($value, $title)
    {
        $new = is_object($value);

        // Append breadcrumbs
        $this->view->getBreadcrumbBag()
                   ->addOne('Shop', 'Shop:Admin:Browser@indexAction')
                   ->addOne('Attributes', 'Shop:Admin:Attributes@indexAction')
                   ->addOne($title);

        return $this->view->render('attribute-value', array(
            'groups' => $this->getModuleService('attributeGroupManager')->fetchList(),
            'value' => $value,
            'new' => $new
        ));
    }

    /**
     * Deletes attribute value by its associated id
     * 
     * @param string $id
     * @return string
     */
    public function deleteAction($id)
    {
        $service = $this->getModuleService('attributeValueManager');
        $service->deleteById($id);

        $this->flashBag->set('success', 'Selected element has been removed successfully');
        return '1';
    }

    /**
     * Renders adding form
     *
     * @return string
     */
    public function addAction()
    {
        return $this->createForm(new VirtualEntity(), 'Add attribute');
    }

    /**
     * Renders edit form
     * 
     * @param string $id
     * @return string
     */
    public function editAction($id)
    {
        $value = $this->getModuleService('attributeValueManager')->fetchById($id, true);

        if ($value !== false) {
            return $this->createForm($value, 'Edit the attribute');
        } else {
            return false;
        }
    }

    /**
     * Save the record
     * 
     * @return string
     */
    public function saveAction()
    {
        $input = $this->request->getPost('value');

        $service = $this->getModuleService('attributeValueManager');
        $service->save($this->request->getPost());

        if (!empty($input['id'])) {
            $this->flashBag->set('success', 'The element has been updated successfully');
            return '1';

        } else {
            $this->flashBag->set('success', 'The element has been created successfully');
            return $service->getLastId();
        }
    }
}
