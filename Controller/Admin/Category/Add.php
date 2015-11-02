<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Shop\Controller\Admin\Category;

use Krystal\Stdlib\VirtualEntity;

final class Add extends AbstractCategory
{
    /**
     * Shows adding form
     * 
     * @return string
     */
    public function indexAction()
    {
        $this->loadSharedPlugins();
        $this->loadBreadcrumbs('Add a category');
        $this->view->getPluginBag()->load('preview');

        return $this->view->render($this->getTemplatePath(), array(
            'categories' => $this->getCategoriesTree(),
            'title' => 'Add a category',
            'category' => new VirtualEntity()
        ));
    }

    /**
     * Adds a category
     * 
     * @return string
     */
    public function addAction()
    {
        $formValidator = $this->getValidator($this->request->getPost('category'), $this->request->getFiles());

        if ($formValidator->isValid()) {
            $categoryManager = $this->getCategoryManager();

            if ($categoryManager->add($this->request->getAll())) {
                $this->flashBag->set('success', 'A category has been added successfully');
                return $categoryManager->getLastId();
            }

        } else {
            return $formValidator->getErrors();
        }
    }
}
