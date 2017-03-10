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
use Shop\Storage\DeliveryTypeMapperInterface;
use Krystal\Stdlib\VirtualEntity;

final class DeliveryTypeManager extends AbstractManager implements DeliveryTypeManagerInterface
{
    /**
     * Delivery type mapper
     * 
     * @var \Shop\Storage\DeliveryTypeMapperInterface
     */
    private $deliveryTypeMapper;

    /**
     * State initialization
     * 
     * @param \Shop\Storage\DeliveryTypeMapperInterface $deliveryTypeMapper
     * @return void
     */
    public function __construct(DeliveryTypeMapperInterface $deliveryTypeMapper)
    {
        $this->deliveryTypeMapper = $deliveryTypeMapper;
    }

    /**
     * {@inheritDoc}
     */
    protected function toEntity(array $row)
    {
        $entity = new VirtualEntity();
        $entity->setId($row['id'], VirtualEntity::FILTER_INT)
               ->setName($row['name'], VirtualEntity::FILTER_TAGS)
               ->setPrice($row['price'], VirtualEntity::FILTER_FLOAT);

        return $entity;
    }

    /**
     * Fetches delivery type name by its associated ID
     * 
     * @param string $id
     * @return string
     */
    public function fetchNameById($id)
    {
        return $this->deliveryTypeMapper->fetchNameById($id);
    }

    /**
     * Updates the delivery type
     * 
     * @param array $input
     * @return boolean
     */
    public function update(array $input)
    {
        return $this->deliveryTypeMapper->persist($input);
    }

    /**
     * Adds new delivery type
     * 
     * @param array $input
     * @return boolean
     */
    public function add(array $input)
    {
        return $this->deliveryTypeMapper->persist($input);
    }

    /**
     * Returns last delivery type ID
     * 
     * @return string
     */
    public function getLastId()
    {
        return $this->deliveryTypeMapper->getLastId();
    }

    /**
     * Delete delivery type by its associated ID
     * 
     * @param string $id
     * @return boolean
     */
    public function deleteById($id)
    {
        return $this->deliveryTypeMapper->deleteById($id);
    }

    /**
     * Fetches entity by its associated ID
     * 
     * @param string $id
     * @return \Krystal\Stdlib\VirtualEntity
     */
    public function fetchById($id)
    {
        return $this->prepareResult($this->deliveryTypeMapper->fetchById($id));
    }

    /**
     * Fetch all entities
     * 
     * @return array
     */
    public function fetchAll()
    {
        return $this->prepareResults($this->deliveryTypeMapper->fetchAll());
    }
}
