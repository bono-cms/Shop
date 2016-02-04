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
    const FILTER_ROUTE = '/admin/module/shop/filter/';

    /**
     * Applies the filter
     * 
     * @return string
     */
    public function filterAction()
    {
        $records = $this->getFilter($this->getProductManager(), self::FILTER_ROUTE);

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
        $products = $this->getProductManager()->fetchAllByPage($page, $this->getSharedPerPageCount());
        $url = '/admin/module/shop/page/(:var)';

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
        $products = $this->getProductManager()->fetchAllByCategoryIdAndPage($id, $page, $this->getSharedPerPageCount());
        $url = '/admin/module/shop/category/'.$id. '/page/(:var)';

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
    private function createGrid(array $products, $url = null, $categoryId)
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
            'products' => $products,
            'paginator' => $paginator,
            'categoryId' => $categoryId,
            'taskManager' => $this->getModuleService('taskManager'),
            'categories' => $this->getModuleService('categoryManager')->getCategoriesTree(),
            'filter' => new QueryContainer($this->request->getQuery(), self::FILTER_ROUTE)
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
