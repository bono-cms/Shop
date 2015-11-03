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

use Cms\Controller\Admin\AbstractController;
use Krystal\Tree\AdjacencyList\TreeBuilder;
use Krystal\Tree\AdjacencyList\Render\PhpArray;
use Krystal\Validate\Pattern;

abstract class AbstractCategory extends AbstractController
{
    /**
     * Returns prepared a configured validator
     * 
     * @param array $input Raw input data
     * @param array $files
     * @param boolean $edit Whether on edit form
     * @return \Krystal\Validate\ValidatorChain
     */
    final protected function getValidator(array $input, array $files, $edit = false)
    {
        return $this->validatorFactory->build(array(
            'input' => array(
                'source' => $input,
                'definition' => array(
                    'title' => new Pattern\Title()
                )
            )
        ));
    }

    /**
     * Returns template path
     * 
     * @return string
     */
    final protected function getTemplatePath()
    {
        return 'category.form';
    }

    /**
     * Loads shared plugins
     * 
     * @return void
     */
    final protected function loadSharedPlugins()
    {
        $this->loadMenuWidget();

        $this->view->getPluginBag()
                   ->load($this->getWysiwygPluginName())
                   ->appendScript('@Shop/admin/category.form.js');
    }

    /**
     * Returns category manager
     * 
     * @return \Shop\Service\CategoryManager
     */
    final protected function getCategoryManager()
    {
        return $this->getModuleService('categoryManager');
    }
    
    /**
     * Returns a tree of categories
     * 
     * @return array
     */
    final protected function getCategoriesTree()
    {
        $text = sprintf('— %s —', $this->translator->translate('None'));
        return $this->getCategoryManager()->getPromtWithCategoriesTree($text);
    }

    /**
     * Loads breadcrumbs
     * 
     * @param string $title
     * @return void
     */
    final protected function loadBreadcrumbs($title)
    {
        $this->view->getBreadcrumbBag()->addOne('Shop', 'Shop:Admin:Browser@indexAction')
                                       ->addOne($title);
    }
}
