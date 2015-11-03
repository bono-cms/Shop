<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Shop\Controller\Admin\Product;

use Cms\Controller\Admin\AbstractController;
use Krystal\Tree\AdjacencyList\TreeBuilder;
use Krystal\Tree\AdjacencyList\Render\PhpArray;
use Krystal\Validate\Pattern;

abstract class AbstractProduct extends AbstractController
{
    /**
     * Returns configured validator instance
     * 
     * @param array $input Raw input data
     * @return \Krystal\Validate\ValidatorChain
     */
    final protected function getValidator(array $input)
    {
        return $this->validatorFactory->build(array(
            'input' => array(
                'source' => $input['product'],
                'definition' => array(
                    'title' => new Pattern\Title(),
                    'regular_price' => new Pattern\Price(),
                    'description' => new Pattern\Description()
                )
            )
        ));
    }

    /**
     * Loads shared plugins
     * 
     * @return void
     */
    final protected function loadSharedPlugins()
    {
        $this->view->getPluginBag()
                   ->load(array('preview', $this->getWysiwygPluginName()))
                   ->appendScript('@Shop/admin/product.form.js')
                   ->appendStylesheet('@Shop/admin/product.form.css');
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

    /**
     * Returns shared variables for Add and Edit controllers
     * 
     * @param array $overrides
     * @return array
     */
    final protected function getWithSharedVars(array $overrides)
    {
        $vars = array(
            'categories' => $this->getCategoryManager()->getCategoriesTree(),
            'config' => $this->getModuleService('configManager')->getEntity()
        );

        return array_replace_recursive($vars, $overrides);
    }

    /**
     * Returns product manager
     * 
     * @return \Shop\Service\ProductManager
     */
    final protected function getProductManager()
    {
        return $this->getModuleService('productManager');
    }

    /**
     * Returns product manager
     * 
     * @return \Shop\Service\ProductManager
     */
    final protected function getCategoryManager()
    {
        return $this->getModuleService('categoryManager');
    }

    /**
     * Returns template path
     * 
     * @return string
     */
    final protected function getTemplatePath()
    {
        return 'product.form';
    }
}
