<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Shop\Service;

use Shop\Storage\SpecificationCategoryMapperInterface;
use Cms\Service\AbstractManager;

final class SpecificationCategoryService extends AbstractManager
{
    /**
     * Any compliant specification category mapper
     * 
     * @var \Shop\Storage\SpecificationCategoryMapperInterface
     */
    private $specificationCategoryMapper;

    /**
     * State initialization
     * 
     * @param \Shop\Storage\SpecificationCategoryMapperInterface $specificationCategoryMapper
     * @return void
     */
    public function __construct(SpecificationCategoryMapperInterface $specificationCategoryMapper)
    {
        $this->specificationCategoryMapper = $specificationCategoryMapper;
    }

    /**
     * Returns last id
     * 
     * @return integer
     */
    public function getLastId()
    {
        return $this->specificationCategoryMapper->getLastId();
    }
}
