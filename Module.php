<?php

/**
 * This file is part of the Bono CMS
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Shop;

use Krystal\Image\Tool\ImageBagInterface;
use Krystal\Image\Tool\ImageManager;
use Krystal\Stdlib\VirtualEntity;
use Cms\AbstractCmsModule;
use Shop\Service\DeliveryTypeManager;
use Shop\Service\CouponManager;
use Shop\Service\CurrencyManager;
use Shop\Service\AttributeGroupManager;
use Shop\Service\AttributeValueManager;
use Shop\Service\RecentProductManagerFactory;
use Shop\Service\BasketManager;
use Shop\Service\ProductManager;
use Shop\Service\CategoryManager;
use Shop\Service\OrderManager;
use Shop\Service\ProductRemover;
use Shop\Service\OrderStatusManager;
use Shop\Service\SiteService;
use Shop\Service\WishlistManager;
use Shop\Service\SpecificationCategoryService;
use Shop\Service\SpecificationItemService;
use Shop\Service\SpecificationValueService;
use Shop\Service\BrandService;
use Shop\Service\VariantService;
use Krystal\Cart\ShoppingCart;
use Krystal\Cart\SessionAdapter;

final class Module extends AbstractCmsModule
{
    const PARAM_PRODUCTS_IMG_PATH = '/data/uploads/module/shop/products/';
    const PARAM_CATEGORIES_IMG_PATH = '/data/uploads/module/shop/categories/';

    /**
     * {@inheritDoc}
     */
    public function getServiceProviders()
    {
        $config = $this->createConfigService();

        // Build required mappers
        $imageMapper = $this->getMapper('/Shop/Storage/MySQL/ImageMapper', false);
        $productMapper = $this->getMapper('/Shop/Storage/MySQL/ProductMapper');
        $categoryMapper = $this->getMapper('/Shop/Storage/MySQL/CategoryMapper');
        $orderInfoMapper = $this->getMapper('/Shop/Storage/MySQL/OrderInfoMapper');
        $orderProductMapper = $this->getMapper('/Shop/Storage/MySQL/OrderProductMapper');
        $attributeMapper = $this->getMapper('/Shop/Storage/MySQL/ProductAttributeMapper');
        $deliveryTypeMapper = $this->getMapper('/Shop/Storage/MySQL/DeliveryTypeMapper');
        $couponMapper = $this->getMapper('/Shop/Storage/MySQL/CouponMapper', false);
        $currencyMapper = $this->getMapper('/Shop/Storage/MySQL/CurrencyMapper', false);
        $orderStatusMapper = $this->getMapper('/Shop/Storage/MySQL/OrderStatusMapper');
        $wishlistMapper = $this->getMapper('/Shop/Storage/MySQL/WishlistMapper', false);
        $variantMapper = $this->getMapper('/Shop/Storage/MySQL/ProductVariantMapper', false);

        // Now build required services
        $productImageManager = $this->getProductImageManager($config->getEntity());
        $webPageManager = $this->getWebPageManager();
        $historyManager = $this->getHistoryManager();

        $basketManager = new BasketManager($productMapper, $webPageManager, $productImageManager->getImageBag(), new ShoppingCart(new SessionAdapter()));

        $productRemover = new ProductRemover($productMapper, $imageMapper, $webPageManager, $productImageManager);

        // Build category manager
        $categoryManager = new CategoryManager(
            $categoryMapper, 
            $productMapper, 
            $webPageManager, 
            $this->getCategoryImageManager($config->getEntity()), 
            $historyManager
        );

        $productManager = new ProductManager(
            $productMapper, 
            $imageMapper, 
            $categoryMapper, 
            $currencyMapper,
            $attributeMapper,
            $webPageManager, 
            $productImageManager, 
            $historyManager,
            $productRemover
        );

        $deliveryTypeManager = new DeliveryTypeManager($deliveryTypeMapper);
        $couponManager = new CouponManager($couponMapper, $this->getServiceLocator()->get('sessionBag'));
        $currencyManager = new CurrencyManager($currencyMapper);

        $siteService = new SiteService(
            $productManager, 
            $categoryManager, 
            $this->getRecentProduct($config->getEntity(), $productManager), 
            $currencyManager, 
            $wishlistMapper, 
            $config->getEntity()
        );

        return [
            'variantService' => new VariantService($variantMapper),
            'wishlistManager' => new WishlistManager($wishlistMapper, $productManager),
            'siteService' => $siteService,
            'configManager' => $config,
            'deliveryTypeManager' => $deliveryTypeManager,
            'orderStatusManager' => new OrderStatusManager($orderStatusMapper),
            'currencyManager' => $currencyManager,
            'couponManager' => $couponManager,
            'orderManager' => new OrderManager($orderInfoMapper, $orderProductMapper, $basketManager, $webPageManager),
            'basketManager' => $basketManager,
            'productManager' => $productManager,
            'categoryManager' => $categoryManager,
            'attributeGroupManager' => new AttributeGroupManager($this->getMapper('/Shop/Storage/MySQL/AttributeGroupMapper')),
            'attributeValueManager' => new AttributeValueManager($this->getMapper('/Shop/Storage/MySQL/AttributeValueMapper')),
            'specificationCategoryService' => new SpecificationCategoryService($this->getMapper('/Shop/Storage/MySQL/SpecificationCategoryMapper')),
            'specificationItemService' => new SpecificationItemService($this->getMapper('/Shop/Storage/MySQL/SpecificationItemMapper')),
            'specificationValueService' => new SpecificationValueService($this->getMapper('/Shop/Storage/MySQL/SpecificationValueMapper'), $this->getMapper('/Shop/Storage/MySQL/SpecificationCategoryMapper')),
            'brandService' => new BrandService($this->getMapper('/Shop/Storage/MySQL/BrandMapper'))
        ];
    }

    /**
     * Returns product image manager
     * 
     * @param \Krystal\Stdlib\VirtualEntity $config
     * @return \Krystal\Image\ImageManager
     */
    private function getProductImageManager(VirtualEntity $config)
    {
        $plugins = [
            'thumb' => [
                'dimensions' => [
                    // In product's page (Administration area)
                    [200, 200],
                    // Dimensions for a main cover image on site
                    [$config->getCoverWidth(), $config->getCoverHeight()],
                    // In category (and in browser)
                    [$config->getCategoryCoverWidth(), $config->getCategoryCoverHeight()],
                    // Thumbs on site
                    [$config->getThumbWidth(), $config->getThumbHeight()],
                ]
            ],
            'original' => [
                'prefix' => 'original'
            ]
        ];

        return new ImageManager(
            self::PARAM_PRODUCTS_IMG_PATH,
            $this->appConfig->getRootDir(),
            $this->appConfig->getRootUrl(),
            $plugins
        );
    }

    /**
     * Returns category image manager
     * 
     * @param \Krystal\Stdlib\VirtualEntity $config
     * @return \Krystal\Image\ImageManager
     */
    private function getCategoryImageManager(VirtualEntity $config)
    {
        $plugins = [
            'thumb' => [
                'dimensions' => [
                    // For the administration panel
                    [200, 200],
                    // For the site
                    [$config->getCategoryCoverWidth(), $config->getCategoryCoverHeight()]
                ]
            ],
            'original' => [
                'prefix' => 'original'
            ]
        ];

        return new ImageManager(
            self::PARAM_CATEGORIES_IMG_PATH,
            $this->appConfig->getRootDir(),
            $this->appConfig->getRootUrl(),
            $plugins
        );
    }

    /**
     * Returns manager for recent products
     * 
     * @param \Krystal\Stdlib\VirtualEntity $config
     * @param \Shop\Service\ProductManager $productManager
     * @return \Shop\Service\RecentProduct
     */
    private function getRecentProduct(VirtualEntity $config, ProductManager $productManager)
    {
        return RecentProductManagerFactory::build($productManager, $this->createStorage($config), $config);
    }

    /**
     * Returns storage manager
     * 
     * @param \Krystal\Stdlib\VirtualEntity $config
     * @return \Krystal\Http\PersistentStorageInterface
     */
    private function createStorage(VirtualEntity $config)
    {
        if ($config->getBasketStorageType() == 'cookies') {
            return $this->getServiceLocator()->get('request')->getCookieBag();
        } else {
            // Always session storage by default
            return $this->getServiceLocator()->get('sessionBag');
        }
    }
}
