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
     * Applies the filter
     * 
     * @return string
     */
    public function filterAction()
    {
        $records = $this->getFilter($this->getProductManager(), $this->createUrl('Shop:Admin:Browser@filterAction', array(null)));

        if ($records !== false) {
            return $this->createGrid($records, null, null);
        } else {
            return $this->indexAction();
        }
    }

    /**
     * Shows a table
     * 
     * @param integer $page Current page
     * @return string
     */
    public function indexAction($page = 1)
    {
        $products = $this->getProductManager()->fetchAllByPage($page, $this->getSharedPerPageCount(), null);
        $url = $this->createUrl('Shop:Admin:Browser@indexAction', array(), 1);

        return $this->createGrid($products, $url, null);
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
     * @param string $url
     * @param string $categoryId
     * @return string
     */
    private function createGrid(array $products, $url, $categoryId)
    {
        $paginator = $this->getProductManager()->getPaginator();

        if ($url !== null) {
            $paginator->setUrl($url);
        }

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
