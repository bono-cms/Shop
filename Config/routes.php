<?php

/**
 * This file is part of the Bono CMS
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

return [
    // Wishlist
    '/module/shop/wishlist/delete' => [
        'controller' => 'Customer:Wishlist@deleteAction'
    ],
    
    '/module/shop/wishlist/add' => [
        'controller' => 'Customer:Wishlist@addAction'
    ],
    
    '/module/shop/wishlist' => [
        'controller' => 'Customer:Wishlist@indexAction'
    ],
    
    '/customer/orders' => [
        'controller' => 'Customer:Order@listAction'
    ],

    '/customer/order/(:var)' => [
        'controller' => 'Customer:Order@detailAction'
    ],
    
    '/module/shop/checkout' => [
        'controller' => 'Checkout@indexAction'
    ],
    
    '/module/shop/category/do/filter/(:var)' => [
        'controller' => 'Category@filterAction'
    ],

    '/module/shop/search/(:var)' => [
        'controller' => 'Search@searchAction'
    ],
    
    '/module/shop/stokes' => [
        'controller' => 'Stokes@indexAction'
    ],
    
    '/module/shop/category/(:var)' => [
        'controller' => 'Category@indexAction'
    ],
    
    '/module/shop/product/(:var)' => [
        'controller' => 'Product@indexAction'
    ],

    '/module/shop/product/quick-view/(:var)' => [
        'controller' => 'Product@quickViewAction'
    ],
    
    '/module/shop/basket' => [
        'controller' => 'Basket@indexAction'
    ],
    
    '/module/shop/basket/add' => [
        'controller' => 'Basket@addAction'
    ],

    '/module/shop/basket/add-variant' => [
        'controller' => 'Basket@addVariant'
    ],
    
    '/module/shop/basket/wishlist' => [
        'controller' => 'Basket@wishlistAction'
    ],
    
    '/module/shop/basket/get-stat' => [
        'ajax' => true,
        'controller' => 'Basket@getStatAction'
    ],
    
    '/module/shop/basket/re-count' => [
        //'ajax' => true,
        'controller' => 'Basket@recountAction'
    ],
    
    '/module/shop/basket/delete' => [
        'controller' => 'Basket@deleteAction'
    ],
    
    '/module/shop/basket/clear' => [
        'controller' => 'Basket@clearAction'
    ],
    
    //---- Orders
    '/module/shop/basket/order' => [
        'controller' => 'Order@orderAction'
    ],
    
    '/%s/module/shop/basket/order/delete/(:var)' => [
        'controller' => 'Admin:Order@deleteAction'
    ],
    
    '/%s/module/shop/basket/order/approve/(:var)' => [
        'controller' => 'Admin:Order@approveAction'
    ],
    
    '/%s/module/shop/orders' => [
        'controller' => 'Admin:Order@indexAction'
    ],

    '/%s/module/shop/orders/tweak' => [
        'controller' => 'Admin:Order@tweakAction'
    ],
    
    '/%s/module/shop/orders/filter/(:var)' => [
        'controller' => 'Admin:Order@filterAction'
    ],

    '/%s/module/shop/orders/page/(:var)' => [
        'controller' => 'Admin:Order@indexAction'
    ],
    
    '/%s/module/shop/orders/details/(:var)' => [
        'controller' => 'Admin:Order@detailsAction'
    ],
    
    //---- Orders
    

    '/module/shop/category/do/change-per-page-count' => [
        'controller' => 'Category@changePerPageCountAction'
    ],
    
    '/module/shop/category/do/change-sort-action' => [
        'controller' => 'Category@changeSortAction'
    ],

    // ------------------------------------------

    // Coupons
    '/%s/module/shop/coupons' => [
        'controller' => 'Admin:Coupon@indexAction'
    ],

    '/%s/module/shop/coupons/edit/(:var)' => [
        'controller' => 'Admin:Coupon@editAction'
    ],

    '/%s/module/shop/coupons/save' => [
        'controller' => 'Admin:Coupon@saveAction'
    ],

    '/%s/module/shop/coupons/delete/(:var)' => [
        'controller' => 'Admin:Coupon@deleteAction'
    ],

    // Order statuses
    '/%s/module/shop/order-statuses' => [
        'controller' => 'Admin:OrderStatus@indexAction'
    ],

    '/%s/module/shop/order-statuses/add' => [
        'controller' => 'Admin:OrderStatus@addAction'
    ],

    '/%s/module/shop/order-statuses/edit/(:var)' => [
        'controller' => 'Admin:OrderStatus@editAction'
    ],

    '/%s/module/shop/order-statuses/save' => [
        'controller' => 'Admin:OrderStatus@saveAction'
    ],

    '/%s/module/shop/order-statuses/delete/(:var)' => [
        'controller' => 'Admin:OrderStatus@deleteAction'
    ],
    
    // Currencies
    '/%s/module/shop/currencies' => [
        'controller' => 'Admin:Currency@indexAction'
    ],

    '/%s/module/shop/currencies/edit/(:var)' => [
        'controller' => 'Admin:Currency@editAction'
    ],

    '/%s/module/shop/currencies/save' => [
        'controller' => 'Admin:Currency@saveAction'
    ],

    '/%s/module/shop/currencies/delete/(:var)' => [
        'controller' => 'Admin:Currency@deleteAction'
    ],

    // Coupon validation on site
    '/module/shop/coupon/check/(:var)' => [
        'controller' => 'Checkout@couponAction'
    ],
    
    // Delivery types
    '/%s/module/shop/delivery-type' => [
        'controller' => 'Admin:DeliveryType@indexAction'
    ],
    
    '/%s/module/shop/delivery-type/edit/(:var)' => [
        'controller' => 'Admin:DeliveryType@editAction'
    ],

    '/%s/module/shop/delivery-type/add' => [
        'controller' => 'Admin:DeliveryType@addAction'
    ],

    '/%s/module/shop/delivery-type/save' => [
        'controller' => 'Admin:DeliveryType@saveAction'
    ],
    
    '/%s/module/shop/delivery-type/delete/(:var)' => [
        'controller' => 'Admin:DeliveryType@deleteAction'
    ],
    
    '/%s/module/shop/statistic' => [
        'controller' => 'Admin:Statistic@indexAction'
    ],
    
    '/%s/module/shop/category/add' => [
        'controller' => 'Admin:Category@addAction'
    ],
    
    '/%s/module/shop/category/edit/(:var)' => [
        'controller' => 'Admin:Category@editAction'
    ],
    
    '/%s/module/shop/category/save' => [
        'controller' => 'Admin:Category@saveAction',
        'disallow' => ['guest']
    ],
    
    // For viewing a category
    '/%s/module/shop/category/(:var)' => [
        'controller' => 'Admin:Browser@categoryAction'
    ],
    
    '/%s/module/shop/category/(:var)/page/(:var)' => [
        'controller' => 'Admin:Browser@categoryAction'
    ],
    
    '/%s/module/shop/category/do/delete/(:var)' => [
        'controller' => 'Admin:Category@deleteAction',
        'disallow' => ['guest']
    ],
    
    '/%s/module/shop' => [
        'controller' => 'Admin:Browser@indexAction'
    ],

    '/%s/module/shop/attributes' => [
        'controller' => 'Admin:Attributes@indexAction'
    ],

    '/%s/module/shop/attributes/group/view/(:var)' => [
        'controller' => 'Admin:Attributes@groupAction'
    ],
    
    '/%s/module/shop/attributes/group/add' => [
        'controller' => 'Admin:AttributeGroup@addAction'
    ],
    
    '/%s/module/shop/attributes/group/edit/(:var)' => [
        'controller' => 'Admin:AttributeGroup@editAction'
    ],
    
    '/%s/module/shop/attributes/group/save' => [
        'controller' => 'Admin:AttributeGroup@saveAction'
    ],
    
    '/%s/module/shop/attributes/group/delete/(:var)' => [
        'controller' => 'Admin:AttributeGroup@deleteAction'
    ],
    
    '/%s/module/shop/attributes/value/save' => [
        'controller' => 'Admin:AttributeValue@saveAction'
    ],

    '/%s/module/shop/attributes/value/add' => [
        'controller' => 'Admin:AttributeValue@addAction'
    ],
    
    '/%s/module/shop/attributes/value/edit/(:var)' => [
        'controller' => 'Admin:AttributeValue@editAction'
    ],
    
    '/%s/module/shop/attributes/value/save' => [
        'controller' => 'Admin:AttributeValue@saveAction'
    ],
    
    '/%s/module/shop/attributes/value/delete/(:var)' => [
        'controller' => 'Admin:AttributeValue@deleteAction'
    ],
    
    '/%s/module/shop/page/(:var)' => [
        'controller' => 'Admin:Browser@indexAction'
    ],
    
    '/%s/module/shop/tweak' => [
        'controller' => 'Admin:Product@tweakAction',
        'disallow' => ['guest']
    ],
    
    '/%s/module/shop/product/add' => [
        'controller' => 'Admin:Product@addAction'
    ],

    '/%s/module/shop/product/edit/(:var)' => [
        'controller' => 'Admin:Product@editAction'
    ],
    
    '/%s/module/shop/product/save' => [
        'controller' => 'Admin:Product@saveAction',
        'disallow' => ['guest']
    ],
    
    '/%s/module/shop/product/delete/(:var)' => [
        'controller' => 'Admin:Product@deleteAction',
        'disallow' => ['guest']
    ],
    
    '/%s/module/shop/config' => [
        'controller' => 'Admin:Config@indexAction'
    ],
    
    '/%s/module/shop/config/save.ajax' => [
        'controller' => 'Admin:Config@saveAction',
        'disallow' => ['guest']
    ],

    '/%s/module/shop/specification/(:var)' => [
        'controller' => 'Admin:SpecificationItem@indexAction'
    ],
    
    // Specification category
    '/%s/module/shop/specification/category/add' => [
        'controller' => 'Admin:SpecificationCategory@addAction'
    ],

    '/%s/module/shop/specification/category/edit/(:var)' => [
        'controller' => 'Admin:SpecificationCategory@editAction'
    ],

    '/%s/module/shop/specification/category/delete/(:var)' => [
        'controller' => 'Admin:SpecificationCategory@deleteAction'
    ],

    '/%s/module/shop/specification/category/save' => [
        'controller' => 'Admin:SpecificationCategory@saveAction'
    ],

    // Specification item
    '/%s/module/shop/specification/item/add' => [
        'controller' => 'Admin:SpecificationItem@addAction'
    ],

    '/%s/module/shop/specification/item/edit/(:var)' => [
        'controller' => 'Admin:SpecificationItem@editAction'
    ],

    '/%s/module/shop/specification/item/delete/(:var)' => [
        'controller' => 'Admin:SpecificationItem@deleteAction'
    ],

    '/%s/module/shop/specification/item/save' => [
        'controller' => 'Admin:SpecificationItem@saveAction'
    ],

    // Brands
    '/%s/module/shop/brands' => [
        'controller' => 'Admin:Brand@indexAction'
    ],

    '/%s/module/shop/brands/edit/(:var)' => [
        'controller' => 'Admin:Brand@editAction'
    ],

    '/%s/module/shop/brands/delete/(:var)' => [
        'controller' => 'Admin:Brand@deleteAction'
    ],

    '/%s/module/shop/brands/save' => [
        'controller' => 'Admin:Brand@saveAction'
    ],
    
    // Variants
    '/%s/module/shop/variant/save' => [
        'controller' => 'Admin:ProductVariant@saveAction'
    ],

    '/%s/module/shop/variant/add/(:var)' => [
        'controller' => 'Admin:ProductVariant@addAction'
    ],

    '/%s/module/shop/variant/edit/(:var)' => [
        'controller' => 'Admin:ProductVariant@editAction'
    ],

    '/%s/module/shop/variant/delete/(:var)' => [
        'controller' => 'Admin:ProductVariant@deleteAction'
    ]
];