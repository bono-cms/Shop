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

use Cms\Storage\MySQL\AbstractMapper;
use Shop\Storage\SpecificationValueMapperInterface;

final class SpecificationValueMapper extends AbstractMapper implements SpecificationValueMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_shop_specification_values');
    }

    /**
     * {@inheritDoc}
     */
    public static function getTranslationTable()
    {
        return SpecificationValueTranslationMapper::getTableName();
    }
}
