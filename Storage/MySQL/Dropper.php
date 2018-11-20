<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Shop\Storage\MySQL;

use Cms\Storage\MySQL\AbstractStorageDropper;

final class Dropper extends AbstractStorageDropper
{
    /**
     * {@inheritDoc}
     */
    protected function getTables()
    {
        return array(
            CategoryMapper::getTableName(),
            CategoryTranslationMapper::getTableName(),
            CategoryAttributeGroupRelationMapper::getTableName(),
            ImageMapper::getTableName(),
            OrderInfoMapper::getTableName(),
            OrderProductMapper::getTableName(),
            ProductMapper::getTableName(),
            ProductTranslationMapper::getTableName(),
            ProductCategoryRelationMapper::getTableName(),
            AttributeGroupMapper::getTableName(),
            AttributeValueMapper::getTableName(),
            ProductAttributeMapper::getTableName(),
            ProductSimilarRelationMapper::getTableName(),
            ProductRecommendedMapper::getTableName(),
            DeliveryTypeMapper::getTableName(),
            CouponMapper::getTableName(),
            CurrencyMapper::getTableName(),
            OrderStatusMapper::getTableName(),
            OrderStatusTranslationMapper::getTableName(),
            WishlistMapper::getTableName(),
            SpecificationCategoryMapper::getTableName(),
            SpecificationCategoryTranslationMapper::getTableName(),
            SpecificationItemMapper::getTableName(),
            SpecificationItemTranslationMapper::getTableName(),
            SpecificationCategoryProductRelationMapper::getTableName(),
            SpecificationValueMapper::getTableName(),
            SpecificationValueTranslationMapper::getTableName(),
            BrandMapper::getTableName()
        );
    }
}
