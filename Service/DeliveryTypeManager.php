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
               ->setLangId($row['lang_id'], VirtualEntity::FILTER_INT)
               ->setName($row['name'], VirtualEntity::FILTER_TAGS)
               ->setPrice($row['price'], VirtualEntity::FILTER_FLOAT);

        return $entity;
    }

    /**
     * Creates delivery status (name +price)
     * 
     * @param string $id Delivery ID
     * @return string
     */
    public function createDeliveryStatus($id)
    {
        $delivery = $this->fetchById($id);

        if ($delivery !== false) {
            if ($delivery->getPrice() == 0) {
                return $delivery->getName();
            } else {
                return sprintf('%s (+%s)', $delivery->getName(), $delivery->getPrice());
            }
        } else {
            return null;
        }
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
     * Updates or inserts delivery type
     * 
     * @param array $input
     * @return boolean
     */
    public function save(array $input)
    {
        return $this->deliveryTypeMapper->saveEntity($input['deliveryType'], $input['translation']);
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
        return $this->deliveryTypeMapper->deleteEntity($id);
    }

    /**
     * Fetches entity by its associated ID
     * 
     * @param string $id
     * @param boolean $withTranslations Whether to fetch translations or not
     * @return \Krystal\Stdlib\VirtualEntity|array
     */
    public function fetchById($id, $withTranslations)
    {
        if ($withTranslations === true) {
            return $this->prepareResults($this->deliveryTypeMapper->fetchById($id, true));
        } else {
            return $this->prepareResult($this->deliveryTypeMapper->fetchById($id, false));
        }
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
