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
    
    '/%s/module/shop/orders' => array(
        'controller' => 'Admin:Order@indexAction'
    ),
    
    '/%s/module/shop/orders/filter/(:var)' => array(
        'controller' => 'Admin:Order@filterAction'
    ),

    '/%s/module/shop/orders/page/(:var)' => array(
        'controller' => 'Admin:Order@indexAction'
    ),
    
    '/%s/module/shop/orders/details/(:var)' => array(
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
    
    
    '/%s/module/shop/statistic' => array(
        'controller' => 'Admin:Statistic@indexAction'
    ),
    
    '/%s/module/shop/category/add' => array(
        'controller' => 'Admin:Category@addAction'
    ),
    
    '/%s/module/shop/category/edit/(:var)' => array(
        'controller' => 'Admin:Category@editAction'
    ),
    
    '/%s/module/shop/category/save' => array(
        'controller' => 'Admin:Category@saveAction',
        'disallow' => array('guest')
    ),
    
    // For viewing a category
    '/%s/module/shop/category/(:var)' => array(
        'controller' => 'Admin:Browser@categoryAction'
    ),
    
    '/%s/module/shop/category/(:var)/page/(:var)' => array(
        'controller' => 'Admin:Browser@categoryAction'
    ),
    
    '/%s/module/shop/category/do/delete/(:var)' => array(
        'controller' => 'Admin:Category@deleteAction',
        'disallow' => array('guest')
    ),
    
    '/%s/module/shop' => array(
        'controller' => 'Admin:Browser@indexAction'
    ),

    '/%s/module/shop/filter/(:var)' => array(
        'controller' => 'Admin:Browser@filterAction'
    ),
    
    '/%s/module/shop/page/(:var)' => array(
        'controller' => 'Admin:Browser@indexAction'
    ),
    
    '/%s/module/shop/tweak' => array(
        'controller' => 'Admin:Product@tweakAction',
        'disallow' => array('guest')
    ),
    
    '/%s/module/shop/product/add' => array(
        'controller' => 'Admin:Product@addAction'
    ),
    
    '/%s/module/shop/product/edit/(:var)' => array(
        'controller' => 'Admin:Product@editAction'
    ),
    
    '/%s/module/shop/product/save' => array(
        'controller' => 'Admin:Product@saveAction',
        'disallow' => array('guest')
    ),
    
    '/%s/module/shop/product/delete/(:var)' => array(
        'controller' => 'Admin:Product@deleteAction',
        'disallow' => array('guest')
    ),
    
    '/%s/module/shop/config' => array(
        'controller' => 'Admin:Config@indexAction'
    ),
    
    '/%s/module/shop/config/save.ajax' => array(
        'controller' => 'Admin:Config@saveAction',
        'disallow' => array('guest')
    )
);
