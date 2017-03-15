CHANGELOG
=========

1.4
---

 * Ability to return formatted prices from product entities
 * Added `countAllStokes()` and `getAllStokes()` in `\Shop\Service\SiteService`
 * Multi-currencies support. Added `getConvertedPrice()` and `getConvertedStokePrice()` in product entity
 * Added optional `data-basket-button-disabled-class` attribute that can disable the "Add to basket" button by adding provided class
 * Added `BasketManager::has()` and its linked `BasketEntity::hasProduct()` method
 * Added optional `$limit` parameter in `BasketManager::getProducts()` to limit the result-set
 * Added discount coupons
 * Custom delivery methods support
 * Added page detection methods in entities - `getCategoryPage()`, `getBasketPage()`, `getProductPage()`
 * Added dedicated search by products
 * Added support for dynamic attributes
 * Support for similar and recommended products
 * Added "Quick View" support
 * Added `getTopCategories()` that returns top category entities in `\Shop\Service\SiteService`
 * Replacement text inputs with textarea for `keywords` attribute
 * Fixed issue with quote escaping
 * Fixed issue with product synchronizations (in basket and recent block)
 * Support for in stock QTY count. Added `isAvailable()` and `getInStock()` methods in product entity
 * In site service, added `getCategoryChildrenByParentId()`
 * Fixed issue with user-defined pagination
 * Removed extra `fetchAllByIdAndPage()` and `fetchAllPublishedByIdAndPage()` methods in `CategoryMapper`
 * Fixed sorting issue in `CategoryMapper::fetchChildrenByParentId()`
 * In site service, added `renderCategoryTree()` and `renderCategoryDropdown()` to dynamically render menus
 * In `getLatest()` added optional category id filter `\Shop\Service\SiteService`
 * Fixed issue with `getProductsWithMaxViewCount()` in `\Shop\Service\SiteService`
 * Ability to attach several categories for one single product
 * Removed `table-striped` class from main table
 * Added `getSalePercentage()` in `\Shop\Service\ProductEntity`
 * Improved the looks of "Successfully added to the basket" modal dialog
 * Added email field in the order form
 * Added ability to fetch best seller products
 * Added option to disable or enable the basket
 * Changed the way of storing configuration data. Since now its stored in the database
 * Added notification count of new orders on the grid's page
 * Added `name` attribute
 * Added `getImageUrl()` entities in product entity object. It basically acts as a shortcut
 * Fixed minor issue with basket's breadcrumb
 * Merged `fetchAllByPage()` with `fetchAllByCategoryIdAndPage()`
 * Added support for table prefix
 * Switched back to two columns view
 * Updated module icon
 * Added ability to remove several orders at once
 * Fixed issue with appearing breadcrumb on filtering
 * Added breadcrumbs in order list. Removed "Back" button
 * Added extra "Go home" item to return back when viewing particular category id
 * Minor improvements in internals

1.3
---

 * Fixed issue with date sorting when viewing categories
 * Improved internal code-base

1.2
---

 * Set cookie lifetime for recent products to 631139040 seconds
 * Added support for starting prices in category. This functionality is implemented in `SiteService::getMinCategoryPriceCount`
 * Implemented support for "Mostly Viewed Products". Added `getProductsWithMaxViewCount()` to `SiteService` 
 * Added support for views count
 * Minor improvements in code base
 * Added support for permanent URLs. Now each product's entity has `getPermanentUrl()` method
 * Added `SiteService`, which is called `$shop`. Moved all related functionality there as well
 * Added filters to grid view
 * Removed a block of latest orders from grid view in administration panel
 * Added image zoom to product images (Thanks to light-box JS-plugin)


1.1
---

 * Added currency support
 * Added stoke price support
 * Improved cart's logic
 * Add "recently viewed products" functionality, which is optional


1.0
---

 * First public version