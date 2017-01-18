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

final class AttributeValue extends AbstractController
{
    /**
     * Renders the form
     * 
     * @param \Krystal\Stdlib\VirtualEntity $value
     * @param string $title
     * @return string
     */
    private function createForm(VirtualEntity $value, $title)
    {
        // Append breadcrumbs
        $this->view->getBreadcrumbBag()
                   ->addOne('Shop', 'Shop:Admin:Browser@indexAction')
                   ->addOne('Attributes', 'Shop:Admin:Attributes@indexAction')
                   ->addOne($title);

        return $this->view->render('attribute-value', array(
            'groups' => $this->getModuleService('attributeGroupManager')->fetchList(),
            'value' => $value
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
        return $this->invokeRemoval('attributeValueManager', $id);
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
        $value = $this->getModuleService('attributeValueManager')->fetchById($id);

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

        return $this->invokeSave('attributeValueManager', $input['id'], $input, array(
            'input' => array(
                'source' => $input,
                'definition' => array(
                    'name' => new Pattern\Name()
                )
            )
        ));
    }
}
