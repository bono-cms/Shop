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

final class Edit extends AbstractCategory
{
    /**
     * Shows category edit form
     * 
     * @param string $id
     * @return string
     */
    public function indexAction($id)
    {
        $category = $this->getCategoryManager()->fetchById($id);

        if ($category !== false) {
            $this->loadSharedPlugins();
            $this->loadBreadcrumbs('Edit the category');

            return $this->view->render($this->getTemplatePath(), array(
                'categories' => $this->getCategoriesTree(),
                'title' => 'Edit the category',
                'category' => $category
            ));

        } else {
            return false;
        }
    }

    /**
     * Updates a category
     * 
     * @return string
     */
    public function updateAction()
    {
        $formValidator = $this->getValidator($this->request->getPost('category'), $this->request->getFiles());

        if ($formValidator->isValid()) {
            if ($this->getCategoryManager()->update($this->request->getAll())) {
                $this->flashBag->set('success', 'The category has been updated successfully');
                return '1';
            }

        } else {
            return $formValidator->getErrors();
        }
    }
}
