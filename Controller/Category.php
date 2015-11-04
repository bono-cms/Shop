<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Shop\Controller;

use Shop\Service\CategorySortProvider;
use Krystal\Form\Providers\PerPageCount;

final class Category extends AbstractShopController
{
    /**
     * Renders a category by its associated id
     * 
     * @param string $id Category id
     * @param integer $pageNumber Current page number
     * @param string $code Optional language code
     * @param string $slug Optional slug
     * @return string
     */
    public function indexAction($id = false, $pageNumber = 1, $code = null, $slug = null)
    {
        $categoryManager = $this->getModuleService('categoryManager');
        $category = $categoryManager->fetchById($id);

        // If $category isn't false, then right id is supplied, $category itself a bag
        if ($category !== false) {
            $this->loadPlugins($categoryManager->getBreadcrumbs($category));

            $productManager = $this->getModuleService('productManager');

            // Grab and configure pagination component
            $paginator = $productManager->getPaginator();

            // If $slug isn't null by type, then this controller is invoked manually from Site module
            if ($slug !== null) {
                $this->preparePaginator($paginator, $code, $slug, $pageNumber);
            }

            // Finally fetch products
            $products = $productManager->fetchAllPublishedByCategoryIdAndPage(
                $id, 
                $pageNumber, 
                $this->getPerPageCountProvider()->getPerPageCount(), 
                $this->getCategorySortProvider()->getSortOption()
            );

            $vars = array(
                'paginator' => $paginator,
                'products' => $products,
                'page' => $category,
                'category' => $category,

                // Form gadgets
                'ppc' => $this->getPerPageCountProvider(),
                'sorter' => $this->getCategorySortProvider()
            );

            // Extract child categories
            $children = $categoryManager->fetchChildrenByParentId($id);

            if (!empty($children)) {
                // Then append them to view templates as well
                $vars['categories'] = $children;
            }

            // Done. Now just render them
            return $this->view->render('shop-category', $vars);

        } else {

            // Returning false will trigger 404 error automatically
            return false;
        }
    }

    /**
     * Loads category plugins
     * 
     * @param array $breadcrumbs
     * @return void
     */
    private function loadPlugins(array $breadcrumbs)
    {
        $this->loadSitePlugins();

        // Append breadcrumbs now
        $this->view->getBreadcrumbBag()
                   ->add($breadcrumbs);
    }

    /**
     * Returns per page count
     * 
     * @return \Shop\Service\PerPageCountProvider
     */
    private function getPerPageCountProvider()
    {
        static $provider = null;

        if (is_null($provider)) {
            $provider = new PerPageCount($this->sessionBag, 'cat_pc', 5);
        }

        return $provider;
    }

    /**
     * Returns prepared category sort provider
     * 
     * @return \Krystal\Service\CategorySortProvider
     */
    private function getCategorySortProvider()
    {
        // To cache method calls, so that returned instance instantiated only once
        static $provider = null;

        if (is_null($provider)) {
            $provider = new CategorySortProvider($this->sessionBag);
        }

        return $provider;
    }

    /**
     * Changes per page count
     * 
     * @return string
     */
    public function changePerPageCountAction()
    {
        if ($this->request->hasPost('count')) {
            $count = $this->request->getPost('count');
            $this->getPerPageCountProvider()->setPerPageCount($count);

            return '1';
        }
    }

    /**
     * Changes the sort order
     * 
     * @return string
     */
    public function changeSortAction()
    {
        if ($this->request->hasPost('sort')) {
            $sort = $this->request->getPost('sort');
            $this->getCategorySortProvider()->setSortOption($sort);

            return '1';
        }
    }
}
