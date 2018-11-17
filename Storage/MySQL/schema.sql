

DROP TABLE IF EXISTS `bono_module_shop_orders_info`;
CREATE TABLE `bono_module_shop_orders_info` (
	
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'Unique order id',
    `order_status_id` INT COMMENT 'Order Status ID',
    `customer_id` INT COMMENT 'Optional customer ID',
	`date` DATE NOT NULL,
	`name` varchar(255) NOT NULL COMMENT 'Name of customer',
	`email` varchar(255) NOT NULL COMMENT 'Customer email',
	`phone` varchar(254) NOT NULL COMMENT 'Phone of customer',
	`address` TEXT NOT NULL COMMENT 'Destination address',
	`comment` LONGTEXT NOT NULL COMMENT 'Customer comment',
	`delivery` TEXT NOT NULL COMMENT 'Delivery type',
	`qty` int NOT NULL COMMENT 'Ammount of products',
	`total_price` FLOAT COMMENT 'Total price',
    `discount` FLOAT COMMENT 'Discount price if applied',
	`approved` varchar(1) NOT NULL COMMENT 'Whether this order is approved'
	
) DEFAULT CHARSET = UTF8;



DROP TABLE IF EXISTS `bono_module_shop_orders_products`;
CREATE TABLE `bono_module_shop_orders_products` (
    
	`order_id` INT NOT NULL,
	`product_id` INT NOT NULL COMMENT 'Product id',
	`name` varchar(255) NOT NULL COMMENT 'Product name',
	`price` float NOT NULL COMMENT 'Product price',
	`sub_total_price` float NOT NULL COMMENT 'Sub-total price',
	`qty` INT NOT NULL COMMENT 'Amount of ordered products',
    `attributes` TEXT COMMENT 'JSON string representing a collection of Group ID => Value ID'
    
) DEFAULT CHARSET = UTF8;


DROP TABLE IF EXISTS `bono_module_shop_categories`;
CREATE TABLE `bono_module_shop_categories` (

    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `parent_id` INT NOT NULL COMMENT 'Parent category id this category id refers to',
    `order` INT NOT NULL COMMENT 'Sort order for this category',
    `seo` varchar(1) NOT NULL COMMENT 'Whether SEO enabled or not',
    `cover` varchar(254) NOT NULL COMMENT 'Cover image base name'

) DEFAULT CHARSET = UTF8;


DROP TABLE IF EXISTS `bono_module_shop_categories_translations`;
CREATE TABLE `bono_module_shop_categories_translations` (
    `id` INT NOT NULL,
	`lang_id` INT NOT NULL,
	`web_page_id` INT NOT NULL COMMENT 'Sluggable web page id this category refers to',
	`title` varchar(255) NOT NULL COMMENT 'Title of the category',
	`name` varchar(255) NOT NULL COMMENT 'Name of the category',
	`description` LONGTEXT NOT NULL COMMENT 'Full description of this category',
	`keywords` TEXT NOT NULL COMMENT 'Keywords for search engines',
	`meta_description` TEXT COMMENT 'Meta description for search engines',

) DEFAULT CHARSET = UTF8;


DROP TABLE IF EXISTS `bono_module_shop_categories_attr_groups`;
CREATE TABLE `bono_module_shop_categories_attr_groups` (

    `master_id` INT NOT NULL COMMENT 'Category ID',
    `slave_id` INT NOT NULL COMMENT 'Attribute group ID'

) DEFAULT CHARSET = UTF8;


DROP TABLE IF EXISTS `bono_module_shop_product_images`;
CREATE TABLE `bono_module_shop_product_images` (
	`id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'Image id',
	`product_id` INT NOT NULL,
	`image` varchar(254) NOT NULL COMMENT 'Image base name on file-system',
	`order` INT NOT NULL COMMENT 'Sort order',
	`published` varchar(1) NOT NULL COMMENT 'Whether this image is visible'
	
) DEFAULT CHARSET = UTF8;


DROP TABLE IF EXISTS `bono_module_shop_products`;
CREATE TABLE `bono_module_shop_products` (

    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `regular_price` FLOAT NOT NULL COMMENT 'Regular price of this product',
    `stoke_price` FLOAT NOT NULL COMMENT 'Whether this product is considered as a special offer',
    `in_stock` INT NOT NULL COMMENT 'Quantity of the product in stoke',
    `special_offer` varchar(1) NOT NULL COMMENT 'Whether this product is considered as a special offer',
    `published` varchar(1) NOT NULL COMMENT 'Whether this product should be visible on site',
    `order` INT NOT NULL COMMENT 'Sort order of this product',
    `seo` varchar(1) NOT NULL COMMENT 'Whether SEO tool is enabled or not',
    `cover` varchar(254) NOT NULL COMMENT 'Basename of image file',
    `date` DATE NOT NULL COMMENT 'Date when added',
    `views` INT NOT NULL COMMENT 'View couter'

) DEFAULT CHARSET = UTF8;


DROP TABLE IF EXISTS `bono_module_shop_products_translations`;
CREATE TABLE `bono_module_shop_products_translations` (

    `id` INT NOT NULL,
    `lang_id` INT NOT NULL,
    `web_page_id` INT NOT NULL COMMENT 'Web page id this product refers to',
	`title` varchar(255) NOT NULL COMMENT 'Title of the product',
	`name` varchar(255) NOT NULL COMMENT 'Name of the product',
	`description` LONGTEXT NOT NULL COMMENT 'Full description` of this product',
	`keywords` TEXT NOT NULL COMMENT 'Keywords for search engines',
    `meta_description` TEXT NOT NULL COMMENT 'Meta-description for search engines'
);


DROP TABLE IF EXISTS `bono_module_shop_product_category_relations`;
CREATE TABLE `bono_module_shop_product_category_relations` (
    `master_id` INT NOT NULL COMMENT 'Product ID',
    `slave_id` INT NOT NULL COMMENT 'Category ID'
);


/* Attributes */
DROP TABLE IF EXISTS `bono_module_shop_attribute_groups`;
CREATE TABLE `bono_module_shop_attribute_groups` (

    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `name` varchar(255) NOT NULL COMMENT 'Group name',
    `dynamic` varchar(1) NOT NULL COMMENT 'Whether the group contains dynamic attributes only'

) DEFAULT CHARSET = UTF8;


DROP TABLE IF EXISTS `bono_module_shop_attribute_values`;
CREATE TABLE `bono_module_shop_attribute_values` (

    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `group_id` INT NOT NULL,
    `name` varchar(255) NOT NULL

) DEFAULT CHARSET = UTF8;


DROP TABLE IF EXISTS `bono_module_shop_product_attributes`;
CREATE TABLE `bono_module_shop_product_attributes` (
    `product_id` INT NOT NULL COMMENT 'Product ID',
    `group_id` INT NOT NULL COMMENT 'Group ID',
    `value_id` INT NOT NULL COMMENT 'Value ID'
);


DROP TABLE IF EXISTS `bono_module_shop_product_recommended`;
CREATE TABLE `bono_module_shop_product_recommended` (
    `master_id` INT NOT NULL COMMENT 'Target product ID',
    `slave_id` INT NOT NULL COMMENT 'Attached product ID'
) DEFAULT CHARSET = UTF8;


DROP TABLE IF EXISTS `bono_module_shop_product_similar`;
CREATE TABLE `bono_module_shop_product_similar` (
    `master_id` INT NOT NULL COMMENT 'Target product ID',
    `slave_id` INT NOT NULL COMMENT 'Attached product ID'
) DEFAULT CHARSET = UTF8;


DROP TABLE IF EXISTS `bono_module_shop_delivery_types`;
CREATE TABLE `bono_module_shop_delivery_types` (
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'Delivery type ID',
    `name` varchar(255) NOT NULL COMMENT 'Delivery type name',
    `price` FLOAT NOT NULL COMMENT 'The price for'
) DEFAULT CHARSET = UTF8;


DROP TABLE IF EXISTS `bono_module_shop_coupons`;
CREATE TABLE `bono_module_shop_coupons` (
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'Coupon ID',
    `code` varchar(30) NOT NULL COMMENT 'Coupon code',
    `percentage` FLOAT NOT NULL COMMENT 'Discount percentage'
) DEFAULT CHARSET = UTF8;


DROP TABLE IF EXISTS `bono_module_shop_currencies`;
CREATE TABLE `bono_module_shop_currencies` (
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'Currency ID',
    `code` varchar(30) NOT NULL COMMENT 'Unique currency code',
    `value` FLOAT NOT NULL COMMENT 'Currency value'
) DEFAULT CHARSET = UTF8;


DROP TABLE IF EXISTS `bono_module_shop_orders_status`;
CREATE TABLE `bono_module_shop_orders_status` (
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'Order Status ID'
) DEFAULT CHARSET = UTF8;

DROP TABLE IF EXISTS `bono_module_shop_orders_status_translations`;
CREATE TABLE `bono_module_shop_orders_status_translations` (

    `id` INT NOT NULL COMMENT 'Order Status ID',
    `lang_id` INT NOT NULL COMMENT 'Attached language ID',
    `name` varchar(255) NOT NULL COMMENT 'Order Status Name',
    `description` TEXT COMMENT 'Order Status Description',

    FOREIGN KEY (id) REFERENCES bono_module_shop_orders_status(id) ON DELETE CASCADE

) DEFAULT CHARSET = UTF8;

DROP TABLE IF EXISTS `bono_module_shop_wishlist`;
CREATE TABLE `bono_module_shop_wishlist` (

    `wishlist_item_id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `customer_id` INT NOT NULL COMMENT 'Customer ID',
    `product_id` INT NOT NULL COMMENT 'Product ID'

) DEFAULT CHARSET = UTF8;

DROP TABLE IF EXISTS `bono_module_shop_specification_category`;
CREATE TABLE `bono_module_shop_specification_category` (
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `order` INT NOT NULL COMMENT 'Sorting order'
) DEFAULT CHARSET = UTF8;

DROP TABLE IF EXISTS `bono_module_shop_specification_category_translation`;
CREATE TABLE `bono_module_shop_specification_category_translation` (
    `id` INT NOT NULL,
    `lang_id` INT NOT NULL COMMENT 'Attached language ID',
    `name` varchar(255) NOT NULL COMMENT 'Category name',

    FOREIGN KEY (id) REFERENCES bono_module_shop_specification_category(id) ON DELETE CASCADE

) DEFAULT CHARSET = UTF8;

DROP TABLE IF EXISTS `bono_module_shop_specification_item`;
CREATE TABLE `bono_module_shop_specification_item` (
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `category_id` INT NOT NULL COMMENT 'Attached category ID',
    `order` INT NOT NULL COMMENT 'Sorting order',

    FOREIGN KEY (category_id) REFERENCES bono_module_shop_specification_category(id) ON DELETE CASCADE

) DEFAULT CHARSET = UTF8;

DROP TABLE IF EXISTS `bono_module_shop_specification_item_translation`;
CREATE TABLE `bono_module_shop_specification_item_translation` (
    `id` INT NOT NULL,
    `lang_id` INT NOT NULL COMMENT 'Attached language ID',
    `name` varchar(255) NOT NULL COMMENT 'Category name',

    FOREIGN KEY (id) REFERENCES bono_module_shop_specification_item(id) ON DELETE CASCADE

) DEFAULT CHARSET = UTF8;

/* Attached specification categories to products (i.e their ID relations) */
DROP TABLE IF EXISTS `bono_module_shop_specification_relation`;
CREATE TABLE `bono_module_shop_specification_relation` (
    `master_id` INT NOT NULL COMMENT 'Product ID',
    `slave_id` INT NOT NULL COMMENT 'Category ID',

    FOREIGN KEY (slave_id) REFERENCES bono_module_shop_specification_category(id) ON DELETE CASCADE

) DEFAULT CHARSET = UTF8;
