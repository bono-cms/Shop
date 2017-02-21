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

use Krystal\Paginate\PaginatorInterface;
use Krystal\Stdlib\VirtualEntity;

final class Search extends AbstractShopController
{
    /**
     * Tweaks paginator's instance
     * 
     * @param \Krystal\Paginate\PaginatorInterface $paginator
     * @return void
     */
    private function tweakPaginator(PaginatorInterface $paginator)
    {
        $placeholder = '(:var)';

        $url =  $this->createUrl('Shop:Search@searchAction', array('?')) . $this->request->buildQuery(array('page' => $placeholder));
        $url = str_replace(rawurlencode($placeholder), $placeholder, $url);

        $paginator->setUrl($url);
    }

    /**
     * Creates page entity
     * 
     * @return \Krystal\Stdlib\PageEntity
     */
    private function createPageEntity()
    {
        // Shared title
        $title = $this->translator->translate('Search results for "%s"', $this->request->getQuery('keyword'));

        $page = new VirtualEntity();
        $page->setTitle($title)
             ->setName($title)
             ->setSeo(false);

        return $page;
    }

    /**
     * Load plugins
     * 
     * @return string
     */
    private function loadPlugins()
    {
        $this->loadSitePlugins();

        // Append breadcrumbs now
        $this->view->getBreadcrumbBag()
                   ->addOne($this->translator->translate('Search'));
    }

    /**
     * Search by product keyword
     * 
     * @return string
     */
    public function searchAction()
    {
        if ($this->request->hasQuery('keyword')) {
            // Page number
            $pageNumber = $this->request->hasQuery('page') ? $this->request->getQuery('page') : 1;

            $productManager = $this->getModuleService('productManager');

            // Grab and configure pagination component
            $paginator = $productManager->getPaginator();
            $this->tweakPaginator($paginator);

            // Finally fetch products
            $products = $productManager->fetchAllPublishedByCategoryIdAndPage(
                null, 
                $pageNumber, 
                $this->getPerPageCountGadget()->getPerPageCount(), 
                $this->getCategorySortGadget()->getSortOption(),
                $this->request->getQuery('keyword')
            );

            // Load site plugins
            $this->loadPlugins();
            $page = $this->createPageEntity();

            // Done. Now just render them
            return $this->view->render('shop-category', array(
                'paginator' => $paginator,
                'products' => $products,
                'page' => $page,
                'category' => $page,

                // Form gadgets
                'ppc' => $this->getPerPageCountGadget(),
                'sorter' => $this->getCategorySortGadget()
            ));

        } else {
            return false;
        }
    }
}
