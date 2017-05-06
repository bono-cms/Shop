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
use Krystal\Validate\Pattern;

final class AttributeGroup extends AbstractController
{
    /**
     * Creates the attribute form
     * 
     * @param \Krystal\Stdlib\VirtualEntity $entity
     * @param string $title
     * @return string
     */
    private function createForm(VirtualEntity $entity, $title)
    {
        // Append breadcrumbs
        $this->view->getBreadcrumbBag()
                   ->addOne('Shop', 'Shop:Admin:Browser@indexAction')
                   ->addOne('Attributes', 'Shop:Admin:Attributes@indexAction')
                   ->addOne($title);

        return $this->view->render('attribute-group', array(
            'group' => $entity
        ));
    }

    /**
     * Renders adding form
     * 
     * @return string
     */
    public function addAction()
    {
        return $this->createForm(new VirtualEntity(), 'Add a group');
    }

    /**
     * Renders group editing form
     * 
     * @param string $id
     * @return string
     */
    public function editAction($id)
    {
        $group = $this->getModuleService('attributeGroupManager')->fetchById($id);

        if ($group !== false) {
            return $this->createForm($group, 'Edit the group');
        } else {
            return false;
        }
    }

    /**
     * Saves the group
     * 
     * @return boolean
     */
    public function saveAction()
    {
        $input = $this->request->getPost('group');

        $formValidator = $this->createValidator(array(
            'input' => array(
                'source' => $input,
                'definition' => array(
                    'name' => new Pattern\Name()
                )
            )
        ));

        if ($formValidator->isValid()) {
            $service = $this->getModuleService('attributeGroupManager');

            if (!empty($input['id'])) {
                if ($service->update($input)) {
                    $this->flashBag->set('success', 'The element has been updated successfully');
                    return '1';
                }

            } else {
                if ($service->add($input)) {
                    $this->flashBag->set('success', 'The element has been created successfully');
                    return $service->getLastId();
                }
            }

        } else {
            return $formValidator->getErrors();
        }
    }

    /**
     * Deletes a group
     * 
     * @param string $id Group id
     * @return string
     */
    public function deleteAction($id)
    {
        $service = $this->getModuleService('attributeGroupManager');
        $service->deleteById($id);

        $this->flashBag->set('success', 'Selected element has been removed successfully');
        return '1';
    }
}
