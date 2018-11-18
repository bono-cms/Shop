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

use Krystal\Stdlib\ArrayUtils;
use Krystal\Stdlib\VirtualEntity;
use Cms\Service\AbstractManager;
use Shop\Storage\SpecificationValueMapperInterface;

final class SpecificationValueService extends AbstractManager
{
    /**
     * Any compliant mapper
     * 
     * @var \Shop\Storage\SpecificationValueMapperInterface
     */
    private $specificationValueMapper;

    /**
     * State initialization
     * 
     * @param \Shop\Storage\SpecificationValueMapperInterface $specificationValueMapper
     * @return void
     */
    public function __construct(SpecificationValueMapperInterface $specificationValueMapper)
    {
        $this->specificationValueMapper = $specificationValueMapper;
    }

    /**
     * Find values by product ID
     * 
     * @param int $id Product ID
     * @return array
     */
    public function findByProduct($id)
    {
        return $this->specificationValueMapper->findByProduct($id);
    }
}
