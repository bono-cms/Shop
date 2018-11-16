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

use Cms\Service\AbstractManager;
use Shop\Storage\SpecificationItemMapperInterface;

final class SpecificationItemService extends AbstractManager
{
    /**
     * Any compliant specification item mapper
     * 
     * @var \Shop\Storage\SpecificationItemMapperInterface
     */
    private $specificationItemMapper;

    /**
     * State initialization
     * 
     * @param \Shop\Storage\SpecificationItemMapperInterface $specificationItemMapper
     * @return void
     */
    public function __construct(SpecificationItemMapperInterface $specificationItemMapper)
    {
        $this->specificationItemMapper = $specificationItemMapper;
    }

    /**
     * Returns last ID
     * 
     * @return int
     */
    public function getLastId()
    {
        return $this->specificationItemMapper->getMaxId();
    }
}
