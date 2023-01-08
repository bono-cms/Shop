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
use Krystal\Db\Filter\QueryContainer;

final class Browser extends AbstractController
{
    /**
     * Shows a table
     * 
     * @param integer $page Current page
     * @return string
     */
    public function indexAction($page = 1)
    {
        $products = $this->getFilter($this->getProductManager());

        return $this->createGrid($products, null);
    }

    /**
     * Displays products by category id
     * 
     * @param string $id Category id
     * @param integer $page
     * @return string
     */
    public function categoryAction($id, $page = 1)
    {
        $products = $this->getProductManager()->fetchAllByPage($page, $this->getSharedPerPageCount(), $id);
        $url = $this->createUrl('Shop:Admin:Browser@categoryAction', array($id), 1);

        return $this->createGrid($products, $url, $id);
    }

    /**
     * Creates a grid
     * 
     * @param array $products
     * @param string $categoryId
     * @return string
     */
    private function createGrid(array $products, $categoryId)
    {
        $paginator = $this->getProductManager()->getPaginator();

        // Load view plugins
        $this->view->getPluginBag()
                   ->load('datepicker')
                   ->load('lightbox')
                   ->appendScript('@Shop/admin/browser.js');

        // Append a breadcrumb
        $this->view->getBreadcrumbBag()
                   ->addOne('Shop');

        return $this->view->render('browser', array(
            'newOrdersCount' => $this->getModuleService('orderManager')->countUnapproved(),
            'products' => $products,
            'paginator' => $paginator,
            'categoryId' => $categoryId,
            'categories' => $this->getModuleService('categoryManager')->getCategoriesTree(true),
            'filter' => new QueryContainer($this->request->getQuery(), $this->createUrl('Shop:Admin:Browser@filterAction', array(null))),
            'query' => $this->request->getQuery()
        ));
    }

    /**
     * Returns prepared product manager
     * 
     * @return \Shop\Service\ProductManager
     */
    private function getProductManager()
    {
        return $this->getModuleService('productManager');
    }
}
