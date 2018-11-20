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

use Shop\Storage\BrandMapperInterface;
use Cms\Service\AbstractManager;

final class BrandService extends AbstractManager
{
    /**
     * Any compliant brand mapper
     * 
     * @var \Shop\Storage\BrandMapperInterface
     */
    private $brandMapper;

    /**
     * State initialization
     * 
     * @param \Shop\Storage\BrandMapperInterface $brandMapper
     * @return void
     */
    public function __construct(BrandMapperInterface $brandMapper)
    {
        $this->brandMapper = $brandMapper;
    }

    /**
     * Returns last ID
     * 
     * @return integer
     */
    public function getLastId()
    {
        return $this->brandMapper->getMaxId();
    }
}
