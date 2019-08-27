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

use Krystal\Stdlib\VirtualEntity;
use Cms\Controller\Admin\AbstractController;

final class AttributeGroup extends AbstractController
{
    /**
     * Creates the attribute form
     * 
     * @param \Krystal\Stdlib\VirtualEntity|array $group
     * @param string $title Page title
     * @return string
     */
    private function createForm($group, $title)
    {
        $new = is_object($group);

        // Append breadcrumbs
        $this->view->getBreadcrumbBag()
                   ->addOne('Shop', 'Shop:Admin:Browser@indexAction')
                   ->addOne('Attributes', 'Shop:Admin:Attributes@indexAction')
                   ->addOne($title);

        return $this->view->render('attributes/group', array(
            'group' => $group,
            'new' => $new
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
        $group = $this->getModuleService('attributeGroupManager')->fetchById($id, true);

        if ($group !== false) {
            $name = $this->getCurrentProperty($group, 'name');
            return $this->createForm($group, $this->translator->translate('Edit the attribute group "%s"', $name));
        } else {
            return false;
        }
    }

    /**
     * Saves the group
     * 
     * @return int
     */
    public function saveAction()
    {
        $input = $this->request->getPost('group');

        $service = $this->getModuleService('attributeGroupManager');
        $service->save($this->request->getPost());

        if (!empty($input['id'])) {
            $this->flashBag->set('success', 'The element has been updated successfully');
            return '1';
        } else {
            $this->flashBag->set('success', 'The element has been created successfully');
            return $service->getLastId();
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
