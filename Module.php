<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
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
use Shop\Service\ProductImageManagerFactory;
use Shop\Service\CategoryImageManagerFactory;
use Shop\Service\RecentProductManagerFactory;
use Shop\Service\BasketManagerFactory;
use Shop\Service\BasketManager;
use Shop\Service\ProductManagerInterface;
use Shop\Service\ProductManager;
use Shop\Service\CategoryManager;
use Shop\Service\OrderManager;
use Shop\Service\ProductRemover;
use Shop\Service\OrderStatusManager;
use Shop\Service\SiteService;
use Shop\Service\WishlistManager;

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
        $orderInfoMapper = $this->getMapper('/Shop/Storage/MySQL/OrderInfoMapper', false);
        $orderProductMapper = $this->getMapper('/Shop/Storage/MySQL/OrderProductMapper', false);
        $attributeMapper = $this->getMapper('/Shop/Storage/MySQL/ProductAttributeMapper', false);
        $deliveryTypeMapper = $this->getMapper('/Shop/Storage/MySQL/DeliveryTypeMapper', false);
        $couponMapper = $this->getMapper('/Shop/Storage/MySQL/CouponMapper', false);
        $currencyMapper = $this->getMapper('/Shop/Storage/MySQL/CurrencyMapper', false);
        $orderStatusMapper = $this->getMapper('/Shop/Storage/MySQL/OrderStatusMapper', false);
        $wishlistMapper = $this->getMapper('/Shop/Storage/MySQL/WishlistMapper', false);

        // Now build required services
        $productImageManager = $this->getProductImageManager($config->getEntity());
        $webPageManager = $this->getWebPageManager();
        $historyManager = $this->getHistoryManager();

        $basketManager = $this->getBasketManager($config->getEntity(), $productMapper, $productImageManager->getImageBag());
        $basketManager->load();

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

        return array(
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
            'attributeGroupManager' => new AttributeGroupManager($this->getMapper('/Shop/Storage/MySQL/AttributeGroupMapper', false)),
            'attributeValueManager' => new AttributeValueManager($this->getMapper('/Shop/Storage/MySQL/AttributeValueMapper', false))
        );
    }

    /**
     * Returns product image manager
     * 
     * @param \Krystal\Stdlib\VirtualEntity $config
     * @return \Krystal\Image\ImageManager
     */
    private function getProductImageManager(VirtualEntity $config)
    {
        $plugins = array(
            'thumb' => array(
                'dimensions' => array(
                    // In product's page (Administration area)
                    array(200, 200),
                    // Dimensions for a main cover image on site
                    array($config->getCoverWidth(), $config->getCoverHeight()),
                    // In category (and in browser)
                    array($config->getCategoryCoverWidth(), $config->getCategoryCoverHeight()),
                    // Thumbs on site
                    array($config->getThumbWidth(), $config->getThumbHeight()),
                )
            ),
            'original' => array(
                'prefix' => 'original'
            )
        );

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
        $plugins = array(
            'thumb' => array(
                'dimensions' => array(
                    // For the administration panel
                    array(200, 200),
                    // For the site
                    array($config->getCategoryCoverWidth(), $config->getCategoryCoverHeight())
                )
            ),
            'original' => array(
                'prefix' => 'original'
            )
        );

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
     * @param \Shop\Service\ProductManagerInterface $productManager
     * @return \Shop\Service\RecentProduct
     */
    private function getRecentProduct(VirtualEntity $config, ProductManagerInterface $productManager)
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

    /**
     * Returns an instance of basket manager
     * 
     * @param \Krystal\Stdlib\VirtualEntity $config
     * @param \Shop\Storage\ProductMapperInterface $productMapper
     * @param \Krystal\Image\Tool\ImageBagInterface $imageBag
     * @return \Shop\Service\BasketManager
     */
    private function getBasketManager(VirtualEntity $config, $productMapper, ImageBagInterface $imageBag)
    {
        return BasketManagerFactory::build($productMapper, $this->getWebPageManager(), $imageBag, $this->createStorage($config));
    }    
}
