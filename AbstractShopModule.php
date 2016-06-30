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
use Shop\Service\ProductImageManagerFactory;
use Shop\Service\CategoryImageManagerFactory;
use Shop\Service\RecentProductManagerFactory;
use Shop\Service\BasketManagerFactory;
use Shop\Service\BasketManager;
use Shop\Service\ProductManagerInterface;

abstract class AbstractShopModule extends AbstractCmsModule
{
    /**
     * Returns product image manager
     * 
     * @param \Krystal\Stdlib\VirtualEntity $config
     * @return \Krystal\Image\ImageManager
     */
    final protected function getProductImageManager(VirtualEntity $config)
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
            '/data/uploads/module/shop/products/',
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
    final protected function getCategoryImageManager(VirtualEntity $config)
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
            '/data/uploads/module/shop/categories/',
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
    final protected function getRecentProduct(VirtualEntity $config, ProductManagerInterface $productManager)
    {
        return RecentProductManagerFactory::build($productManager, $this->createStorage($config), $config);
    }

    /**
     * Returns storage manager
     * 
     * @param \Krystal\Stdlib\VirtualEntity $config
     * @return \Krystal\Http\PersistentStorageInterface
     */
    final protected function createStorage(VirtualEntity $config)
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
    final protected function getBasketManager(VirtualEntity $config, $productMapper, ImageBagInterface $imageBag)
    {
        return BasketManagerFactory::build($productMapper, $this->getWebPageManager(), $imageBag, $this->createStorage($config));
    }
}
