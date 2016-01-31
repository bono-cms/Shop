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
use Krystal\Tree\AdjacencyList\TreeBuilder;
use Krystal\Tree\AdjacencyList\Render\PhpArray;
use Krystal\Validate\Pattern;
use Krystal\Stdlib\VirtualEntity;

final class Category extends AbstractController
{
    /**
     * Returns category manager
     * 
     * @return \Shop\Service\CategoryManager
     */
    private function getCategoryManager()
    {
        return $this->getModuleService('categoryManager');
    }

    /**
     * Returns a tree of categories
     * 
     * @return array
     */
    private function getCategoriesTree()
    {
        $text = sprintf('— %s —', $this->translator->translate('None'));
        return $this->getCategoryManager()->getPromtWithCategoriesTree($text);
    }

    /**
     * Creates a form
     * 
     * @param \Krystal\Stdlib\VirtualEntity $category
     * @param string $title
     * @return string
     */
    private function createForm(VirtualEntity $category, $title)
    {
        // Load view plugins
        $this->loadMenuWidget();

        $this->view->getPluginBag()
                   ->load($this->getWysiwygPluginName())
                   ->appendScript('@Shop/admin/category.form.js');

        // Append breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Shop', 'Shop:Admin:Browser@indexAction')
                                       ->addOne($title);

        return $this->view->render('category.form', array(
            'categories' => $this->getCategoriesTree(),
            'category' => $category
        ));
    }

    /**
     * Renders empty form
     * 
     * @return string
     */
    public function addAction()
    {
        $this->view->getPluginBag()
                   ->load('preview');

        return $this->createForm(new VirtualEntity(), 'Add a category');
    }

    /**
     * Renders edit form
     * 
     * @param string $id
     * @return string
     */
    public function editAction($id)
    {
        $category = $this->getCategoryManager()->fetchById($id);

        if ($category !== false) {
            return $this->createForm($category, 'Edit the category');
        } else {
            return false;
        }
    }

    /**
     * Deletes a category by its associated id
     * 
     * @return string
     */
    public function deleteAction()
    {
        if ($this->request->hasPost('id')) {
            $id = $this->request->getPost('id');

            $categoryManager = $this->getModuleService('categoryManager');
            if ($categoryManager->removeById($id)) {
                $this->flashBag->set('success', 'The category has been removed successfully');
                return '1';
            }
        }
    }

    /**
     * Persists a category
     * 
     * @return string
     */
    public function saveAction()
    {
        $input = $this->request->getPost('category');

        $formValidator = $this->validatorFactory->build(array(
            'input' => array(
                'source' => $input,
                'definition' => array(
                    'title' => new Pattern\Title()
                )
            )
        ));

        if ($formValidator->isValid()) {
            $categoryManager = $this->getCategoryManager();

            if ($input['id']) {
                if ($categoryManager->update($this->request->getAll())) {
                    $this->flashBag->set('success', 'The category has been updated successfully');
                    return '1';
                }
                
            } else {
                if ($categoryManager->add($this->request->getAll())) {
                    $this->flashBag->set('success', 'A category has been added successfully');
                    return $categoryManager->getLastId();
                }
            }

        } else {
            return $formValidator->getErrors();
        }
    }
}
