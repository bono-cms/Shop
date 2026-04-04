Shop module
===========

This module allows you to manage e-commerce store on your site. For details, please refer to its documentation.

# Features

## General features

 - Filterable grid in administration panel
 - Two column view. One for products, the second for categories
 - Configuration
 - Statistic
 - Searchable (it would be possible to search products and categories by keywords on the site)

## Product features

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

## Category features

 - Filters / attributes by product attributes
 - Unlimited depth support (aka Parent-child relationship)
 - Category cover
 - Breadcrumbs
 - Description
 - Ability to sort applying several options when viewing on the site
 - Ability to change per page count on the site as well

## Order features

 - Shopping cart (Basket)
 - Order form with built-in one click order
 - Discount codes
 - Dynamic delivery methods
 - Ability to order right in the basket page or via standalone Checkout page
 
## Customer features

This is optional feature. Orders can be done without registration.

 - Personal cabinet (Login / Registration / Password recovery)
 - Wishlist
 - Order statuses
 - Order history
 
## Coming soon

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

**Product images**

Within the `shop-product.phtml` template, an array of product images is available via the `$images` variable.

The `$images` variable contains an array of **image entities** associated with the current product. Each image entity provides access to various image data and methods, including the image URL through its `ImageBag`.

Example usage

    <?php if (!empty($images)): ?>
        <div class="row">
         <?php foreach ($images as $image): ?>
         <div class="col-lg-4">
             <img src="<?= $image->getImageBag()->getUrl('original'); ?>" alt="<?= $product->getName(); ?>" class="img-fluid">
         </div>
         <?php endforeach; ?>
        </div>
    <?php endif; ?>

**Notes**

-   `$images` will be empty if no images are assigned to the product.
    
-   You can retrieve different image sizes or versions by replacing `'original'` with other available image keys (e.g. `'600x600'`, `'250x500'`, etc.), depending on your configuration.

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

## Variants

Product variants are options that can be assigned to a single product to represent different versions, such as size, color, or material. Unlike standard attributes, each variant is a unique entity with its own SKU, Price, and Stock level, allowing for precise inventory tracking and flexible pricing strategies within a single product page.

**Usage in templates**

In the shop-product.phtml template, you can check whether a product has variants and iterate through them to build a selection table or a dropdown.

    <?php if ($product->hasVariants()): ?>
    <h5 class="mt-5 mb-4 fw-bold text-uppercase small text-muted"><?= $this->translate('Available Variants'); ?></h5>

    <div class="table-responsive">
        <table class="table align-middle border-top">
            <thead>
                <tr class="small text-uppercase fw-bold text-secondary">
                    <th class="py-3"><?= $this->translate('SKU'); ?></th>
                    <th class="py-3"><?= $this->translate('Price'); ?></th>
                    <th class="py-3 w-25"><?= $this->translate('Quantity'); ?></th>
                    <th class="py-3 text-end"><?= $this->translate('Action'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($product->getVariants() as $variant): ?>
                <tr>
                    <td class="fw-semibold text-dark"><?= $variant->getSku(); ?></td>
                    <td>
                        <span class="fw-bold text-primary"><?= $variant->getPrice(); ?></span>
                        <small class="text-muted"><?= $basket->getCurrency(); ?></small>
                    </td>
                    <td>
                        <?php if ($variant->getStock() > 0): ?>
                        <div class="input-group input-group-sm w-75">
                            <span class="input-group-text bg-white text-muted border-end-0"><i class="bi bi-box-seam"></i></span>
                            <input type="number" class="form-control border-start-0 text-center rounded-0" value="1" min="1" max="<?= $variant->getStock(); ?>" data-basket-quantity="<?= $variant->getId(); ?>" aria-label="Quantity">
                        </div>
                        <div class="text-success mt-1 small opacity-75">
                            <?= $this->translate('In stock'); ?>: <?= $variant->getStock(); ?>
                        </div>
                        <?php else: ?>
                        <span class="badge rounded-pill bg-light text-danger border border-danger border-opacity-25 py-2 px-3">
                            <i class="bi bi-x-circle me-1"></i><?= $this->translate('Out of stock'); ?>
                        </span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end">
                        <button class="btn btn-primary btn-sm rounded-0 px-3" data-button="add-variant-to-cart" data-product-id="<?= $variant->getProductId(); ?>" data-variant-id="<?= $variant->getId(); ?>" <?= $variant->getStock() <= 0 ? 'disabled' : ''; ?>>
                            <i class="bi bi-cart-plus me-1"></i> <?= $this->translate('Add'); ?>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>


**Data structure**

Each variant is returned as a Krystal\Stdlib\VirtualEntity, providing the following methods:

`$variant->getId()` — Unique identifier of the variant.
`$variant->getSku()` — Specific stock keeping unit.
`$variant->getPrice()` — Individual price for this version.
`$variant->getStock()` — Current availability in the warehouse.
`$variant->getPublished()` — Boolean status of visibility.

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

