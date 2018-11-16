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
use Krystal\Stdlib\ArrayUtils;

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
               ->setLangId($row['lang_id'], VirtualEntity::FILTER_INT)
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
        return $this->orderStatusMapper->deleteEntity($id);
    }

    /**
     * Adds or updates new order status entry
     * 
     * @param array $input
     * @return boolean
     */
    public function save(array $input)
    {
        return $this->orderStatusMapper->saveEntity($input['orderStatus'], $input['translation']);
    }

    /**
     * Fetch associative list
     * 
     * @return array
     */
    public function fetchList()
    {
        return ArrayUtils::arrayList($this->orderStatusMapper->fetchAll(), 'id', 'name');
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
     * @param boolean $withTranslations Whether to fetch translations or not
     * @return \Krystal\Stdlib\VirtualEntity|boolean
     */
    public function fetchById($id, $withTranslations)
    {
        if ($withTranslations === true) {
            return $this->prepareResults($this->orderStatusMapper->fetchById($id, true));
        } else {
            return $this->prepareResult($this->orderStatusMapper->fetchById($id, false));
        }
    }
}
