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

use Shop\Storage\SpecificationCategoryMapperInterface;
use Cms\Service\AbstractManager;
use Krystal\Stdlib\VirtualEntity;
use Krystal\Stdlib\ArrayUtils;

final class SpecificationCategoryService extends AbstractManager
{
    /**
     * Any compliant specification category mapper
     * 
     * @var \Shop\Storage\SpecificationCategoryMapperInterface
     */
    private $specificationCategoryMapper;

    /**
     * State initialization
     * 
     * @param \Shop\Storage\SpecificationCategoryMapperInterface $specificationCategoryMapper
     * @return void
     */
    public function __construct(SpecificationCategoryMapperInterface $specificationCategoryMapper)
    {
        $this->specificationCategoryMapper = $specificationCategoryMapper;
    }

    /**
     * {@inheritDoc}
     */
    protected function toEntity(array $row)
    {
        $entity = new VirtualEntity();
        $entity->setId($row['id'])
               ->setLangId($row['lang_id'])
               ->setName($row['name'])
               ->setOrder($row['order']);

        if (isset($row['item_count'])) {
            $entity->setItemCount($row['item_count']);
        }

        return $entity;
    }

    /**
     * Deletes a category by its ID
     * 
     * @param int $id
     * @return boolean
     */
    public function deleteById($id)
    {
        return $this->specificationCategoryMapper->deleteEntity($id);
    }

    /**
     * Fetch category by its ID
     * 
     * @param int $id
     * @param boolean $withTranslations
     * @return array
     */
    public function fetchById($id, $withTranslations)
    {
        if ($withTranslations == true) {
            return $this->prepareResults($this->specificationCategoryMapper->fetchById($id, true));
        } else {
            return $this->prepareResult($this->specificationCategoryMapper->fetchById($id, false));
        }
    }

    /**
     * Fetches as a list
     * 
     * @return array
     */
    public function fetchList()
    {
        return ArrayUtils::arrayList($this->specificationCategoryMapper->fetchAll(), 'id', 'name');
    }

    /**
     * Fetch all categories
     * 
     * @return array
     */
    public function fetchAll()
    {
        return $this->specificationCategoryMapper->fetchAll();
    }

    /**
     * Fetch attached specification category IDs by product ID
     * 
     * @param int $id Product ID
     * @return array
     */
    public function fetchAttachedByProductId($id)
    {
        return $this->specificationCategoryMapper->fetchAttachedByProductId($id);
    }

    /**
     * Returns last id
     * 
     * @return integer
     */
    public function getLastId()
    {
        return $this->specificationCategoryMapper->getLastId();
    }

    /**
     * Saves a category
     * 
     * @param array $input
     * @return mixed
     */
    public function save(array $input)
    {
        return $this->specificationCategoryMapper->saveEntity($input['category'], $input['translation']);
    }
}
