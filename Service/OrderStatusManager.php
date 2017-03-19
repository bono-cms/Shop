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

use Shop\Storage\OrderStatusMapperInterface;
use Cms\Service\AbstractManager;
use Krystal\Stdlib\VirtualEntity;

final class OrderStatusManager extends AbstractManager implements OrderStatusManagerInterface
{
    /**
     * Any compliant order status mapper
     * 
     * @var \Shop\Storage\OrderStatusMapperInterface
     */
    private $orderStatusMapper;

    /**
     * State initialization
     * 
     * @param \Shop\Storage\OrderStatusMapperInterface $orderStatusMapper
     * @return void
     */
    public function __construct(OrderStatusMapperInterface $orderStatusMapper)
    {
        $this->orderStatusMapper = $orderStatusMapper;
    }

    /**
     * {@inheritDoc}
     */
    protected function toEntity(array $row)
    {
        $entity = new VirtualEntity();
        $entity->setId($row['id'], VirtualEntity::FILTER_INT)
               ->setName($row['name'])
               ->setDescription($row['description']);

        return $entity;
    }

    /**
     * Returns last ID
     * 
     * @return string
     */
    public function getLastId()
    {
        return $this->orderStatusMapper->getLastId();
    }

    /**
     * Deletes a order status by its associated ID
     * 
     * @param string $id
     * @return boolean
     */
    public function deleteById($id)
    {
        return $this->orderStatusMapper->deleteById($id);
    }

    /**
     * Adds new order status entry
     * 
     * @param array $input
     * @return boolean
     */
    public function add(array $input)
    {
        return $this->orderStatusMapper->persist($input);
    }

    /**
     * Updates order status entry
     * 
     * @param array $input
     * @return boolean
     */
    public function update(array $input)
    {
        return $this->orderStatusMapper->persist($input);
    }

    /**
     * Fetch all entities
     * 
     * @return array
     */
    public function fetchAll()
    {
        return $this->prepareResults($this->orderStatusMapper->fetchAll());
    }

    /**
     * Fetches order status entity by its associated ID
     * 
     * @param string $id
     * @return \Krystal\Stdlib\VirtualEntity|boolean
     */
    public function fetchById($id)
    {
        return $this->prepareResult($this->orderStatusMapper->fetchById($id));
    }
}
