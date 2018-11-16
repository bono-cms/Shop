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
    // Wishlist
    '/module/shop/wishlist/delete' => array(
        'controller' => 'Customer:Wishlist@deleteAction'
    ),
    
    '/module/shop/wishlist/add' => array(
        'controller' => 'Customer:Wishlist@addAction'
    ),
    
    '/module/shop/wishlist' => array(
        'controller' => 'Customer:Wishlist@indexAction'
    ),
    
    '/customer/orders' => array(
        'controller' => 'Customer:Order@listAction'
    ),

    '/customer/order/(:var)' => array(
        'controller' => 'Customer:Order@detailAction'
    ),
    
    '/module/shop/checkout' => array(
        'controller' => 'Checkout@indexAction'
    ),
    
    '/module/shop/category/do/filter/(:var)' => array(
        'controller' => 'Category@filterAction'
    ),

    '/module/shop/search/(:var)' => array(
        'controller' => 'Search@searchAction'
    ),
    
    '/module/shop/stokes' => array(
        'controller' => 'Stokes@indexAction'
    ),
    
    '/module/shop/category/(:var)' => array(
        'controller' => 'Category@indexAction'
    ),
    
    '/module/shop/product/(:var)' => array(
        'controller' => 'Product@indexAction'
    ),

    '/module/shop/product/quick-view/(:var)' => array(
        'controller' => 'Product@quickViewAction'
    ),
    
    '/module/shop/basket' => array(
        'controller' => 'Basket@indexAction'
    ),
    
    '/module/shop/basket/add' => array(
        'controller' => 'Basket@addAction'
    ),
    
    '/module/shop/basket/wishlist' => array(
        'controller' => 'Basket@wishlistAction'
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
    
    '/%s/module/shop/basket/order/delete/(:var)' => array(
        'controller' => 'Admin:Order@deleteAction'
    ),
    
    '/%s/module/shop/basket/order/approve/(:var)' => array(
        'controller' => 'Admin:Order@approveAction'
    ),
    
    '/%s/module/shop/orders' => array(
        'controller' => 'Admin:Order@indexAction'
    ),

    '/%s/module/shop/orders/tweak' => array(
        'controller' => 'Admin:Order@tweakAction'
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

    // Coupons
    '/%s/module/shop/coupons' => array(
        'controller' => 'Admin:Coupon@indexAction'
    ),

    '/%s/module/shop/coupons/edit/(:var)' => array(
        'controller' => 'Admin:Coupon@editAction'
    ),

    '/%s/module/shop/coupons/save' => array(
        'controller' => 'Admin:Coupon@saveAction'
    ),

    '/%s/module/shop/coupons/delete/(:var)' => array(
        'controller' => 'Admin:Coupon@deleteAction'
    ),

    // Order statuses
    '/%s/module/shop/order-statuses' => array(
        'controller' => 'Admin:OrderStatus@indexAction'
    ),

    '/%s/module/shop/order-statuses/edit/(:var)' => array(
        'controller' => 'Admin:OrderStatus@editAction'
    ),

    '/%s/module/shop/order-statuses/save' => array(
        'controller' => 'Admin:OrderStatus@saveAction'
    ),

    '/%s/module/shop/order-statuses/delete/(:var)' => array(
        'controller' => 'Admin:OrderStatus@deleteAction'
    ),
    
    // Currencies
    '/%s/module/shop/currencies' => array(
        'controller' => 'Admin:Currency@indexAction'
    ),

    '/%s/module/shop/currencies/edit/(:var)' => array(
        'controller' => 'Admin:Currency@editAction'
    ),

    '/%s/module/shop/currencies/save' => array(
        'controller' => 'Admin:Currency@saveAction'
    ),

    '/%s/module/shop/currencies/delete/(:var)' => array(
        'controller' => 'Admin:Currency@deleteAction'
    ),

    // Coupon validation on site
    '/module/shop/coupon/check/(:var)' => array(
        'controller' => 'Checkout@couponAction'
    ),
    
    // Delivery types
    '/%s/module/shop/delivery-type' => array(
        'controller' => 'Admin:DeliveryType@indexAction'
    ),
    
    '/%s/module/shop/delivery-type/edit/(:var)' => array(
        'controller' => 'Admin:DeliveryType@editAction'
    ),
    
    '/%s/module/shop/delivery-type/save' => array(
        'controller' => 'Admin:DeliveryType@saveAction'
    ),
    
    '/%s/module/shop/delivery-type/delete/(:var)' => array(
        'controller' => 'Admin:DeliveryType@deleteAction'
    ),
    
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

    '/%s/module/shop/attributes' => array(
        'controller' => 'Admin:Attributes@indexAction'
    ),

    '/%s/module/shop/attributes/group/view/(:var)' => array(
        'controller' => 'Admin:Attributes@groupAction'
    ),
    
    '/%s/module/shop/attributes/group/add' => array(
        'controller' => 'Admin:AttributeGroup@addAction'
    ),
    
    '/%s/module/shop/attributes/group/edit/(:var)' => array(
        'controller' => 'Admin:AttributeGroup@editAction'
    ),
    
    '/%s/module/shop/attributes/group/save' => array(
        'controller' => 'Admin:AttributeGroup@saveAction'
    ),
    
    '/%s/module/shop/attributes/group/delete/(:var)' => array(
        'controller' => 'Admin:AttributeGroup@deleteAction'
    ),
    
    '/%s/module/shop/attributes/value/save' => array(
        'controller' => 'Admin:AttributeValue@saveAction'
    ),

    '/%s/module/shop/attributes/value/add' => array(
        'controller' => 'Admin:AttributeValue@addAction'
    ),
    
    '/%s/module/shop/attributes/value/edit/(:var)' => array(
        'controller' => 'Admin:AttributeValue@editAction'
    ),
    
    '/%s/module/shop/attributes/value/save' => array(
        'controller' => 'Admin:AttributeValue@saveAction'
    ),
    
    '/%s/module/shop/attributes/value/delete/(:var)' => array(
        'controller' => 'Admin:AttributeValue@deleteAction'
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
    ),

    '/%s/module/shop/specification/' => array(
        'controller' => 'Admin:SpecificationItem@indexAction'
    ),
    
    // Specification category
    '/%s/module/shop/specification/category/add' => array(
        'controller' => 'Admin:SpecificationCategory@addAction'
    ),

    '/%s/module/shop/specification/category/edit/(:var)' => array(
        'controller' => 'Admin:SpecificationCategory@editAction'
    ),

    '/%s/module/shop/specification/category/delete/(:var)' => array(
        'controller' => 'Admin:SpecificationCategory@deleteAction'
    ),

    '/%s/module/shop/specification/category/save' => array(
        'controller' => 'Admin:SpecificationCategory@saveAction'
    ),

    // Specification item
    '/%s/module/shop/specification/item/add' => array(
        'controller' => 'Admin:SpecificationItem@addAction'
    ),

    '/%s/module/shop/specification/item/edit/(:var)' => array(
        'controller' => 'Admin:SpecificationItem@editAction'
    ),

    '/%s/module/shop/specification/item/delete/(:var)' => array(
        'controller' => 'Admin:SpecificationItem@deleteAction'
    ),

    '/%s/module/shop/specification/item/save' => array(
        'controller' => 'Admin:SpecificationItem@saveAction'
    )
);
