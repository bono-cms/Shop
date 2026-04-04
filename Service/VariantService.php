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

use Krystal\Stdlib\VirtualEntity;
use Shop\Storage\ProductVariantMapperInterface;

final class VariantService
{
    /**
     * Variant mapper
     * 
     * @var \Shop\Storage\ProductVariantMapperInterface
     */
    private $variantMapper;

    /**
     * State initialization
     * 
     * @param \Shop\Storage\ProductVariantMapperInterface $variantMapper
     * @return void
     */
    public function __construct(ProductVariantMapperInterface $variantMapper)
    {
        $this->variantMapper = $variantMapper;
    }

    /**
     * Fetches all variants associated with a product ID
     * 
     * @param int $productId
     * @param boolean $published Whether to fetch only published ones
     * @return array
     */
    public function fetchAllByProductId($productId, $published = false)
    {
        return $this->variantMapper->fetchAllByProductId($productId, $published);
    }

    /**
     * Deletes by PKs
     * 
     * @param int $id
     * @return boolean
     */
    public function delete($id)
    {
        return $this->variantMapper->deleteByPk($id);
    }

    /**
     * Saves a variant
     * 
     * @param array $input
     * @return boolean
     */
    public function save(array $input)
    {
        
    }
}
