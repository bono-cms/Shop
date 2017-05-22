<?php

return array(
    'name' => 'Shop',
    'caption' => 'Shop',
    'route' => 'Shop:Admin:Browser@indexAction',
    'icon' => 'fa fa-shopping-cart fa-5x',
    'order' => 1,
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
    )
);