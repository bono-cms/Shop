<?php

/**
 * Module configuration container
 */

return [
    'name' => 'Shop',
    'description' => 'Shop module allows you to manage e-commerce system on your site',
    'bookmarks' => [
        [
            'name' => 'Add a product',
            'controller' => 'Shop:Admin:Product@addAction',
            'icon' => 'fas fa-cart-arrow-down'
        ],

        [
            'name' => 'Add a category',
            'controller' => 'Shop:Admin:Category@addAction',
            'icon' => 'fas fa-clone'
        ],

        [
            'name' => 'Orders',
            'controller' => 'Shop:Admin:Order@indexAction',
            'icon' => 'far fa-credit-card'
        ]
    ],
    'menu' => [
        'name' => 'Shop',
        'icon' => 'fas fa-cart-arrow-down',
        'items' => [
            [
                'route' => 'Shop:Admin:Browser@indexAction',
                'name' => 'View all products'
            ],
            [
                'route' => 'Shop:Admin:Product@addAction',
                'name' => 'Add a product'
            ],
            [
                'route' => 'Shop:Admin:Category@addAction',
                'name' => 'Add a category'
            ]
        ]
    ]
];