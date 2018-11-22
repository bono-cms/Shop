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

final class Attributes extends AbstractController
{
    /**
     * Creates the grid
     * 
     * @param string $groupId
     * @return string
     */
    private function createGrid($groupId)
    {
        // Append a breadcrumb
        $this->view->getBreadcrumbBag()
                   ->addOne('Shop', 'Shop:Admin:Browser@indexAction')
                   ->addOne('Attributes');

        return $this->view->render('attributes/index', array(
            'groups' => $this->getModuleService('attributeGroupManager')->fetchAll(),
            'values' => $this->getModuleService('attributeValueManager')->fetchAllByCategoryId($groupId),
            'groupId' => $groupId
        ));
    }

    /**
     * Renders the group
     * 
     * @param string $groupId Group id
     * @return string
     */
    public function groupAction($groupId)
    {
        return $this->createGrid($groupId);
    }

    /**
     * Renders attribute page
     * 
     * @return string
     */
    public function indexAction()
    {
        $id = $this->getModuleService('attributeGroupManager')->getLastId();
        return $this->createGrid($id);
    }
}
