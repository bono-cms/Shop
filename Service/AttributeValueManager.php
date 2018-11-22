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

use Shop\Storage\AttributeValueMapperInterface;
use Krystal\Stdlib\VirtualEntity;
use Krystal\Stdlib\ArrayUtils;
use Cms\Service\AbstractManager;

final class AttributeValueManager extends AbstractManager
{
    /**
     * Any compliant attribute group mapper
     * 
     * @var \Shop\Storage\AttributeValueMapperInterface
     */
    private $attributeValueMapper;

    /**
     * State initialization
     * 
     * @param \Shop\Storage\AttributeValueMapperInterface $attributeGroupMapper
     * @return void
     */
    public function __construct(AttributeValueMapperInterface $attributeValueMapper)
    {
        $this->attributeValueMapper = $attributeValueMapper;
    }

    /**
     * {@inheritDoc}
     */
    protected function toEntity(array $row)
    {
        $entity = new VirtualEntity();
        $entity->setId($row['id'], VirtualEntity::FILTER_INT)
               ->setLangId($row['lang_id'], VirtualEntity::FILTER_INT)
               ->setGroupId($row['group_id'], VirtualEntity::FILTER_INT)
               ->setName($row['name'], VirtualEntity::FILTER_TAGS);

        return $entity;
    }

    /**
     * Fetch all value entities
     * 
     * @param string $id Category id
     * @return array
     */
    public function fetchAllByCategoryId($id)
    {
        return $this->prepareResults($this->attributeValueMapper->fetchAllByCategoryId($id));
    }

    /**
     * Returns last id
     * 
     * @return string
     */
    public function getLastId()
    {
        return $this->attributeValueMapper->getLastId();
    }

    /**
     * Updates a value
     * 
     * @param array $input
     * @return boolean
     */
    public function save(array $input)
    {
        return $this->attributeValueMapper->saveEntity($input['value'], $input['translation']);
    }

    /**
     * Deletes attribute value by its associated id
     * 
     * @param string $id Value id
     * @return boolean
     */
    public function deleteById($id)
    {
        return $this->attributeValueMapper->deleteEntity($id);
    }

    /**
     * Fetches value by its associated id
     * 
     * @param string $id
     * @param boolean $withTranslations Whether to fetch translations or not
     * @return array
     */
    public function fetchById($id, $withTranslations)
    {
        if ($withTranslations) {
            return $this->prepareResults($this->attributeValueMapper->fetchById($id, true));
        } else {
            return $this->prepareResult($this->attributeValueMapper->fetchById($id, false));
        }
    }
}
