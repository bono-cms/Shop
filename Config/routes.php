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
    
    '/module/shop/basket/add.ajax' => array(
        'controller' => 'Basket@addAction'
    ),
    
    '/module/shop/basket/get-stat.ajax' => array(
        'ajax' => true,
        'controller' => 'Basket@getStatAction'
    ),
    
    '/module/shop/basket/re-count.ajax' => array(
        //'ajax' => true,
        'controller' => 'Basket@recountAction'
    ),
    
    '/module/shop/basket/delete.ajax' => array(
        'controller' => 'Basket@deleteAction'
    ),
    
    '/module/shop/basket/clear.ajax' => array(
        'controller' => 'Basket@clearAction'
    ),
    
    
    //---- Orders
    '/module/shop/basket/order.ajax' => array(
        'controller' => 'Order@orderAction'
    ),
    
    '/module/shop/basket/order/delete.ajax' => array(
        'controller' => 'Admin:Order@deleteAction'
    ),
    
    '/module/shop/basket/order/delete-selected.ajax' => array(
        'controller' => 'Admin:Order@deleteSelectedAction'
    ),
    
    '/module/shop/basket/order/approve.ajax' => array(
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
    
    
    '/module/shop/category/do/change-per-page-count.ajax' => array(
        'controller' => 'Category@changePerPageCountAction'
    ),
    
    '/module/shop/category/do/change-sort-action.ajax' => array(
        'controller' => 'Category@changeSortAction'
    ),
    
    // ------------------------------------------
    
    
    '/admin/module/shop/statistic.ajax' => array(
        'controller' => 'Admin:Statistic@indexAction'
    ),
    
    
    '/admin/module/shop/category/add' => array(
        'controller' => 'Admin:Category:Add@indexAction'
    ),
    
    '/admin/module/shop/category/add.ajax' => array(
        'controller' => 'Admin:Category:Add@addAction',
        'disallow' => array('guest')
    ),
    
    '/admin/module/shop/category/edit/(:var)' => array(
        'controller' => 'Admin:Category:Edit@indexAction'
    ),
    
    '/admin/module/shop/category/edit.ajax' => array(
        'controller' => 'Admin:Category:Edit@updateAction',
        'disallow' => array('guest')
    ),
    
    // For viewing a category
    '/admin/module/shop/category/(:var)' => array(
        'controller' => 'Admin:Browser@categoryAction'
    ),
    
    '/admin/module/shop/category/(:var)/page/(:var)' => array(
        'controller' => 'Admin:Browser@categoryAction'
    ),
    
    '/admin/module/shop/category/do/delete.ajax' => array(
        'controller' => 'Admin:Browser@deleteCategoryAction',
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
    
    '/admin/module/shop/save.ajax' => array(
        'controller' => 'Admin:Browser@saveAction',
        'disallow' => array('guest')
    ),
    
    '/admin/module/shop/delete-selected.ajax' => array(
        'controller' => 'Admin:Browser@deleteSelectedAction',
        'disallow' => array('guest')
    ),
    
    '/admin/module/shop/product/add' => array(
        'controller' => 'Admin:Product:Add@indexAction'
    ),
    
    '/admin/module/shop/product/add.ajax' => array(
        'controller' => 'Admin:Product:Add@addAction',
        'disallow' => array('guest')
    ),
    
    '/admin/module/shop/product/edit/(:var)' => array(
        'controller' => 'Admin:Product:Edit@indexAction'
    ),
    
    '/admin/module/shop/product/edit.ajax' => array(
        'controller' => 'Admin:Product:Edit@updateAction',
        'disallow' => array('guest')
    ),
    
    '/admin/module/shop/product/delete.ajax' => array(
        'controller' => 'Admin:Browser@deleteAction',
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
