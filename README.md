Shop module
===========

This module allows you to manage e-commerce store on your site. For details, please refer to its documentation.

# Features

## General

 - Filterable grid in administration panel
 - Two column view. One for products, the second for categories
 - Configuration
 - Statistic
 - Searchable (it would be possible to search products and categories by keywords on the site)

## Products

 - Quick view
 - Regular and stoke prices
 - Ability to attach several categories to single product
 - Cover and additional images
 - Recently viewed products
 - Best sellers
 - Latest products
 - Attached similar products
 - Attached recommended products
 - Description
 - View counter
 - Breadcrumbs
 - Specifications
 - Brands

## Categories

 - Filters / attributes by product attributes
 - Unlimited depth support (aka Parent-child relationship)
 - Category cover
 - Breadcrumbs
 - Description
 - Ability to sort applying several options when viewing on the site
 - Ability to change per page count on the site as well

## Orders

 - Shopping cart (Basket)
 - Order form with built-in one click order
 - Discount codes
 - Dynamic delivery methods
 - Ability to order right in the basket page or via standalone Checkout page
 
## Customers

This is optional feature. Orders can be done without registration.

 - Personal cabinet (Login / Registration / Password recovery)
 - Wishlist
 - Order statuses
 - Order history
 
## Coming soon:

 - Dynamic fields in attributes (WYSIWYG, select, etc)
 - Colors and image attaches
 - Product ratings
 - Product reviews

# Templates

## Product template
The template file must be named `shop-product.phtml` and placed inside the current theme directory.

Within this template, the `$product`  entity object is available and provides the following methods:


    $product->getImageUrl('dimension'); 
    // Returns the full URL to the product image. 
    // You can specify a dimension (e.g., 'thumbnail', 'medium') depending on the configured sizes.
    
    // Returns the name of the category this product belongs to.
    $product->getCategoryName(); 
    
    // Returns the product name.
    $product->getName(); 
    
    // Returns the product price.
    $product->getPrice(); 
    
    // Returns the strike-through price (e.g., original price before discount), if defined.
    $product->getStokePrice(); 
    
    // Returns TRUE or FALSE depending on whether the product is marked as a special offer.
    $product->getSpecialOffer(); 
    
    // Returns the full product description.
    $product->getDescription(); 
    
    // Returns the date the product was added by the site administrator, in `YYYY-MM-DD` format.
    $product->getDate(); 
    
    // Returns the full product URL.
    $product->getUrl(); 
    
    // Returns the number of times the product has been viewed by users.
    $product->getViewCount(); 

## Category template

The file must be named `shop-category.phtml` and placed within the current theme directory.

Inside this template, a `$category` entity object is available, providing the following methods:

    // Returns the full URL to the category's cover image. 
    // You can specify a dimension (e.g., 'thumbnail', 'medium') based on the configured image sizes.
    $category->getImageUrl('dimension'); 
    
    // Returns the title of the category.
    $category->getName(); 
    
    // Returns the category description.
    $category->getDescription(); 
    
    // Returns the full URL of the current category.
    $category->getUrl(); 

### Nested categories

To determine whether the current category contains nested categories, use the predefined `$categories` array. If there is at least one nested category, the array will contain the corresponding category entities.

If there are no nested categories, products are typically displayed instead.

    <?php if (isset($categories)): ?>
    
    <!-- There are nested categories. Render them; -->
    <div class="row">
        <?php foreach ($categories as $nested): ?>
        <div class="col-lg-3">
            <h3 class="mb-3"><?= $nested->getName(); ?></h3>
            <a href="<?= $nested->getUrl(); ?>">View category</a>
        </div>
        <?php endforeach; ?>
    </div>
    
    <?php else: ?>
    
    <!-- No nested categories. Render products instead. -->
    <div class="row">
        <?php foreach ($products as $product): ?>
        <div class="col-lg-3">
            <h3><?= $product->getName(); ?></h3>
            <a href="<?= $product->getUrl(); ?>">View product</a>
        </div>
        <?php endforeach; ?>
    </div>
    
    <?php endif; ?>

## Basket template

The file must be named `shop-basket.phtml` and placed in the current theme directory.

Within this template, a `$products` array is available, containing product entity objects. Each object provides the following methods:

    // Returns the unique ID of the product.
    $basket->getId(); 
    
    // Returns the product title.
    $basket->getName(); 
    
    // Returns the full URL to the product image. 
    // You can specify a dimension based on the configured image sizes.
    $basket->getImageUrl('dimension'); 
    
    // Returns the quantity of this product in the basket.
    $basket->getQty(); 
    
    // Returns the product price. 
    // If a strike-through price is defined, it will be returned instead.
    $basket->getPrice(); 
    
    // Returns the subtotal price for this product 
    // (i.e., quantity × price).
    $basket->getSubTotalPrice(); 

## Stoke page template

The file must be named shop-stokes.phtml and placed in the current theme directory.

This page contains a `$products` array of product entities. The available methods are identical to those found on the category page.

# URL Generation

## Categories

To generate a URL for a category by its ID (assuming the category ID is 1), use:

    <a href="<?= $cms->createUrl(1, 'Shop (Categories)'); ?>">View category</a>

## Products

To generate a URL for a product by its ID (assuming the product ID is 1), use:

    <a href="<?= $cms->createUrl(1, 'Shop'); ?>">View product</a>

# Services

## Basket service
The service is accessible via the `$basket` variable, which is available in all templates. It provides the following methods:

    // Returns the URL of the basket page. 
    // Note: The page ID must be specified in the module configuration.
    $basket->getUrl(); 
    
    // Returns the total price of all products in the basket.
    $basket->getTotalPrice(); 
    
    // Returns the total quantity of products in the basket.
    $basket->getTotalQty(); 
    
    // Returns the current currency.
    // Note: The currency must be defined in the module configuration.
    $basket->getCurrency(); 

## Shop service

The `$shop` variable represents the shop service and is available globally. It provides the following methods:

### Getting minimal product price

Returns the minimum product price (i.e., the starting price) within the specified category.

    $shop->getMinCategoryPriceCount($id); // $id - category id

### Getting products with max view count

    $shop->getProductsWithMaxViewCount($limit, $categoryId = null);

Returns an array of the most viewed product entities.

-   `$limit`: Specifies the number of products to return.
    
-   `$categoryId` (optional): Filters the results by a specific category ID, if provided.

### Getting recent products
    $shop->getRecentProducts($id); // Product ID to be exluded. Optional.
    
Returns an array of products recently viewed by the user.

-   `$id`: The ID of the current product to exclude from the result.  
    This is useful when displaying related or recently viewed products, so the currently viewed product isn’t shown in the list.


### Getting last products

    $shop->getLatest()

Returns an array of the latest products added by the site administrator.  
The number of products returned is determined by the module's configuration settings.

