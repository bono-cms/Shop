<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

return array(
    
    '/module/shop/stokes' => array(
        'controller' => 'Stokes@indexAction'
    ),
    
    '/module/shop/category/(:var)' => array(
        'controller' => 'Category@indexAction'
    ),
    
    '/module/shop/product/(:var)' => array(
        'controller' => 'Product@indexAction'
    ),

    '/module/shop/basket' => array(
        'controller' => 'Basket@indexAction'
    ),
    
    '/module/shop/basket/add' => array(
        'controller' => 'Basket@addAction'
    ),
    
    '/module/shop/basket/get-stat' => array(
        'ajax' => true,
        'controller' => 'Basket@getStatAction'
    ),
    
    '/module/shop/basket/re-count' => array(
        //'ajax' => true,
        'controller' => 'Basket@recountAction'
    ),
    
    '/module/shop/basket/delete' => array(
        'controller' => 'Basket@deleteAction'
    ),
    
    '/module/shop/basket/clear' => array(
        'controller' => 'Basket@clearAction'
    ),
    
    
    //---- Orders
    '/module/shop/basket/order' => array(
        'controller' => 'Order@orderAction'
    ),
    
    '/module/shop/basket/order/delete' => array(
        'controller' => 'Admin:Order@deleteAction'
    ),
    
    '/module/shop/basket/order/approve' => array(
        'controller' => 'Admin:Order@approveAction'
    ),
    
    '/admin/module/shop/orders' => array(
        'controller' => 'Admin:Order@indexAction'
    ),
    
    '/admin/module/shop/orders/filter/(:var)' => array(
        'controller' => 'Admin:Order@filterAction'
    ),

    '/admin/module/shop/orders/page/(:var)' => array(
        'controller' => 'Admin:Order@indexAction'
    ),
    
    '/admin/module/shop/orders/details/(:var)' => array(
        'controller' => 'Admin:Order@detailsAction'
    ),
    
    //---- Orders
    
    
    '/module/shop/category/do/change-per-page-count' => array(
        'controller' => 'Category@changePerPageCountAction'
    ),
    
    '/module/shop/category/do/change-sort-action' => array(
        'controller' => 'Category@changeSortAction'
    ),
    
    // ------------------------------------------
    
    
    '/admin/module/shop/statistic' => array(
        'controller' => 'Admin:Statistic@indexAction'
    ),
    
    '/admin/module/shop/category/add' => array(
        'controller' => 'Admin:Category@addAction'
    ),
    
    '/admin/module/shop/category/edit/(:var)' => array(
        'controller' => 'Admin:Category@editAction'
    ),
    
    '/admin/module/shop/category/save' => array(
        'controller' => 'Admin:Category@saveAction',
        'disallow' => array('guest')
    ),
    
    // For viewing a category
    '/admin/module/shop/category/(:var)' => array(
        'controller' => 'Admin:Browser@categoryAction'
    ),
    
    '/admin/module/shop/category/(:var)/page/(:var)' => array(
        'controller' => 'Admin:Browser@categoryAction'
    ),
    
    '/admin/module/shop/category/do/delete' => array(
        'controller' => 'Admin:Category@deleteAction',
        'disallow' => array('guest')
    ),
    
    '/admin/module/shop' => array(
        'controller' => 'Admin:Browser@indexAction'
    ),

    '/admin/module/shop/filter/(:var)' => array(
        'controller' => 'Admin:Browser@filterAction'
    ),
    
    '/admin/module/shop/page/(:var)' => array(
        'controller' => 'Admin:Browser@indexAction'
    ),
    
    '/admin/module/shop/tweak' => array(
        'controller' => 'Admin:Product@tweakAction',
        'disallow' => array('guest')
    ),
    
    '/admin/module/shop/product/add' => array(
        'controller' => 'Admin:Product@addAction'
    ),
    
    '/admin/module/shop/product/edit/(:var)' => array(
        'controller' => 'Admin:Product@editAction'
    ),
    
    '/admin/module/shop/product/save' => array(
        'controller' => 'Admin:Product@saveAction',
        'disallow' => array('guest')
    ),
    
    '/admin/module/shop/product/delete' => array(
        'controller' => 'Admin:Product@deleteAction',
        'disallow' => array('guest')
    ),
    
    '/admin/module/shop/config' => array(
        'controller' => 'Admin:Config@indexAction'
    ),
    
    '/admin/module/shop/config/save.ajax' => array(
        'controller' => 'Admin:Config@saveAction',
        'disallow' => array('guest')
    )
);
