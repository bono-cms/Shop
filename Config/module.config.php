<?php

/**
 * Module configuration container
 */

return array(
    'name' => 'Shop',
    'description' => 'Shop module allows you to manage e-commerce system on your site',
    // Bookmarks of this module
    'bookmarks' => array(
        array(
            'name' => 'Add a product',
            'controller' => 'Shop:Admin:Product@addAction',
            'icon' => 'glyphicon glyphicon-shopping-cart'
        ),

        array(
            'name' => 'Add a category',
            'controller' => 'Shop:Admin:Category@addAction',
            'icon' => 'glyphicon glyphicon-folder-open'
        ),

        array(
            'name' => 'Orders',
            'controller' => 'Shop:Admin:Order@indexAction',
            'icon' => 'glyphicon glyphicon-user'
        )
    ),
    'menu' => array(
        'name' => 'Shop',
        'icon' => 'fas fa-cart-arrow-down',
        'items' => array(
            array(
                'route' => 'Shop:Admin:Browser@indexAction',
                'name' => 'View all products'
            ),
            array(
                'route' => 'Shop:Admin:Category@addAction',
                'name' => 'Add a category'
            )
        )
    )
);