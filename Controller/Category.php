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

final class Category extends AbstractShopController
{
    /**
     * Applies a filter
     * 
     * @return string
     */
    public function filterAction()
    {
        // Request data
        $data = $this->request->getQuery();
        $pageNumber = $this->request->getQuery('page', 1);
        $sort = $this->request->getQuery('sort', null);

        // Services
        $productManager = $this->getModuleService('productManager');
        $paginator = $productManager->getPaginator();

        if (isset($data['attributes'])) {
            $products = $productManager->findByAttributes(
                $data['category_id'], 
                $this->createCustomerId(),
                $data['attributes'], 
                $sort,
                $pageNumber, 
                $this->getPerPageCountGadget()->getPerPageCount()
            );
        } else {

            // If no attributes then filter was discarded
            $products = $productManager->fetchAllPublishedByCategoryIdAndPage(
                $data['category_id'], 
                $pageNumber, 
                $this->getPerPageCountGadget()->getPerPageCount(), 
                $sort,
                null,
                $this->createCustomerId()
            );
        }

        $this->loadSitePlugins();

        return $this->view->disableLayout()->render('partials/category-products', array(
            'products' => $products
        ));
    }

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
            // Indicated that this is a category page
            $category->setCategoryPage(true);

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
                $this->getPerPageCountGadget()->getPerPageCount(), 
                $this->getCategorySortGadget()->getSortOption(),
                null,
                $this->createCustomerId()
            );

            $vars = array(
                'paginator' => $paginator,
                'products' => $products,
                'page' => $category,
                'category' => $category,
                'attributes' => $categoryManager->fetchAttributesById($category->getId(), false),

                // Form gadgets
                'ppc' => $this->getPerPageCountGadget(),
                'sorter' => $this->getCategorySortGadget()
            );

            // Extract child categories
            $children = $categoryManager->fetchChildrenByParentId($id);

            if (!empty($children)) {
                // Then append them to view templates as well
                $vars['categories'] = $children;
            }

            // Persists last category id, for product breadcrumbs
            $this->getCategoryIdKeeper()->persistLastCategoryId($id);

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
     * Changes per page count
     * 
     * @return string
     */
    public function changePerPageCountAction()
    {
        if ($this->request->hasPost('count')) {
            $count = $this->request->getPost('count');
            $this->getPerPageCountGadget()->setPerPageCount($count);

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
            $this->getCategorySortGadget()->setSortOption($sort);

            return '1';
        }
    }
}
