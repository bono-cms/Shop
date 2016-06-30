CHANGELOG
=========

1.4
---

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