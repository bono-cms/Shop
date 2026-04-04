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
use Cms\Service\AbstractManager;
use Shop\Storage\ProductVariantMapperInterface;

final class VariantService extends AbstractManager
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
     * {@inheritDoc}
     */
    protected function toEntity(array $row)
    {
        $entity = new VirtualEntity();
        $entity->setId($row['id'])
               ->setProductId($row['product_id'])
               ->setSku($row['sku'])
               ->setPrice($row['price'])
               ->setStock($row['stock'])
               ->setPublished($row['published']);

        return $entity;
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
        $rows = $this->variantMapper->fetchAllByProductId($productId, $published);
        return $this->prepareResults($rows);
    }

    /**
     * Fetches a variant entity by its ID
     *
     * @param int $id Variant ID
     * @return \Krystal\Stdlib\VirtualEntity|boolean
     */    
    public function fetchById($id)
    {
        return $this->prepareResult($this->variantMapper->findByPk($id));
    }

    /**
     * Deletes a variant by its ID
     *
     * @param int $id Variant ID
     * @return boolean
     */
    public function deleteById($id)
    {
        return $this->variantMapper->deleteByPk($id);
    }

    /**
     * Returns last id
     * 
     * @return int
     */
    public function getLastId()
    {
        return $this->variantMapper->getLastId();
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
        return $this->variantMapper->persist($input);
    }
}
