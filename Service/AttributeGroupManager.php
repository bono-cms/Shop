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

use Shop\Storage\AttributeGroupMapperInterface;
use Krystal\Stdlib\VirtualEntity;
use Krystal\Stdlib\ArrayUtils;
use Cms\Service\AbstractManager;

final class AttributeGroupManager extends AbstractManager
{
    /**
     * Any compliant attribute group mapper
     * 
     * @var \Shop\Storage\AttributeGroupMapperInterface
     */
    private $attributeGroupMapper;

    /**
     * State initialization
     * 
     * @param \Shop\Storage\AttributeGroupMapperInterface $attributeGroupMapper
     * @return void
     */
    public function __construct(AttributeGroupMapperInterface $attributeGroupMapper)
    {
        $this->attributeGroupMapper = $attributeGroupMapper;
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
               ->setDynamic($row['dynamic'], VirtualEntity::FILTER_BOOL);

        return $entity;
    }

    /**
     * Fetches groups as a list
     * 
     * @return array
     */
    public function fetchList()
    {
        return ArrayUtils::arrayList($this->attributeGroupMapper->fetchAll(), 'id', 'name');
    }

    /**
     * Fetch all group entities
     * 
     * @return array
     */
    public function fetchAll()
    {
        return $this->prepareResults($this->attributeGroupMapper->fetchAll());
    }

    /**
     * Returns last id
     * 
     * @return string
     */
    public function getLastId()
    {
        return $this->attributeGroupMapper->getLastId();
    }

    /**
     * Saves a group
     * 
     * @param array $input
     * @return boolean
     */
    public function save(array $input)
    {
        return $this->attributeGroupMapper->saveEntity($input['group'], $input['translation']);
    }

    /**
     * Deletes attribute group by its associated id
     * 
     * @param string $id Value id
     * @return boolean
     */
    public function deleteById($id)
    {
        return $this->attributeGroupMapper->deleteEntity($id);
    }

    /**
     * Fetches group by its associated id
     * 
     * @param string $id
     * @param boolean $withTranslation Whether to fetch translations or not
     * @return array
     */
    public function fetchById($id, $withTranslation)
    {
        if ($withTranslation === true) {
            return $this->prepareResults($this->attributeGroupMapper->fetchById($id, true));
        } else {
            return $this->prepareResult($this->attributeGroupMapper->fetchById($id, false));
        }
    }
}
